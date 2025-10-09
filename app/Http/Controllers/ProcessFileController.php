<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Storage;
use App\Models\UserFile;
use Illuminate\Support\Facades\Log;
use OpenSpout\Reader\XLSX\Reader;

error_reporting(E_ALL);
ini_set('display_errors', 1);

class ProcessFileController extends Controller
{
    public function viewFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }

        $spreadsheet = IOFactory::load($filePath);

        $sheet = $spreadsheet->getActiveSheet();

        $data = $sheet->toArray();

        return $data;
    }
    
    public function xlsxToXls_v1(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        $file = UserFile::findOrFail($id);
        Log::info('Starting XLSX to XLS conversion with array-based styling', ['file_id' => $id]);

        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($file->path);

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
            Log::info('XLS file saved', ['path' => $outputFilePath, 'exists' => file_exists($outputFilePath)]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

            if (!file_exists($outputFilePath)) {
                Log::error('Output file does not exist', ['path' => $outputFilePath]);
                throw new \Exception("Output file was not created");
            }

            // Возвращаем файл для скачивания
            $outputFileName = pathinfo($file->original_name, PATHINFO_FILENAME) . '.xls';
            return response()->download($outputFilePath, $outputFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временные файлы
            $this->safeUnlink($tempFilePath);
        }
    }

    /**
     * Копирует ячейки и стили с использованием массового экспорта/импорта через массивы
     */
    private function copyCellsWithArrayStyles($sourceSheet, $newSheet): void
    {
        $highestRow = $sourceSheet->getHighestDataRow();
        $highestColumn = $sourceSheet->getHighestDataColumn();

        // Используем пакетную обработку для экономии памяти
        $batchSize = 50; // Меньший размер для лучшего управления памятью при работе со стилями
        $processedRows = 0;

        Log::info('Starting array-based style copying', ['rows' => $highestRow, 'columns' => $highestColumn]);

        for ($row = 1; $row <= $highestRow; $row += $batchSize) {
            $endRow = min($row + $batchSize - 1, $highestRow);

            // Обрабатываем диапазон строк
            for ($currentRow = $row; $currentRow <= $endRow; $currentRow++) {
                // Собираем данные строки
                $rowData = [];
                $rowStyles = [];

                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellCoordinate = $col . $currentRow;

                    if (!$sourceSheet->cellExists($cellCoordinate)) {
                        $rowData[$col] = null;
                        continue;
                    }

                    $sourceCell = $sourceSheet->getCell($cellCoordinate);
                    $cellValue = $this->getSafeCellValue($sourceCell);

                    $rowData[$col] = $cellValue;

                    // Экспортируем стиль ячейки в массив
                    try {
                        $rowStyles[$col] = $sourceCell->getStyle()->exportArray();
                    } catch (\Exception $e) {
                        Log::warning("Error exporting style for cell {$cellCoordinate}: " . $e->getMessage());
                        $rowStyles[$col] = [];
                    }
                }

                // Записываем данные строки массово
                $newSheet->fromArray([$rowData], null, 'A' . $currentRow);

                // Применяем стили массово для всей строки
                $this->applyRowStyles($newSheet, $currentRow, $rowStyles, $highestColumn);

                $processedRows++;

                // Освобождаем память каждые 25 строк
                if ($processedRows % 25 === 0) {
                    gc_collect_cycles();
                    Log::debug("Processed {$processedRows} rows with array styles");
                }
            }

            Log::debug("Completed processing rows {$row} to {$endRow}");
        }

        Log::info('Completed array-based style copying', ['total_rows' => $processedRows]);
    }

    /**
     * Массово применяет стили к строке
     */
    private function applyRowStyles($sheet, $rowNumber, $styles, $highestColumn): void
    {
        foreach ($styles as $col => $styleArray) {
            if (empty($styleArray)) {
                continue;
            }

            $cellCoordinate = $col . $rowNumber;

            try {
                // Применяем стиль из массива
                $sheet->getStyle($cellCoordinate)->applyFromArray($styleArray);
            } catch (\Exception $e) {
                Log::warning("Error applying style to cell {$cellCoordinate}: " . $e->getMessage());

                // Пытаемся применить стиль по частям
                $this->applyStyleSafely($sheet, $cellCoordinate, $styleArray);
            }
        }
    }

    /**
     * Безопасное применение стиля по частям
     */
    private function applyStyleSafely($sheet, $cellCoordinate, $styleArray): void
    {
        // Применяем отдельные компоненты стиля
        if (isset($styleArray['font'])) {
            try {
                $sheet->getStyle($cellCoordinate)->getFont()->applyFromArray($styleArray['font']);
            } catch (\Exception $e) {
                Log::debug("Error applying font style to {$cellCoordinate}: " . $e->getMessage());
            }
        }

        if (isset($styleArray['fill'])) {
            try {
                $sheet->getStyle($cellCoordinate)->getFill()->applyFromArray($styleArray['fill']);
            } catch (\Exception $e) {
                Log::debug("Error applying fill style to {$cellCoordinate}: " . $e->getMessage());
            }
        }

        if (isset($styleArray['borders'])) {
            try {
                $sheet->getStyle($cellCoordinate)->getBorders()->applyFromArray($styleArray['borders']);
            } catch (\Exception $e) {
                Log::debug("Error applying borders to {$cellCoordinate}: " . $e->getMessage());
            }
        }

        if (isset($styleArray['alignment'])) {
            try {
                $sheet->getStyle($cellCoordinate)->getAlignment()->applyFromArray($styleArray['alignment']);
            } catch (\Exception $e) {
                Log::debug("Error applying alignment to {$cellCoordinate}: " . $e->getMessage());
            }
        }

        if (isset($styleArray['numberFormat'])) {
            try {
                $formatCode = $styleArray['numberFormat'];
                if (is_array($formatCode) && isset($formatCode['formatCode'])) {
                    $formatCode = $formatCode['formatCode'];
                }
                if ($formatCode && $formatCode !== 'General') {
                    $sheet->getStyle($cellCoordinate)->getNumberFormat()->setFormatCode($formatCode);
                }
            } catch (\Exception $e) {
                Log::debug("Error applying number format to {$cellCoordinate}: " . $e->getMessage());
            }
        }
    }

    /**
     * Копирует свойства листа
     */
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
            Log::warning("Error copying sheet properties: " . $e->getMessage());
        }
    }

    /**
     * Копирует объединенные ячейки
     */
    private function copyMergedCells($sourceSheet, $newSheet): void
    {
        try {
            foreach ($sourceSheet->getMergeCells() as $mergedCells) {
                $newSheet->mergeCells($mergedCells);
            }
        } catch (\Exception $e) {
            Log::warning("Error copying merged cells: " . $e->getMessage());
        }
    }

    /**
     * Копирует размеры столбцов и строк
     */
    private function copyDimensions($sourceSheet, $newSheet): void
    {
        try {
            // Копируем ширину столбцов
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
        } catch (\Exception $e) {
            Log::warning("Error copying dimensions: " . $e->getMessage());
        }
    }

    /**
     * Безопасное получение значения ячейки
     */
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
            Log::warning("Error getting cell value: " . $e->getMessage());
            return null;
        }
    }
    
    /////old


    public function xlsxToXls(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        $file = UserFile::findOrFail($id);
        Log::info('file', ['file' => $file]);

        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($file->path);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);
        Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

        try {
            // Настройка читателя
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);

            // Загружаем исходный XLSX файл по ПУТИ
            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            // Обрабатываем каждый лист
            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                $highestRow = $sourceSheet->getHighestDataRow();
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

            // Формируем путь для выходного файла ПРАВИЛЬНО
            $outputFilePath = substr($tempFilePath, 0, -4) . '.xls';

            $writer = IOFactory::createWriter($newSpreadsheet, 'Xls');
            $writer->save($outputFilePath);
            Log::info('XLS file saved', ['path' => $outputFilePath, 'exists' => file_exists($outputFilePath)]);

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

            // Возвращаем файл для скачивания
            return response()->download($outputFilePath, substr($file->original_name, 0, -4) . 'xls')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временные файлы
            $this->safeUnlink($tempFilePath);
        }
    }

    //old


    public function xlsToXlsx(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        $file = UserFile::findOrFail($id);
        Log::info('file', ['file' => $file]);

        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($file->path);

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
            dd($outputFilePath);

            return response()->download($outputFilePath, pathinfo($file->original_name, PATHINFO_FILENAME) . '.xlsx')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeUnlink($tempFilePath);
        }
    }


    public function excelToOds(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        $file = UserFile::findOrFail($id);
        Log::info('file', ['file' => $file]);

        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($file->path);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);
        Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

        try {
            if (strrchr($file->path, '.') == ".xlsx") {
                $reader = IOFactory::createReader('Xlsx');
            } else {
                $reader = IOFactory::createReader('Xls');
            }

            // Настройка читателя
            $reader->setReadDataOnly(true);

            // Загружаем исходный excel файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0); // Удаляем дефолтный лист

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

            if (!file_exists($outputFilePath)) {
                Log::error('Output file does not exist', ['path' => $outputFilePath]);
                throw new \Exception("Output file was not created");
            }

            if (!is_readable($outputFilePath)) {
                Log::error('Output file is not readable', ['path' => $outputFilePath, 'perms' => substr(sprintf('%o', fileperms($outputFilePath)), -4)]);
                throw new \Exception("Output file is not readable");
            }

            return response()->download($outputFilePath, pathinfo($file->original_name, PATHINFO_FILENAME) . '.ods')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeUnlink($tempFilePath);
        }
    }

    public function excelToCsv(string $id, string $delimiter = ',')
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        $file = UserFile::findOrFail($id);
        Log::info('file', ['file' => $file]);

        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($file->path);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);
        Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

        try {
            // Определяем ридер по расширению файла
            $fileExtension = pathinfo($file->original_name, PATHINFO_EXTENSION);
            if ($fileExtension === 'xlsx') {
                $reader = IOFactory::createReader('Xlsx');
            } else {
                $reader = IOFactory::createReader('Xls');
            }

            // Настройка читателя
            $reader->setReadDataOnly(true);

            // Загружаем исходный excel файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            if ($sourceSpreadsheet->getSheetCount() === 1) {
                $sourceSheet = $sourceSpreadsheet->getSheet(0);
                $sheetName = $this->sanitizeFilename($sourceSheet->getTitle());

                $outputFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.csv';

                $this->convertSheetToCsv($sourceSheet, $outputFilePath, $delimiter);

                $sourceSpreadsheet->disconnectWorksheets();
                unset($sourceSpreadsheet);

                return response()->download($outputFilePath, $sheetName . '.csv')->deleteFileAfterSend(true);
            } else {

                $zipFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($file->original_name, PATHINFO_FILENAME) . '_csv.zip';
                $zip = new \ZipArchive();

                if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
                    foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                        $sheetName = $this->sanitizeFilename($sourceSheet->getTitle());
                        $csvTempPath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.csv';

                        $this->convertSheetToCsv($sourceSheet, $csvTempPath, $delimiter);
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

                return response()->download($zipFilePath, pathinfo($file->original_name, PATHINFO_FILENAME) . '_csv.zip')->deleteFileAfterSend(true);
            }
        } catch (\Exception $e) {
            Log::error('CSV conversion error: ' . $e->getMessage());
        } finally {
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

    #########################################################################

    /**
     * Постепенная запись HTML заголовка
     */
    private function writeHtmlHeader($fileHandle, string $title): void
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
            -pdf-keep-in-frame-mode: shrink;
            font-family: DejaVu Sans;
            color: #2d3748;
            background: white;
        }
            body {
            font-size: 12px;
        }

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

        tr:nth-of-type(even) {
            background-color: #f7fafc;
        }

        td:first-child {
            border-right: 1px solid #e2e8f0;
        }

        tr {
            transition: background-color 0.2s ease;
        }

        tr:hover {
            background-color: #ebf8ff;
        }";

        fwrite($fileHandle, "<!DOCTYPE html><html><head>\n<meta charset=\"UTF-8\">\n<style>" . $styles . "</style>\n</head><body><div class='table-container'>\n");
    }

    /**
     * Постепенная запись HTML подвала
     */
    private function writeHtmlFooter($fileHandle): void
    {
        fwrite($fileHandle, "</div></body></html>");
    }

    ######################################################################
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
            Log::error("Error deleting file {$path}: " . $e->getMessage());
        }
    }

    ################################
    //additional xls to xlsx
    private function xlsToXlsxAdditional(string $id): array
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        $file = UserFile::findOrFail($id);
        Log::info('file', ['file' => $file]);

        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($file->path);

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

            return ['path' => $outputFilePath, 'name' => pathinfo($file->original_name, PATHINFO_FILENAME)];
        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeUnlink($tempFilePath);
        }
    }

    ###########################
    //rewrite function spout to xlsx
    public function convertExcelToHtmlViaSpout(string $id)
    {
        $file = UserFile::findOrFail($id);

        $fileExtension = pathinfo($file->original_name, PATHINFO_EXTENSION);
        if ($fileExtension === 'xlsx') {
            $filePath = Storage::disk('local')->path($file->path);
            $name = $file->original_name;
        } else {
            $convergedResponse = $this->xlsToXlsxAdditional($id);
            $filePath = $convergedResponse['path'];
            $name = $convergedResponse['name'];
        }

        // Создаем временную директорию для HTML-файлов
        $tempHtmlDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('html_files_');
        if (!mkdir($tempHtmlDir, 0755, true) && !is_dir($tempHtmlDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $tempHtmlDir));
        }

        $reader = new Reader();
        $reader->open($filePath);

        // Получаем количество листов
        $sheetCount = 0;
        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetCount++;
        }

        // Если лист один, возвращаем одиночный HTML-файл (старая логика)
        if ($sheetCount === 1) {
            $reader->close();
            return $this->processSingleSheet($filePath, $name);
        }

        // Если листов несколько, создаем ZIP-архив
        return $this->processMultipleSheetsToZip($filePath, $name, $tempHtmlDir);
    }

    private function processSingleSheet(string $filePath, string $name)
    {
        $outputFilePath = tempnam(sys_get_temp_dir(), 'html_') . '.html';

        try {
            $reader = new Reader();
            $reader->open($filePath);

            $htmlFile = fopen($outputFilePath, 'w');
            $this->writeHtmlHeader($htmlFile, $name);

            foreach ($reader->getSheetIterator() as $sheet) {
                $this->generateSheetHtml($sheet, $htmlFile);
            }

            $this->writeHtmlFooter($htmlFile);
            fclose($htmlFile);
            $reader->close();

            return response()->download($outputFilePath, pathinfo($name, PATHINFO_FILENAME) . '.html')
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            if (isset($htmlFile))
                fclose($htmlFile);
            if (file_exists($outputFilePath))
                @unlink($outputFilePath);
            throw $e;
        }
    }

    private function processMultipleSheetsToZip(string $filePath, string $name, string $tempHtmlDir)
    {
        $zipFilePath = tempnam(sys_get_temp_dir(), 'html_zip_') . '.zip';
        $zip = new \ZipArchive();

        try {
            // Создаем ZIP-архив :cite[3]:cite[6]
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception("Cannot create ZIP archive");
            }

            $reader = new Reader();
            $reader->open($filePath);

            // Обрабатываем каждый лист
            foreach ($reader->getSheetIterator() as $sheet) {
                $sheetName = $this->sanitizeFilename($sheet->getName());
                $htmlFilePath = $tempHtmlDir . DIRECTORY_SEPARATOR . $sheetName . '.html';

                // Генерируем HTML для текущего листа
                $htmlFile = fopen($htmlFilePath, 'w');
                $this->writeHtmlHeader($htmlFile, $sheetName);
                $this->generateSheetHtml($sheet, $htmlFile);
                $this->writeHtmlFooter($htmlFile);
                fclose($htmlFile);

                // Добавляем HTML-файл в ZIP-архив :cite[9]
                $zip->addFile($htmlFilePath, $sheetName . '.html');
            }

            $reader->close();
            $zip->close();

            // Удаляем временную директорию с HTML-файлами
            $this->deleteDirectory($tempHtmlDir);

            return response()->download($zipFilePath, pathinfo($name, PATHINFO_FILENAME) . '_html.zip')
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Удаляем временную директорию в случае ошибки
            $this->deleteDirectory($tempHtmlDir);
            if (file_exists($zipFilePath))
                @unlink($zipFilePath);
            throw $e;
        }
    }

    private function generateSheetHtml($sheet, $htmlFile): void
    {
        $isFirstRow = true;
        $rowCount = 0;

        fwrite($htmlFile, "<table>");
        fwrite($htmlFile, "<caption>" . htmlspecialchars($sheet->getName()) . "</caption>");

        foreach ($sheet->getRowIterator() as $row) {
            $cells = $row->getCells();

            fwrite($htmlFile, "<tr>");

            foreach ($cells as $cell) {
                $value = $cell->getValue();


                if ($isFirstRow) {
                    fwrite($htmlFile, "<th>" . htmlspecialchars($value) . "</th>");
                } else {
                    fwrite($htmlFile, "<td>" . htmlspecialchars($value) . "</td>");
                }
            }

            fwrite($htmlFile, "</tr>");
            $isFirstRow = false;
            $rowCount++;

            // Освобождаем память каждые 100 строк
            if ($rowCount % 100 === 0) {
                gc_collect_cycles();
            }
        }

        fwrite($htmlFile, "</table><br>");
    }

    /**
     * Удаление директории с содержимым
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir))
            return;

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }
}
