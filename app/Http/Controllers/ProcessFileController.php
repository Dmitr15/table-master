<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Storage;
use App\Models\UserFile;
use PhpOffice\PhpSpreadsheet\Calculation\Web\Service;
use App\Services\ConvertService;
use Illuminate\Support\Facades\Log;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Common\Entity\Row;

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



    // public function excelToHtml(string $id)
    // {
    //     $tempDir = sys_get_temp_dir();
    //     if (!is_writable($tempDir)) {
    //         Log::error('Temp directory is not writable', ['path' => $tempDir]);
    //         throw new \Exception("Temporary directory is not writable");
    //     }

    //     $file = UserFile::findOrFail($id);
    //     Log::info('file', ['file' => $file]);

    //     // Получаем содержимое файла
    //     $fileContent = Storage::disk('local')->get($file->path);

    //     // Создаем временный файл
    //     $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
    //     file_put_contents($tempFilePath, $fileContent);
    //     Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

    //     try {
    //         // Определяем ридер по расширению файла
    //         $fileExtension = pathinfo($file->original_name, PATHINFO_EXTENSION);
    //         if ($fileExtension === 'xlsx') {
    //             $reader = IOFactory::createReader('Xlsx');
    //         } else {
    //             $reader = IOFactory::createReader('Xls');
    //         }

    //         $reader->setReadDataOnly(true);

    //         // Загружаем исходный excel файл
    //         $spreadsheet = $reader->load($tempFilePath);
    //         $sheets = $spreadsheet->getAllSheets();

    //         if (count($sheets) === 1) {
    //             $sheet = $sheets[0];
    //             $sheetName = $this->sanitizeFilename($sheet->getTitle());

    //             $outputFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.html';
    //             Log::info('Output file path created', ['outputFilePath' => $outputFilePath]);

    //             $this->convertSheetToHtml($sheet, $outputFilePath);
    //             Log::info("File converted");

    //             $spreadsheet->disconnectWorksheets();
    //             unset($spreadsheet);

    //             return response()->download($outputFilePath, $sheetName . '.html')->deleteFileAfterSend(true);
    //         } else {
    //             $zipFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($file->original_name, PATHINFO_FILENAME) . '_html.zip';
    //             $zip = new \ZipArchive();

    //             if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
    //                 foreach ($sheets as $sheet) {
    //                     $sheetName = $this->sanitizeFilename($sheet->getTitle());
    //                     $htmlTempPath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.html';

    //                     $this->convertSheetToHtml($sheet, $htmlTempPath);
    //                     $zip->addFile($htmlTempPath, $sheetName . '.html');
    //                 }
    //                 $zip->close();

    //                 foreach ($sheets as $sheet) {
    //                     $sheetName = $this->sanitizeFilename($sheet->getTitle());
    //                     $htmlTempPath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.html';
    //                     $this->safeUnlink($htmlTempPath);
    //                 }
    //             }
    //             $spreadsheet->disconnectWorksheets();
    //             unset($spreadsheet);

    //             return response()->download($zipFilePath, pathinfo($file->original_name, PATHINFO_FILENAME) . '_html.zip')->deleteFileAfterSend(true);
    //         }

    //     } catch (\Exception $e) {
    //         Log::error('HTML conversion error: ' . $e->getMessage());
    //         throw $e;
    //     } finally {
    //         $this->safeUnlink($tempFilePath);
    //     }
    // }




    // private function convertSheetToHtml($sheet, string $outputFilePath): void
    // {
    //     $style = ".table-container {
    //         max-width: 100%; 
    //         overflow-x: auto; 
    //         margin-bottom: 20px; 
    //         border-radius: 8px; 
    //         box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    //     } 
    //     table { 
    //         border-collapse: collapse; 
    //         width: 100%; 
    //         -pdf-keep-in-frame-mode: shrink; 
    //         font-family: DejaVu Sans; 
    //         color: #2d3748; background: white;
    //     }
    //     body {
    //         font-size: 12px;
    //     } 
    //     caption {
    //         padding: 1rem; 
    //         font-size: 1.4rem; 
    //         font-weight: 600; 
    //         color: #1a202c; 
    //         text-align: left; 
    //     }
    //     th { 
    //         background-color: #4a5568; 
    //         color: white; 
    //         font-weight: 600; 
    //         text-align: left;
    //         padding: 1rem;
    //         border-right: 1px solid #e2e8f0;
    //     }
    //     td {
    //         padding: 1rem;
    //         border-bottom: 1px solid #e2e8f0;
    //     }
    //     tr:nth-of-type(even) {
    //         background-color: #f7fafc;
    //     }
    //     td:first-child {
    //         border-right: 1px solid #e2e8f0;
    //     }
    //     tr {
    //         transition: background-color 0.2s ease;
    //     }
    //     tr:hover {
    //         background-color: #ebf8ff;
    //     }
    //     ";

    //     $htmlFile = fopen($outputFilePath, 'w');
    //     if (!$htmlFile) {
    //         throw new \Exception("Cannot create file: " . $outputFilePath);
    //     }
    //     Log::info('htmlFile file created', ['path' => $htmlFile]);

    //     // Добавляем BOM для корректного отображения кириллицы
    //     fwrite($htmlFile, "\xEF\xBB\xBF");

    //     $sheetName = $sheet->getTitle();

    //     fwrite($htmlFile, "<!DOCTYPE html>");
    //     fwrite($htmlFile, "<html lang=\"en\">");
    //     fwrite($htmlFile, "<head>");
    //     fwrite($htmlFile, "<meta charset=\"UTF-8\">");
    //     fwrite($htmlFile, "<title>" . htmlspecialchars($sheetName) . "</title>");
    //     fwrite($htmlFile, "<style>" . $style . "</style>");
    //     fwrite($htmlFile, "</head>");
    //     fwrite($htmlFile, "<body>");
    //     fwrite($htmlFile, "<div class='table-container'>");
    //     fwrite($htmlFile, "<table>");
    //     fwrite($htmlFile, "<caption>" . htmlspecialchars($sheetName) . "</caption>");

    //     // Получаем итератор строк для текущего листа
    //     $rowIterator = $sheet->getRowIterator();

    //     foreach ($rowIterator as $row) {
    //         $cellIterator = $row->getCellIterator();
    //         $cellIterator->setIterateOnlyExistingCells(false);

    //         fwrite($htmlFile, "<tr>");

    //         foreach ($cellIterator as $cell) {
    //             $value = $cell->getValue();
    //             if ($value === null) {
    //                 $value = "";
    //             } else {
    //                 // Если ячейка содержит формулу, получаем вычисленное значение
    //                 if ($cell->getDataType() == 'f') {
    //                     $value = $cell->getCalculatedValue();
    //                 }
    //             }

    //             // Определяем, заголовок это или обычная ячейка
    //             if ($row->getRowIndex() == 1) {
    //                 fwrite($htmlFile, "<th>");
    //             } else {
    //                 fwrite($htmlFile, "<td>");
    //             }

    //             // Экранируем HTML-символы и записываем значение
    //             fwrite($htmlFile, htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

    //             // Закрываем ячейку
    //             if ($row->getRowIndex() == 1) {
    //                 fwrite($htmlFile, "</th>");
    //             } else {
    //                 fwrite($htmlFile, "</td>");
    //             }
    //         }
    //         fwrite($htmlFile, "</tr>");
    //     }
    //     fwrite($htmlFile, "</table>");
    //     fwrite($htmlFile, "</div>");
    //     fwrite($htmlFile, "</body>");
    //     fwrite($htmlFile, "</html>");

    //     fclose($htmlFile);

    //     Log::info('HTML file created', ['path' => $outputFilePath, 'size' => filesize($outputFilePath)]);
    // }


    #########################################################################


    public function convertExcelToHtml(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        $file = UserFile::findOrFail($id);
        Log::info('Starting HTML conversion for large file', ['file_id' => $id, 'rows' => 5000]);

        // Увеличиваем лимит памяти для больших файлов
        ini_set('memory_limit', '512M');

        $fileContent = Storage::disk('local')->get($file->path);
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);

        try {
            $fileExtension = pathinfo($file->original_name, PATHINFO_EXTENSION);
            $reader = $fileExtension === 'xlsx' ? IOFactory::createReader('Xlsx') : IOFactory::createReader('Xls');

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tempFilePath);

            if ($spreadsheet->getSheetCount() === 1) {
                $sheet = $spreadsheet->getSheet(0);
                $sheetName = $this->sanitizeFilename($sheet->getTitle());
                $outputFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.html';

                $this->convertLargeSheetToHtml($sheet, $outputFilePath);

                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);

                return response()->download($outputFilePath, $sheetName . '.html')->deleteFileAfterSend(true);
            } else {
                // Многолистовой вариант с оптимизацией
                return $this->handleMultipleSheetsOptimized($spreadsheet, $tempFilePath, $file);
            }

        } catch (\Exception $e) {
            Log::error('HTML conversion error for large file: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeUnlink($tempFilePath);
        }
    }


    /**
     * Оптимизированная конвертация больших листов
     */


    private function convertLargeSheetToHtml($sheet, string $outputFilePath): void
    {
        $htmlFile = fopen($outputFilePath, 'w');
        if (!$htmlFile) {
            throw new \Exception("Cannot create file: " . $outputFilePath);
        }

        // Записываем HTML постепенно, не накапливая в памяти
        $this->writeHtmlHeader($htmlFile, $sheet->getTitle());

        $rowIterator = $sheet->getRowIterator();
        $isFirstRow = true;

        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            fwrite($htmlFile, "<tr>");

            foreach ($cellIterator as $cell) {
                $value = $this->getCellValueOptimized($cell);

                if ($isFirstRow) {
                    fwrite($htmlFile, "<th>" . htmlspecialchars($value) . "</th>");
                } else {
                    fwrite($htmlFile, "<td>" . htmlspecialchars($value) . "</td>");
                }
            }

            fwrite($htmlFile, "</tr>");
            $isFirstRow = false;

            // Освобождаем память каждые 100 строк
            if ($row->getRowIndex() % 100 === 0) {
                gc_collect_cycles();
            }
        }

        $this->writeHtmlFooter($htmlFile);
        fclose($htmlFile);
    }


    /**
     * Получение значения ячейки с оптимизацией памяти
     */
    private function getCellValueOptimized($cell): string
    {
        $value = $cell->getValue();

        if ($value === null) {
            return "";
        }

        // Для формул - используем оптимизированный метод
        if ($cell->getDataType() == 'f') {
            try {
                return (string) $cell->getCalculatedValue();
            } catch (\Exception $e) {
                return "#ERROR";
            }
        }

        return (string) $value;
    }

    /**
     * Постепенная запись HTML заголовка
     */
    private function writeHtmlHeader($fileHandle, string $title): void
    {
        // $styles = ".table-container { max-width: 100%; overflow-x: auto; }
        //        table { border-collapse: collapse; width: 100%; }
        //        th, td { padding: 8px; border: 1px solid #ddd; }
        //        th { background-color: #f2f2f2; }";

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

        fwrite($fileHandle, "<!DOCTYPE html><html><head>\n<meta charset=\"UTF-8\">\\n<style>" . $styles . "</style>\n</head><body><div class='table-container'><table>\n<caption>" . htmlspecialchars($title) . "</caption>");
        // fwrite($fileHandle, "<meta charset=\"UTF-8\">");
        // fwrite($fileHandle, "<title>" . htmlspecialchars($title) . "</title>");
        // fwrite($fileHandle, "<style>" . $styles . "</style>");
        // fwrite($fileHandle, "</head><body><div class='table-container'><table>");
        // fwrite($fileHandle, "<caption>" . htmlspecialchars($title) . "</caption>");
    }

    /**
     * Постепенная запись HTML подвала
     */
    private function writeHtmlFooter($fileHandle): void
    {
        fwrite($fileHandle, "</table></div></body></html>");
    }

    /**
     * Оптимизированная обработка нескольких листов
     */
    private function handleMultipleSheetsOptimized($spreadsheet, $tempFilePath, $file)
    {
        $zipFilePath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . pathinfo($file->original_name, PATHINFO_FILENAME) . '_html.zip';

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $sheetName = $this->sanitizeFilename($sheet->getTitle());
                $htmlTempPath = pathinfo($tempFilePath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $sheetName . '.html';

                $this->convertLargeSheetToHtml($sheet, $htmlTempPath);
                $zip->addFile($htmlTempPath, $sheetName . '.html');

                // Немедленно удаляем временный файл после добавления в ZIP
                $this->safeUnlink($htmlTempPath);
            }
            $zip->close();
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return response()->download($zipFilePath, pathinfo($file->original_name, PATHINFO_FILENAME) . '_html.zip')->deleteFileAfterSend(true);
    }



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

        $outputFilePath = tempnam(sys_get_temp_dir(), 'html_') . '.html';

        try {
            $reader = new Reader();
            $reader->open($filePath);

            $htmlFile = fopen($outputFilePath, 'w');
            $this->writeHtmlHeader($htmlFile, $name);
            
            foreach ($reader->getSheetIterator() as $sheet) {
                $isFirstRow = true;
                $rowCount = 0;

                fwrite($htmlFile, "<table border='1'>");
                fwrite($htmlFile, "<caption>Лист: " . htmlspecialchars($sheet->getName()) . "</caption>");

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

            $this->writeHtmlFooter($htmlFile);
            fclose($htmlFile);
            $reader->close();

            return response()->download($outputFilePath, $name . '.html')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Spout conversion error: ' . $e->getMessage());
            throw $e;
        }
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
            //dd($outputFilePath);

            $response = ['path' => $outputFilePath, 'name' => pathinfo($file->original_name, PATHINFO_FILENAME)];

            return $response;

            //return response()->download($outputFilePath, pathinfo($file->original_name, PATHINFO_FILENAME) . '.xlsx')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeUnlink($tempFilePath);
        }
    }
}
