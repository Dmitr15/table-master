<?php

namespace App\Services;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConvertService
{
    //convert xlsx to xls
    public function convertXlsxToXlsOptimized(string $xlsxFilePath): bool
    {
        try {
            if (!file_exists($xlsxFilePath)) {
                throw new \Exception("File not found: " . $xlsxFilePath);
            }

            // Настройка читателя
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true); // Игнорируем стили для скорости

            // Загружаем исходный XLSX файл
            $sourceSpreadsheet = $reader->load($xlsxFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0); // Удаляем дефолтный лист

            // Обрабатываем каждый лист
            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {

                // Создаем лист в новом документе
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                $highestRow = $sourceSheet->getHighestDataRow(); // Последняя строка с данными
                $highestColumn = $sourceSheet->getHighestDataColumn();

                if ($highestRow == 1 && $sourceSheet->getCell('A1')->getValue() === null) {
                    // Лист полностью пустой - пропускаем
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

            // Сохраняем результат
            $outputFilePath = substr($xlsxFilePath, 0, -4) . 'xls';
            $writer = IOFactory::createWriter($newSpreadsheet, 'Xls');
            $writer->save($outputFilePath);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet);
            unset($newSpreadsheet);

            return true;

        } catch (\Throwable $th) {
            error_log("Error during conversion: " . $th->getMessage());
            return false;
        }
    }


    //convert xls to xlsx
    public function convertXlsToXlsxOptimized(string $xlsFilePath): bool
    {
        try {
            if (!file_exists($xlsFilePath)) {
                throw new \Exception("File not found: " . $xlsFilePath);
            }

            // Настройка читателя
            $reader = IOFactory::createReader('Xls');
            $reader->setReadDataOnly(true); // Игнорируем стили для скорости

            // Загружаем исходный XLSX файл
            $sourceSpreadsheet = $reader->load($xlsFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0); // Удаляем дефолтный лист

            // Обрабатываем каждый лист
            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {

                // Создаем лист в новом документе
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);


                $highestRow = $sourceSheet->getHighestDataRow(); // Последняя строка с данными
                $highestColumn = $sourceSheet->getHighestDataColumn();

                if ($highestRow == 1 && $sourceSheet->getCell('A1')->getValue() === null) {
                    // Лист полностью пустой - пропускаем
                    continue;
                }

                $data = $sourceSheet->rangeToArray(
                    'A1:' . $highestColumn . $highestRow,
                    null,
                    true,
                    false
                );

                $newSheet->fromArray($data);     // Записываем массив целиком

            }

            // Сохраняем результат
            $outputFilePath = substr($xlsFilePath, 0, -3) . 'xlsx'; // Меняем расширение на .xls
            $writer = IOFactory::createWriter($newSpreadsheet, 'Xlsx');
            $writer->save($outputFilePath);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet);
            unset($newSpreadsheet);

