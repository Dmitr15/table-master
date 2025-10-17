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
    protected $name;
    protected $typeOfConversation;
    protected $fileMetaData;


    /**
     * Create a new job instance.
     */
    public function __construct(UserFile $fileMetaData, string $original_name, string $path, string $type)
    {
        $this->fileMetaData = $fileMetaData;
        $this->original_name = $original_name;
        $this->path = $path;
        $this->typeOfConversation = $type;
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
                //$this->xlsToXlsx();
                break;
            case 'excelToOds':
                Log::info('Function excelToOds was started successfully');
                //$this->excelToOds();
                break;
            case 'excelToCsv':
                Log::info('Function excelToCsv was started successfully');
                //$this->excelToCsv();
                break;
            case 'convertExcelToHtmlViaSpout':
                Log::info('Function convertExcelToHtmlViaSpout was started successfully');
                //$this->convertExcelToHtmlViaSpout();
                break;

            default:
                # code...
                break;
        }
    }

    private function xlsxToXls()
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

            //$outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '.xls';
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
            Log::info('Function copySheetProperties was started successfully');
            $newSheet->getPageSetup()->setOrientation($sourceSheet->getPageSetup()->getOrientation());
            $newSheet->getPageSetup()->setPaperSize($sourceSheet->getPageSetup()->getPaperSize());

            $newSheet->getPageMargins()->setTop($sourceSheet->getPageMargins()->getTop());
            $newSheet->getPageMargins()->setRight($sourceSheet->getPageMargins()->getRight());
            $newSheet->getPageMargins()->setLeft($sourceSheet->getPageMargins()->getLeft());
            $newSheet->getPageMargins()->setBottom($sourceSheet->getPageMargins()->getBottom());

            $newSheet->getSheetView()->setZoomScale($sourceSheet->getSheetView()->getZoomScale());
            Log::info('Function copySheetProperties was finished successfully');
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
            Log::info('Function getSafeCellValue was started successfully');
            $value = $cell->getValue();

            if (is_array($value)) {
                return isset($value[0]) ? $value[0] : null;
            }

            if (is_object($value) && method_exists($value, '__toString')) {
                return $value->__toString();
            }

            Log::info('Function getSafeCellValue was finished successfully');
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
        Log::info('Function applyRowStylesOptimized was started successfully');
        foreach ($styles as $col => $styleArray) {
            if (empty($styleArray))
                continue;

            try {
                $sheet->getStyle($col . $rowNumber)->applyFromArray($styleArray);
                Log::info('Function applyRowStylesOptimized was finished successfully');
            } catch (\Exception $e) {
                Log::error('Function applyRowStylesOptimized was finished unsuccessfully: Apply row styles from array error: ' . $e->getMessage());
            }
        }
    }

    private function safeUnlink(string $path): void
    {
        try {
            Log::info('Function safeUnlink was started successfully');
            if (is_file($path)) { // Проверяем, что это файл, а не директория
                if (@unlink($path)) { // Подавляем предупреждение, но проверяем результат
                    Log::info("File successfully deleted: {$path}");
                } else {
                    Log::warning("Failed to delete file: {$path}");
                }
            }
            Log::info('Function safeUnlink was finished successfully');
        } catch (\Exception $e) {
            Log::error("Function safeUnlink was finished unsuccessfully: Error deleting file {$path}: " . $e->getMessage());
        }
    }

}
