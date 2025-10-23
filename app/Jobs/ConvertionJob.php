<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Storage;
use App\Models\UserFile;
use Illuminate\Support\Facades\Log;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ConvertionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $original_name;
    protected $path;
    protected $delimiter;
    protected $name;
    protected $typeOfConversation;
    protected $fileMetaData;


    /**
     * Create a new job instance.
     */
    public function __construct(UserFile $fileMetaData, string $original_name, string $path, string $type, string $delimiter = ',')
    {
        $this->fileMetaData = $fileMetaData;
        $this->original_name = $original_name;
        $this->path = $path;
        $this->typeOfConversation = $type;
        $this->delimiter = $delimiter;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->typeOfConversation) {
            case 'xlsxToXls':
                Log::info('Function xlsxToXls was started successfully');
                $this->xlsxToXls();
                break;
            case 'xlsToXlsx':
                Log::info('Function xlsToXlsx was started successfully');
                $this->xlsToXlsx();
                break;
            case 'excelToOds':
                Log::info('Function excelToOds was started successfully');
                $this->excelToOds();
                break;
            case 'excelToCsv':
                Log::info('Function excelToCsv was started successfully');
                $this->excelToCsv();
                break;
            case 'ExcelToHtml':
                Log::info('Function ExcelToHtml was started successfully');
                $this->ExcelToHtml();
                break;
            case 'split':
                Log::info('Function split was started successfully');
                $this->split();
                break;
            case 'merge':
                Log::info('Function merge was started successfully');
                $this->merge();
                break;

            default:
                # code...
                break;
        }
    }

    private function merge(): void
    {

    }


    private function split(): void
    {
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);
        Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

        try {
            $zipFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR .
                pathinfo($this->original_name, PATHINFO_FILENAME) . '_split.zip';
            $zip = new \ZipArchive();
            Log::info("print after zip creation");

            $fileExtension = pathinfo($this->original_name, PATHINFO_EXTENSION);
            $reader = ($fileExtension === "xlsx") ?
                IOFactory::createReader('Xlsx') :
                IOFactory::createReader('Xls');

            $sourceSpreadsheet = $reader->load($tempFilePath);

            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                $tempSheets = [];
                foreach ($sourceSpreadsheet->getAllSheets() as $sheet) {
                    // Создаем новый Excel документ (отдельный файл для листа)
                    $newSpreadsheetForSheet = new Spreadsheet();
                    $newSpreadsheetForSheet->removeSheetByIndex(0);

                    // Создаем пустой лист, связанный с новым Spreadsheet
                    $newSheet = new Worksheet($newSpreadsheetForSheet, $sheet->getTitle());
                    $newSpreadsheetForSheet->addSheet($newSheet);

                    // Копируем содержимое и структуры (мерджи, размеры, стили) через существующие методы
                    try {
                        $this->copyMergedCells($sheet, $newSheet);
                        $this->copyDimensions($sheet, $newSheet);
                        // Копирование ячеек и стилей (оптимизированный метод уже в классе)
                        $this->copyCellsWithArrayStyles($sheet, $newSheet);
                    } catch (\Exception $e) {
                        Log::warning('Split: warning while copying sheet content: ' . $e->getMessage(), ['sheet' => $sheet->getTitle()]);
                    }

                    // Формируем безопасный путь для временного файла листа
                    $sheetName = $this->sanitizeFilename($sheet->getTitle());
                    $uniqueId = uniqid();
                    $tempSheetPath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '_' . $uniqueId . '.' . $fileExtension;

                    // Создаем писатель и сохраняем файл
                    $writer = ($fileExtension === "xlsx") ?
                        IOFactory::createWriter($newSpreadsheetForSheet, 'Xlsx') :
                        IOFactory::createWriter($newSpreadsheetForSheet, 'Xls');

                    $writer->save($tempSheetPath);

                    // Добавляем файл в ZIP с корректным расширением
                    $added = $zip->addFile($tempSheetPath, $sheetName . '.' . $fileExtension);
                    if ($added === false) {
                        Log::warning('Split: failed to add file to zip', ['file' => $tempSheetPath, 'zip' => $zipFilePath]);
                    } else {
                        $tempSheets[] = $tempSheetPath;
                    }

                    // Освобождаем память
                    $newSpreadsheetForSheet->disconnectWorksheets();
                    unset($newSpreadsheetForSheet, $newSheet);
                }
                $zip->close();
            }

            foreach ($tempSheets as $tmp) {
                $this->safeUnlink($tmp);
            }

            if (!file_exists($zipFilePath)) {
                Log::error('Function split: Output file does not exist', ['path' => $zipFilePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function split: Output file was not created");
            } else {
                $this->fileMetaData->update(["status" => "completed"]);
            }

            $this->fileMetaData->update(["output_path" => $zipFilePath]);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function split: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временный основной файл
            $this->safeUnlink($tempFilePath);
        }
    }


    private function ExcelToHtml(): void
    {
        // Получаем содержимое файла и обновляем статус
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);

        // Если исходный файл не xlsx – конвертируем через уже реализованную функцию
        $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        if ($extension !== 'xlsx') {
            $conversion = $this->xlsToXlsxAdditional(); // предполагается, что функция возвращает ['path'=>..., 'name'=>...]
            $tempFilePath = $conversion['path'];
            $name = $conversion['name'];
        } else {
            $name = $this->original_name;
        }

        // Создаем временную директорию для HTML-файлов
        $tempHtmlDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('html_files_');
        if (!mkdir($tempHtmlDir, 0755, true) && !is_dir($tempHtmlDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $tempHtmlDir));
        }

        // Определяем число листов через Spout
        $reader = new \OpenSpout\Reader\XLSX\Reader();
        $reader->open($tempFilePath);
        $sheetCount = 0;
        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetCount++;
        }
        $reader->close();

        // Если лист один – генерируем одиночный HTML-файл, иначе ZIP-архив
        if ($sheetCount === 1) {
            $outputFilePath = $this->processSingleSheetHtml($tempFilePath, $name);
        } else {
            $outputFilePath = $this->processMultipleSheetsToZipHtml($tempFilePath, $name, $tempHtmlDir);
        }

        // Обновляем метаданные файла с результатом конвертации
        $this->fileMetaData->update(["status" => "completed", "output_path" => $outputFilePath]);

        // Очистка временных файлов/директорий
        $this->safeUnlink($tempFilePath);
        $this->deleteDirectory($tempHtmlDir);
    }

    private function xlsToXlsxAdditional(): array
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        //$file = UserFile::findOrFail($id);
        Log::info('file', ['file' => $this->original_name]);

        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($this->path);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        $correctTempPath = $tempFilePath . '.xls';
        rename($tempFilePath, $correctTempPath);
        $tempFilePath = $correctTempPath;

        file_put_contents($tempFilePath, $fileContent);
        Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

        try {
            // Настройка читателя
            $reader = IOFactory::createReader('Xls');
            $reader->setReadDataOnly(true);

            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                $highestRow = $sourceSheet->getHighestDataRow(); // Последняя строка с данными
                $highestColumn = $sourceSheet->getHighestDataColumn();

                if ($highestRow == 1 && $sourceSheet->getCell('A1')->getValue() === null) {
                    continue;
                }

                $data = $sourceSheet->rangeToArray(
                    'A1:' . $highestColumn . $highestRow,
                    null,
                    true,
                    false
                );

                $newSheet->fromArray($data);
            }

            $outputFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($tempFilePath, PATHINFO_FILENAME) . '.xlsx';

            $writer = IOFactory::createWriter($newSpreadsheet, 'Xlsx');
            $writer->save($outputFilePath);
            Log::info('XLSX file saved', ['path' => $outputFilePath, 'exists' => file_exists($outputFilePath)]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

            if (!file_exists($outputFilePath)) {
                Log::error('Output file does not exist', ['path' => $outputFilePath]);
                throw new \Exception("Output file was not created");
            }

            if (!is_readable($outputFilePath)) {
                Log::error('Output file is not readable', ['path' => $outputFilePath, 'perms' => substr(sprintf('%o', fileperms($outputFilePath)), -4)]);
                throw new \Exception("Output file is not readable");
            }

            return ['path' => $outputFilePath, 'name' => pathinfo($this->original_name, PATHINFO_FILENAME)];
        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeUnlink($tempFilePath);
        }
    }


    private function processSingleSheetHtml(string $filePath, string $name): string
    {
        $outputFilePath = tempnam(sys_get_temp_dir(), 'html_') . '.html';
        $html = $this->getHtmlHeader($name);

        $reader = new \OpenSpout\Reader\XLSX\Reader();
        $reader->open($filePath);
        foreach ($reader->getSheetIterator() as $sheet) {
            $html .= $this->generateSheetHtmlContent($sheet);
        }
        $reader->close();
        $html .= $this->getHtmlFooter();

        file_put_contents($outputFilePath, $html);
        return $outputFilePath;
    }


    private function processMultipleSheetsToZipHtml(string $filePath, string $name, string $tempHtmlDir): string
    {
        $zipFilePath = tempnam(sys_get_temp_dir(), 'html_zip_') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("Cannot create ZIP archive");
        }

        $reader = new \OpenSpout\Reader\XLSX\Reader();
        $reader->open($filePath);
        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetName = $this->sanitizeFilename($sheet->getName());
            $htmlFilePath = $tempHtmlDir . DIRECTORY_SEPARATOR . $sheetName . '.html';
            $html = $this->getHtmlHeader($sheetName);
            $html .= $this->generateSheetHtmlContent($sheet);
            $html .= $this->getHtmlFooter();
            file_put_contents($htmlFilePath, $html);
            $zip->addFile($htmlFilePath, $sheetName . '.html');
        }
        $reader->close();
        $zip->close();

        return $zipFilePath;
    }


    private function getHtmlHeader(string $title): string
    {
        $styles = ".table-container {
            max-width: 100%;
            overflow-x: auto;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: DejaVu Sans;
            color: #2d3748;
            background: white;
        }
        body { font-size: 12px; }
        caption {
            padding: 1rem;
            font-size: 1.4rem;
            font-weight: 600;
            color: #1a202c;
            text-align: left;
        }
        th {
            background-color: #4a5568;
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 1rem;
            border-right: 1px solid #e2e8f0;
        }
        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-of-type(even) { background-color: #f7fafc; }
        td:first-child { border-right: 1px solid #e2e8f0; }
        tr { transition: background-color 0.2s ease; }
        tr:hover { background-color: #ebf8ff; }";

        return "<!DOCTYPE html><html><head>\n<meta charset=\"UTF-8\">\n<style>$styles</style>\n</head><body><div class='table-container'>\n<h1>" . htmlspecialchars($title) . "</h1>\n";
    }


    private function getHtmlFooter(): string
    {
        return "</div></body></html>";
    }


    private function generateSheetHtmlContent($sheet): string
    {
        $html = "<table><caption>" . htmlspecialchars($sheet->getName()) . "</caption>";
        $isFirstRow = true;

        foreach ($sheet->getRowIterator() as $row) {
            $html .= "<tr>";
            foreach ($row->getCells() as $cell) {
                $value = htmlspecialchars($cell->getValue());
                $html .= $isFirstRow ? "<th>$value</th>" : "<td>$value</td>";
            }
            $html .= "</tr>";
            $isFirstRow = false;
        }

        $html .= "</table><br>";
        return $html;
    }


    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }

    private function excelToCsv()
    {
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);
        Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

        try {
            // Определяем ридер по расширению файла
            $fileExtension = pathinfo($this->original_name, PATHINFO_EXTENSION);
            if ($fileExtension === 'xlsx') {
                $reader = IOFactory::createReader('Xlsx');
            } else {
                $reader = IOFactory::createReader('Xls');
            }

            // Настройка читателя
            $reader->setReadDataOnly(true);
            $outputFilePath = "";

            // Загружаем исходный excel файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            if ($sourceSpreadsheet->getSheetCount() === 1) {
                $sourceSheet = $sourceSpreadsheet->getSheet(0);
                $sheetName = $this->sanitizeFilename($sourceSheet->getTitle());

                $outputFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.csv';

                $this->convertSheetToCsv($sourceSheet, $outputFilePath, $this->delimiter);

                $sourceSpreadsheet->disconnectWorksheets();
                unset($sourceSpreadsheet);
            } else {
                $zipFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($this->original_name, PATHINFO_FILENAME) . '_csv.zip';
                $zip = new \ZipArchive();

                if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {

                    foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                        $sheetName = $this->sanitizeFilename($sourceSheet->getTitle());
                        $csvTempPath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.csv';

                        $this->convertSheetToCsv($sourceSheet, $csvTempPath, $this->delimiter);
                        $zip->addFile($csvTempPath, $sheetName . '.csv');
                    }
                    $zip->close();

                    foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                        $sheetName = $this->sanitizeFilename($sourceSheet->getTitle());
                        $csvTempPath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.csv';
                        $this->safeUnlink($csvTempPath);
                    }
                }
                $sourceSpreadsheet->disconnectWorksheets();
                unset($sourceSpreadsheet);

                $outputFilePath = $zipFilePath;
            }

            if (!file_exists($outputFilePath)) {
                Log::error('Function excelToOds: Output file does not exist', ['path' => $outputFilePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function excelToOds: Output file was not created");
            } elseif (!file_exists($outputFilePath)) {
                Log::error('Function excelToOds: Output file does not exist', ['path' => $outputFilePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function excelToOds: Output file was not created");
            } else {
                $this->fileMetaData->update(["status" => "completed"]);
            }


            $this->fileMetaData->update(["output_path" => $outputFilePath]);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function xlsToXlsx: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временные файлы
            $this->safeUnlink($tempFilePath);
        }
    }


    private function convertSheetToCsv($sourceSheet, string $outputFilePath, string $delimiter = ','): void
    {
        $highestRow = $sourceSheet->getHighestDataRow();
        $highestColumn = $sourceSheet->getHighestDataColumn();

        // Пропускаем пустые листы
        if ($highestRow == 1 && $sourceSheet->getCell('A1')->getValue() === null) {
            return;
        }

        $data = $sourceSheet->rangeToArray(
            'A1:' . $highestColumn . $highestRow,
            null,
            true,
            false
        );
        $csvFile = fopen($outputFilePath, 'w');

        // Добавляем BOM для корректного отображения кириллицы в Excel
        fwrite($csvFile, "\xEF\xBB\xBF");

        // Обрабатываем первую строку (исправленная логика)
        if (!empty($data[0])) {
            $firstRow = $data[0];
            for ($i = 0; $i < count($firstRow); $i++) {
                if ($firstRow[$i] === null) {
                    $firstRow[$i] = 0;
                }
            }
            fputcsv($csvFile, $firstRow, $delimiter);
        }

        // Обрабатываем остальные строки
        for ($i = 1; $i < count($data); $i++) {
            fputcsv($csvFile, $data[$i], $delimiter);
        }

        fclose($csvFile);
        Log::info('CSV file created', ['path' => $outputFilePath, 'size' => filesize($outputFilePath)]);
    }


    private function sanitizeFilename(string $filename): string
    {
        return preg_replace('/[\/:*?"<>|]/', '_', $filename);
    }

    private function excelToOds()
    {
        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);

        try {
            if (strrchr($this->path, '.') == ".xlsx") {
                $reader = IOFactory::createReader('Xlsx');
            } else {
                $reader = IOFactory::createReader('Xls');
            }

            // Настройка читателя
            $reader->setReadDataOnly(true);

            // Загружаем исходный ods файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                // Создаем лист в новом документе
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                $highestRow = $sourceSheet->getHighestDataRow(); // Последняя строка с данными
                $highestColumn = $sourceSheet->getHighestDataColumn();

                if ($highestRow == 1 && $sourceSheet->getCell('A1')->getValue() === null) {
                    continue;
                }

                $data = $sourceSheet->rangeToArray(
                    'A1:' . $highestColumn . $highestRow,
                    null,
                    true,
                    false
                );
                $newSheet->fromArray($data);
            }

            $outputFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($tempFilePath, PATHINFO_FILENAME) . '.ods';

            $writer = IOFactory::createWriter($newSpreadsheet, 'Ods');
            $writer->save($outputFilePath);
            Log::info('ODS file saved', ['path' => $outputFilePath, 'exists' => file_exists($outputFilePath)]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

            // if (!file_exists($outputFilePath)) {
            //     Log::error('Output file does not exist', ['path' => $outputFilePath]);
            //     throw new \Exception("Output file was not created");
            // }

            // if (!is_readable($outputFilePath)) {
            //     Log::error('Output file is not readable', ['path' => $outputFilePath, 'perms' => substr(sprintf('%o', fileperms($outputFilePath)), -4)]);
            //     throw new \Exception("Output file is not readable");
            // }

            if (!file_exists($outputFilePath)) {
                Log::error('Function excelToOds: Output file does not exist', ['path' => $outputFilePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function excelToOds: Output file was not created");
            } elseif (!file_exists($outputFilePath)) {
                Log::error('Function excelToOds: Output file does not exist', ['path' => $outputFilePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function excelToOds: Output file was not created");
            } else {
                $this->fileMetaData->update(["status" => "completed"]);
            }

            $this->fileMetaData->update(["output_path" => $outputFilePath]);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function xlsToXlsx: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временные файлы
            $this->safeUnlink($tempFilePath);
        }
    }

    private function xlsToXlsx(): void
    {
        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);

        try {
            // Настройка читателя
            $reader = IOFactory::createReader('Xls');

            // Загружаем исходный XLS файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый XLSX документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                // Копируем основные свойства листа
                $this->copySheetProperties($sourceSheet, $newSheet);

                // Копируем объединенные ячейки
                $this->copyMergedCells($sourceSheet, $newSheet);

                // Копируем размеры столбцов и строк
                $this->copyDimensions($sourceSheet, $newSheet);

                // Копируем данные и стили с использованием массового подхода
                $this->copyCellsWithArrayStyles($sourceSheet, $newSheet);

                // Освобождаем память после обработки каждого листа
                gc_collect_cycles();
            }

            // Формируем путь для выходного файла
            $outputFilePath = tempnam(sys_get_temp_dir(), 'converted_') . '.xlsx';

            Log::info('New path', ['path' => $outputFilePath]);

            $writer = IOFactory::createWriter($newSpreadsheet, 'Xlsx');

            $writer->save($outputFilePath);

            if (file_exists($outputFilePath)) {
                $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($fileInfo, $outputFilePath);
                finfo_close($fileInfo);

                Log::info('File MIME type check', [
                    'path' => $outputFilePath,
                    'mime_type' => $mimeType,
                    'expected' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ]);

                // if (str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'xlsx')) {
                //     $this->fileMetaData->update(["status" => "completed"]);
                //     $this->fileMetaData->update(["output_path" => $outputFilePath]);
                //     Log::info('XLS to XLSX conversion completed successfully');
                // } else {
                //     $this->fileMetaData->update(["status" => "failed"]);
                //     Log::error('Converted file is not in XLSX format', ['mime_type' => $mimeType]);
                //     throw new \Exception("Converted file is not in XLSX format");
                // }
            }

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

            if (!file_exists($outputFilePath)) {
                Log::error('Function xlsToXlsx: Output file does not exist', ['path' => $outputFilePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function xlsToXlsx: Output file was not created");
            } else {
                $this->fileMetaData->update(["status" => "completed"]);
            }

            $this->fileMetaData->update(["output_path" => $outputFilePath]);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function xlsToXlsx: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временные файлы
            $this->safeUnlink($tempFilePath);
        }
    }

    private function xlsxToXls(): void
    {
        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($this->path);

        $this->fileMetaData->update(["status" => "processing"]);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);

        try {
            // Настройка читателя
            $reader = IOFactory::createReader('Xlsx');

            // Загружаем исходный XLSX файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            // Обрабатываем каждый лист
            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                // Копируем основные свойства листа
                $this->copySheetProperties($sourceSheet, $newSheet);

                // Копируем объединенные ячейки
                $this->copyMergedCells($sourceSheet, $newSheet);

                // Копируем размеры столбцов и строк
                $this->copyDimensions($sourceSheet, $newSheet);

                // Копируем данные и стили с использованием массового подхода
                $this->copyCellsWithArrayStyles($sourceSheet, $newSheet);

                // Освобождаем память после обработки каждого листа
                gc_collect_cycles();
            }

            // Формируем путь для выходного файла
            $outputFilePath = substr($tempFilePath, 0, -4) . '.xls';

            $writer = IOFactory::createWriter($newSpreadsheet, 'Xls');
            $writer->save($outputFilePath);
            Log::info('Function xlsxToXls: XLS file saved', ['path' => $outputFilePath, 'exists' => file_exists($outputFilePath)]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

            if (!file_exists($outputFilePath)) {
                Log::error('Function xlsxToXls: Output file does not exist', ['path' => $outputFilePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function xlsxToXls: Output file was not created");
            } else {
                $this->fileMetaData->update(["status" => "completed"]);
            }

            $this->fileMetaData->update(["output_path" => $outputFilePath]);
        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function xlsxToXls: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временные файлы
            $this->safeUnlink($tempFilePath);
        }
    }

    private function copySheetProperties($sourceSheet, $newSheet): void
    {
        try {
            $newSheet->getPageSetup()->setOrientation($sourceSheet->getPageSetup()->getOrientation());
            $newSheet->getPageSetup()->setPaperSize($sourceSheet->getPageSetup()->getPaperSize());

            $newSheet->getPageMargins()->setTop($sourceSheet->getPageMargins()->getTop());
            $newSheet->getPageMargins()->setRight($sourceSheet->getPageMargins()->getRight());
            $newSheet->getPageMargins()->setLeft($sourceSheet->getPageMargins()->getLeft());
            $newSheet->getPageMargins()->setBottom($sourceSheet->getPageMargins()->getBottom());

            $newSheet->getSheetView()->setZoomScale($sourceSheet->getSheetView()->getZoomScale());
        } catch (\Exception $e) {
            Log::warning("Function copySheetProperties was finished unsuccessfully: Error copying sheet properties: " . $e->getMessage());
        }
    }

    private function copyMergedCells($sourceSheet, $newSheet): void
    {
        try {
            Log::info('Function copyMergedCells was started successfully');
            foreach ($sourceSheet->getMergeCells() as $mergedCells) {
                $newSheet->mergeCells($mergedCells);
            }
            Log::info('Function copyMergedCells was finished successfully');
        } catch (\Exception $e) {
            Log::warning("Function copyMergedCells was finished unsuccessfully: Error copying merged cells: " . $e->getMessage());
        }
    }

    private function copyDimensions($sourceSheet, $newSheet): void
    {
        try {
            // Копируем ширину столбцов
            Log::info('Function copyDimensions was started successfully');
            foreach ($sourceSheet->getColumnDimensions() as $columnDimension) {
                $col = $columnDimension->getColumnIndex();
                $newSheet->getColumnDimension($col)->setWidth($columnDimension->getWidth());
                $newSheet->getColumnDimension($col)->setAutoSize($columnDimension->getAutoSize());
            }

            // Копируем высоту строк
            foreach ($sourceSheet->getRowDimensions() as $rowDimension) {
                $row = $rowDimension->getRowIndex();
                $newSheet->getRowDimension($row)->setRowHeight($rowDimension->getRowHeight());
                $newSheet->getRowDimension($row)->setVisible($rowDimension->getVisible());
            }
            Log::info('Function copyDimensions was finished successfully');
        } catch (\Exception $e) {
            Log::warning("Error copying dimensions: " . $e->getMessage());
        }
    }


    private function copyCellsWithArrayStyles($sourceSheet, $newSheet): void
    {
        $highestRow = $sourceSheet->getHighestDataRow();
        $highestColumn = $sourceSheet->getHighestDataColumn();

        // Увеличиваем размер батча для уменьшения накладных расходов
        $batchSize = 200; // Увеличили с 50 до 200
        $processedRows = 0;

        // Предварительно вычисляем границы столбцов в числовом формате
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        Log::info('Function copyCellsWithArrayStyles: Starting optimized array-based style copying', ['rows' => $highestRow, 'columns' => $highestColumn]);

        for ($row = 1; $row <= $highestRow; $row += $batchSize) {
            $endRow = min($row + $batchSize - 1, $highestRow);

            // Обрабатываем диапазон строк
            for ($currentRow = $row; $currentRow <= $endRow; $currentRow++) {
                $rowData = [];
                $rowStyles = [];

                // Используем числовой индекс для столбцов - значительно быстрее
                for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                    $col = Coordinate::stringFromColumnIndex($colIndex);
                    $cellCoordinate = $col . $currentRow;

                    // Быстрая проверка существования ячейки через вычисляемые свойства
                    $cellExists = $sourceSheet->getCell($cellCoordinate)->getValue() !== null
                        || $sourceSheet->getCell($cellCoordinate)->getStyle()->getFill()->getFillType() !== null;

                    if (!$cellExists) {
                        $rowData[$col] = null;
                        continue;
                    }

                    $sourceCell = $sourceSheet->getCell($cellCoordinate);
                    $cellValue = $this->getSafeCellValue($sourceCell);

                    $rowData[$col] = $cellValue;

                    // Экспортируем стиль только если ячейка не пустая или имеет стиль
                    if ($cellValue !== null || $this->hasVisibleStyle($sourceCell)) {
                        try {
                            $rowStyles[$col] = $sourceCell->getStyle()->exportArray();
                        } catch (\Exception $e) {
                            $rowStyles[$col] = [];
                        }
                    }
                }

                // Массовая запись данных строки
                if (
                    !empty(array_filter($rowData, function ($v) {
                        return $v !== null;
                    }))
                ) {
                    $newSheet->fromArray([$rowData], null, 'A' . $currentRow);
                }

                // Применяем стили только если они есть
                if (!empty($rowStyles)) {
                    $this->applyRowStylesOptimized($newSheet, $currentRow, $rowStyles);
                }

                $processedRows++;

                // Освобождаем память реже - каждые 50 строк
                if ($processedRows % 50 === 0) {
                    gc_collect_cycles();
                }
            }

            Log::debug("Function copyCellsWithArrayStyles: Completed processing rows {$row} to {$endRow}");
        }

        Log::info('Function copyCellsWithArrayStyles: Completed optimized array-based style copying', ['total_rows' => $processedRows]);
    }


    private function getSafeCellValue($cell)
    {
        try {
            $value = $cell->getValue();

            if (is_array($value)) {
                return isset($value[0]) ? $value[0] : null;
            }

            if (is_object($value) && method_exists($value, '__toString')) {
                return $value->__toString();
            }

            return $value;
        } catch (\Exception $e) {
            Log::warning("Function getSafeCellValue was finished unsuccessfully: Error getting cell value: " . $e->getMessage());
            return null;
        }
    }


    private function hasVisibleStyle($cell): bool
    {
        $style = $cell->getStyle();
        return $style->getFill()->getFillType() !== null
            || $style->getFont()->getBold()
            || $style->getFont()->getItalic()
            || $style->getFont()->getSize() !== 11
            || $style->getFont()->getName() !== 'Calibri'
            || $style->getBorders()->getLeft()->getBorderStyle() !== 'none'
            || $style->getBorders()->getRight()->getBorderStyle() !== 'none'
            || $style->getBorders()->getTop()->getBorderStyle() !== 'none'
            || $style->getBorders()->getBottom()->getBorderStyle() !== 'none';
    }


    private function applyRowStylesOptimized($sheet, $rowNumber, $styles): void
    {
        foreach ($styles as $col => $styleArray) {
            if (empty($styleArray))
                continue;

            try {
                $sheet->getStyle($col . $rowNumber)->applyFromArray($styleArray);
            } catch (\Exception $e) {
                Log::error('Function applyRowStylesOptimized was finished unsuccessfully: Apply row styles from array error: ' . $e->getMessage());
            }
        }
    }

    private function safeUnlink(string $path): void
    {
        try {
            if (is_file($path)) { // Проверяем, что это файл, а не директория
                if (@unlink($path)) { // Подавляем предупреждение, но проверяем результат
                    Log::info("File successfully deleted: {$path}");
                } else {
                    Log::warning("Failed to delete file: {$path}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Function safeUnlink was finished unsuccessfully: Error deleting file {$path}: " . $e->getMessage());
        }
    }

}