            return true;

        } catch (\Throwable $th) {
            error_log("Error during conversion: " . $th->getMessage());
            return false;
        }
    }

    //convert xls/xlsx to ods
    public function convertExcelToOdsOptimized(string $xlsxFilePath): bool
    {
        try {
            if (!file_exists($xlsxFilePath)) {
                throw new \Exception("File not found: " . $xlsxFilePath);
            }

            if (strrchr($xlsxFilePath, '.') == ".xlsx") {
                $reader = IOFactory::createReader('Xlsx');
            } else {
                $reader = IOFactory::createReader('Xls');
            }

            // Настройка читателя
            $reader->setReadDataOnly(true); // Игнорируем стили для скорости

            // Загружаем исходный XLSX файл
            $sourceSpreadsheet = $reader->load($xlsxFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0); // Удаляем дефолтный лист

            // Обрабатываем каждый лист
            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {

                // Создаем лист в новом документе
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);


                $highestRow = $sourceSheet->getHighestDataRow(); // Последняя строка с данными
                $highestColumn = $sourceSheet->getHighestDataColumn();

                if ($highestRow == 1 && $sourceSheet->getCell('A1')->getValue() === null) {
                    // Лист полностью пустой - пропускаем
                    continue;
                }

                $data = $sourceSheet->rangeToArray(
                    'A1:' . $highestColumn . $highestRow,
                    null,
                    true,
                    false
                );

                $newSheet->fromArray($data);     // Записываем массив целиком

            }

            // Сохраняем результат
            if (strrchr($xlsxFilePath, '.') == ".xlsx") {
                $outputFilePath = substr($xlsxFilePath, 0, -4) . 'ods';
            } else {
                $outputFilePath = substr($xlsxFilePath, 0, -3) . 'ods';
            }
            //$outputFilePath = substr($xlsxFilePath, 0, -4) . 'ods'; 
            $writer = IOFactory::createWriter($newSpreadsheet, 'Ods');
            $writer->save($outputFilePath);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet);
            unset($newSpreadsheet);

            return true;

        } catch (\Throwable $th) {
            error_log("Error during conversion: " . $th->getMessage());
            return false;
        }
    }

    //convert xls/xlsx to html
    public function convertExcelToHtml(string $excelFilePath): bool
    {
        try {
            if (!file_exists($excelFilePath)) {
                throw new \Exception("File not found: " . $excelFilePath);
            }

            // Создаем reader для Excel файла
            $reader = IOFactory::createReaderForFile($excelFilePath);
            $reader->setReadDataOnly(true);

            // Загружаем Excel файл со всеми листами
            $spreadsheet = $reader->load($excelFilePath);
            $sheets = $spreadsheet->getAllSheets();

            // Обрабатываем каждый лист
            foreach ($sheets as $index => $sheet) {
                // Создаем имя HTML файла на основе имени листа
                $sheetName = $sheet->getTitle();

                // Заменяем недопустимые символы в имени файла
                $sheetName = preg_replace('/[\/:*?"<>|]/', '_', $sheetName);

                // Формируем путь к HTML файлу
                $basePath = substr($excelFilePath, 0, strrpos($excelFilePath, '.'));
                $htmlFilePath = $basePath . '_' . $sheetName . '.html';

                $style = ".table-container {
                        max-width: 100%; 
                        overflow-x: auto; 
                        margin-bottom: 20px; 
                        border-radius: 8px; 
                        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                    } 
                    table { 
                        border-collapse: collapse; 
                        width: 100%; 
                        -pdf-keep-in-frame-mode: shrink; 
                        font-family: DejaVu Sans; 
                        color: #2d3748; background: white;
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
                    }
                    ";

                // Открываем HTML файл для записи (используем 'w' вместо 'a' для перезаписи)
                $htmlFile = fopen($htmlFilePath, 'w');
                if (!$htmlFile) {
                    throw new \Exception("Cannot create file: " . $htmlFilePath);
                }

                // Добавляем BOM для корректного отображения кириллицы в Excel
                fwrite($htmlFile, "\xEF\xBB\xBF");

                fwrite($htmlFile, "<!DOCTYPE html>");
                fwrite($htmlFile, "<html lang=\"en\">");
                fwrite($htmlFile, "<head>");
                fwrite($htmlFile, "<meta charset=\"UTF-8\">");
                fwrite($htmlFile, "<title>" . htmlspecialchars($basePath . '_' . $sheetName) . "</title>");
                fwrite($htmlFile, "<style>" . $style . "</style>");
                fwrite($htmlFile, "</head>");
                fwrite($htmlFile, "<body>");
                fwrite($htmlFile, "<div class='table-container'>");
                fwrite($htmlFile, "<table>");
                fwrite($htmlFile, "<caption>" . htmlspecialchars($sheetName) . "</caption>");

                // Получаем итератор строк для текущего листа
                $rowIterator = $sheet->getRowIterator();

                // Обрабатываем каждую строку
                foreach ($rowIterator as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);

                    fwrite($htmlFile, "<tr>");

                    // Обрабатываем каждую ячейку в строке
                    foreach ($cellIterator as $cell) {
                        $value = $cell->getValue();

                        if ($value === null) {
                            $value = "";
                        } else {
                            // Если ячейка содержит формулу, получаем вычисленное значение
                            if ($cell->getDataType() == 'f') {
                                $value = $cell->getCalculatedValue();
                            }
                        }

                        // Определяем, заголовок это или обычная ячейка
                        if ($row->getRowIndex() == 1) {
                            fwrite($htmlFile, "<th>");
                        } else {
                            fwrite($htmlFile, "<td>");
                        }

                        // Экранируем HTML-символы и записываем значение
                        fwrite($htmlFile, htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                        // Закрываем ячейку
                        if ($row->getRowIndex() == 1) {
                            fwrite($htmlFile, "</th>");
                        } else {
                            fwrite($htmlFile, "</td>");
                        }
                    }

                    fwrite($htmlFile, "</tr>");
                }

                fwrite($htmlFile, "</table>");
                fwrite($htmlFile, "</div>");
                fwrite($htmlFile, "</body>");
                fwrite($htmlFile, "</html>");

                // Закрываем HTML файл
                fclose($htmlFile);
            }

            // Освобождаем память
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            return true;

        } catch (\Throwable $th) {
            echo "Error while converting file: " . $th->getMessage();
            return false;
        }
    }

    //convert xls/xlsx to csv
    public function convertExcelToCsvOptimized(string $xlsxFilePath, string $delimiter = ',', string $enclosure = ''): bool
    {
        try {
            if (!file_exists($xlsxFilePath)) {
                throw new \Exception("File not found: " . $xlsxFilePath);
            }

            if (strrchr($xlsxFilePath, '.') == ".xlsx") {
                $reader = IOFactory::createReader('Xlsx');
            } else {
                $reader = IOFactory::createReader('Xls');
            }

            // Настройка читателя
            $reader->setReadDataOnly(true); // Игнорируем стили для скорости

            // Загружаем исходный XLSX файл
            $sourceSpreadsheet = $reader->load($xlsxFilePath);

            // Обрабатываем каждый лист
            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                $sheetName = $sourceSheet->getTitle();
                $sheetName = preg_replace('/[\/:*?"<>|]/', '_', $sheetName);

                $basePath = substr($xlsxFilePath, 0, strrpos($xlsxFilePath, '.'));
                $csvFilePath = $basePath . '_' . $sheetName . '.csv';

                $highestRow = $sourceSheet->getHighestDataRow(); // Последняя строка с данными
                $highestColumn = $sourceSheet->getHighestDataColumn();

                if ($highestRow == 1 && $sourceSheet->getCell('A1')->getValue() === null) {
                    // Лист полностью пустой - пропускаем
                    continue;
                }

                $data = $sourceSheet->rangeToArray(
                    'A1:' . $highestColumn . $highestRow,
                    null,
                    true,
                    false
                );

                $csvFile = fopen($csvFilePath, 'w');

                fwrite($csvFile, "\xEF\xBB\xBF");

                for ($i = 0; $i < count($data[0]); $i++) {
                    if ($data[0][$i] === null) {
                        $data[0][$i] = 0;
                        fputcsv($csvFile, $data[0], $delimiter);
                    }
                }

                for ($i = 1; $i < count($data); $i++) {
                    fputcsv($csvFile, $data[$i], $delimiter);
                }
                fclose($csvFile);
            }

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet);
            return true;

        } catch (\Throwable $th) {
            error_log("Error during conversion: " . $th->getMessage());
            return false;
        }
    }
}