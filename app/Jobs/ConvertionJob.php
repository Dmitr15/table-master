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
    protected $mergedFilePath;



    /**
     * Create a new job instance.
     */
    public function __construct(UserFile $fileMetaData, string $original_name, string $path, string $type, string $delimiter = ',', string $mergeFilePath = '')
    {
        $this->fileMetaData = $fileMetaData;
        $this->original_name = $original_name;
        $this->path = $path;
        $this->typeOfConversation = $type;
        $this->delimiter = $delimiter;
        $this->mergedFilePath = $mergeFilePath;
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
                Log::info('Unknown function found!');
                break;
        }
    }

    // private function merge(): void
    // {
    //     $originalMemoryLimit = ini_get('memory_limit');
    //     $tempFilePath = null;
    //     $tempOutputPath = null;

    //     try {
    //         ini_set('memory_limit', '1024M');

    //         // Проверяем, что путь к файлу для слияния существует
    //         if (empty($this->mergedFilePath) || !Storage::disk('local')->exists($this->mergedFilePath)) {
    //             $this->fileMetaData->update(["status" => "failed"]);
    //             throw new \Exception("Merge file not found or path is empty");
    //         }

    //         $fileContent = Storage::disk('local')->get($this->path);
    //         $this->fileMetaData->update(["status" => "processing"]);

    //         // Получаем расширения файлов
    //         $sourceExtension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
    //         $mergeExtension = strtolower(pathinfo($this->mergedFilePath, PATHINFO_EXTENSION));

    //         $tempFilePath = tempnam(sys_get_temp_dir(), 'merge_source_') . '.' . $sourceExtension;
    //         file_put_contents($tempFilePath, $fileContent);

    //         // Получаем содержимое файла для слияния, второй файл
    //         $mergeFileContent = Storage::disk('local')->get($this->mergedFilePath);
    //         $mergeTempFilePath = tempnam(sys_get_temp_dir(), 'merge_target_') . '.' . $mergeExtension;
    //         file_put_contents($mergeTempFilePath, $mergeFileContent);

    //         unset($fileContent);

    //         Log::info('Merge: Temp files created', [
    //             'source' => $tempFilePath,
    //             'merge' => $mergeTempFilePath,
    //             'source_extension' => $sourceExtension,
    //             'merge_extension' => $mergeExtension,
    //         ]);

    //         $sourceReader = ($sourceExtension === "xlsx") ?
    //             IOFactory::createReader('Xlsx') :
    //             IOFactory::createReader('Xls');

    //         $mergeReader = ($mergeExtension === "xlsx") ?
    //             IOFactory::createReader('Xlsx') :
    //             IOFactory::createReader('Xls');

    //         Log::info('Readers was created');

    //         // Загружаем оба файла
    //         $sourceSpreadsheet = $sourceReader->load($tempFilePath);
    //         $mergeSpreadsheet = $mergeReader->load($mergeTempFilePath);

    //         Log::info('Merge: Files loaded', [
    //             'source_sheets' => $sourceSpreadsheet->getSheetCount(),
    //             'merge_sheets' => $mergeSpreadsheet->getSheetCount()
    //         ]);

    //         // Копируем все листы из файла для слияния в исходный файл
    //         foreach ($mergeSpreadsheet->getAllSheets() as $mergeSheet) {
    //             $sheetName = $this->getUniqueSheetName($sourceSpreadsheet, $mergeSheet->getTitle());

    //             Log::info('Merge: Copying sheet', [
    //                 'original_name' => $mergeSheet->getTitle(),
    //                 'new_name' => $sheetName
    //             ]);

    //             // Создаем новый лист в исходном файле
    //             $newSheet = new Worksheet($sourceSpreadsheet, $sheetName);
    //             $sourceSpreadsheet->addSheet($newSheet);

    //             // Копируем содержимое
    //             $this->copySheetProperties($mergeSheet, $newSheet);
    //             $this->copyMergedCells($mergeSheet, $newSheet);
    //             $this->copyDimensions($mergeSheet, $newSheet);
    //             $this->copyCellsWithArrayStyles($mergeSheet, $newSheet);

    //             gc_collect_cycles();
    //         }

    //         // Сохраняем объединенный файл во временный файл
    //         $outputExtension = $sourceExtension; // Сохраняем в формате исходного файла
    //         $tempOutputPath = tempnam(sys_get_temp_dir(), 'merge_output_') . '.' . $outputExtension;

    //         $outputWriter = ($outputExtension === "xlsx") ?
    //             IOFactory::createWriter($sourceSpreadsheet, 'Xlsx') :
    //             IOFactory::createWriter($sourceSpreadsheet, 'Xls');

    //         $sourceSpreadsheet->disconnectWorksheets();
    //         $mergeSpreadsheet->disconnectWorksheets();
    //         unset($sourceSpreadsheet, $mergeSpreadsheet);
    //         gc_collect_cycles();

    //         $outputWriter->save($tempOutputPath);

    //         unset($outputWriter);

    //         // Сохраняем в storage
    //         $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '_merged_' . uniqid() . '.' . $outputExtension;
    //         $storagePath = 'converted_files/' . $outputFileName;

    //         // Создаем директорию если не существует
    //         if (!Storage::disk('local')->exists('converted_files')) {
    //             Storage::disk('local')->makeDirectory('converted_files');
    //         }

    //         // Сохраняем в Storage
    //         Storage::disk('local')->put($storagePath, file_get_contents($tempOutputPath));

    //         // Проверяем что файл сохранен в storage
    //         if (!Storage::disk('local')->exists($storagePath)) {
    //             Log::error('Function merge: File not saved to storage', ['path' => $storagePath]);
    //             $this->fileMetaData->update(["status" => "failed"]);
    //             throw new \Exception("Function merge: File not saved to storage");
    //         }

    //         $fileSize = Storage::disk('local')->size($storagePath);
    //         Log::info('Function merge: File saved to storage', [
    //             'storage_path' => $storagePath,
    //             'file_size' => $fileSize
    //         ]);

    //         // Обновляем статус
    //         $this->fileMetaData->update([
    //             "status" => "completed",
    //             "output_path" => $storagePath // Сохраняем относительный путь
    //         ]);

    //         Log::info('Merge: Operation completed successfully', [
    //             'merged_file_name' => $outputFileName,
    //             'output_path' => $storagePath,
    //             //'total_sheets' => $sourceSpreadsheet->getSheetCount()
    //         ]);

    //         // Освобождаем память
    //         // $sourceSpreadsheet->disconnectWorksheets();
    //         // $mergeSpreadsheet->disconnectWorksheets();
    //         // unset($sourceSpreadsheet, $mergeSpreadsheet);

    //     } catch (\Exception $e) {
    //         $this->fileMetaData->update(["status" => "failed"]);
    //         Log::error('Merge: Error: ' . $e->getMessage());
    //         throw $e;
    //     } finally {
    //         if ($originalMemoryLimit) {
    //             ini_set('memory_limit', $originalMemoryLimit);
    //         }
    //         //$this->safeCleanup($tempFilePath, $mergeTempFilePath, $tempOutputPath);
    //         $this->safeUnlink($tempFilePath);
    //         $this->safeUnlink($mergeTempFilePath);
    //         $this->safeUnlink($tempOutputPath);

    //         // Удаляем временный merge файл из storage (если он был загружен)
    //         if (!empty($this->mergedFilePath) && Storage::disk('local')->exists($this->mergedFilePath)) {
    //             try {
    //                 Storage::disk('local')->delete($this->mergedFilePath);
    //             } catch (\Exception $e) {
    //                 Log::warning('Failed to delete merge file from storage: ' . $e->getMessage());
    //             }
    //         }
    //         gc_collect_cycles();
    //     }
    // }


    // /**
    //  * Очистка spreadsheet объектов
    //  */
    private function cleanupSpreadsheets(array $spreadsheets): void
    {
        foreach ($spreadsheets as $spreadsheet) {
            if ($spreadsheet instanceof Spreadsheet) {
                try {
                    $spreadsheet->disconnectWorksheets();
                    unset($spreadsheet);
                } catch (\Exception $e) {
                    Log::warning('Error cleaning up spreadsheet: ' . $e->getMessage());
                }
            }
        }
    }

    // /**
    //  * Очистка временных файлов
    //  */
    private function cleanupTempFiles(array $files): void
    {
        foreach ($files as $type => $file) {
            if ($file && file_exists($file)) {
                $this->safeUnlink($file);
                Log::debug('Temp file deleted', ['type' => $type, 'file' => $file]);
            }
        }
    }

    // /**
    //  * Очистка merge файла из storage
    //  */
    private function cleanupMergeFile(): void
    {
        if (!empty($this->mergedFilePath) && Storage::disk('local')->exists($this->mergedFilePath)) {
            try {
                Storage::disk('local')->delete($this->mergedFilePath);
                Log::info('Merge file deleted from storage', ['path' => $this->mergedFilePath]);
            } catch (\Exception $e) {
                Log::warning('Failed to delete merge file from storage: ' . $e->getMessage());
            }
        }
    }

    // /**
    //  * Умная сборка мусора
    //  */
    private function forceGarbageCollection(): void
    {
        $currentUsage = memory_get_usage(true);

        // Собираем мусор только если использование памяти превышает 50MB
        if ($currentUsage > 50 * 1024 * 1024) {
            $cycles = gc_collect_cycles();
            Log::debug('Garbage collection performed', [
                'cycles_collected' => $cycles,
                'memory_before' => $currentUsage,
                'memory_after' => memory_get_usage(true)
            ]);
        }
    }

    // /**
    //  * Создание директории converted_files
    //  */
    private function ensureConvertedFilesDirectory(): void
    {
        if (!Storage::disk('local')->exists('converted_files')) {
            Storage::disk('local')->makeDirectory('converted_files');
        }
    }

    // /**
    //  * Обновление статуса обработки
    //  */
    private function markAsProcessing(): void
    {
        $this->fileMetaData->update(["status" => "processing"]);
        Log::info('Merge: Status updated to processing');
    }

    // /**
    //  * Обновление статуса завершения
    //  */
    private function markAsCompleted(string $outputPath): void
    {
        $this->fileMetaData->update([
            "status" => "completed",
            "output_path" => $outputPath
        ]);
        Log::info('Merge: Status updated to completed', ['output_path' => $outputPath]);
    }

    // /**
    //  * Обновление статуса ошибки
    //  */
    private function markAsFailed(string $message): void
    {
        $this->fileMetaData->update(["status" => "failed"]);
        Log::error('Merge: Status updated to failed', ['reason' => $message]);
    }

    private function merge(): void
    {
        $originalMemoryLimit = ini_get('memory_limit');
        $tempFiles = [];
        $sourceSpreadsheet = null;
        $mergeSpreadsheet = null;

        try {
            // Безопасное управление памятью
            $this->setMemoryLimitSafely('1024M');

            // Проверяем файл для слияния
            if (empty($this->mergedFilePath) || !Storage::disk('local')->exists($this->mergedFilePath)) {
                $this->markAsFailed("Merge file not found or path is empty");
                throw new \Exception("Merge file not found or path is empty");
            }

            $this->markAsProcessing();

            Log::info('Merge: Starting merge operation', [
                'source_file' => $this->path,
                'merge_file' => $this->mergedFilePath
            ]);

            // Создаем временные файлы с оптимизацией памяти
            $tempFiles['source'] = $this->createTempFileFromStorage($this->path, 'merge_source_');
            $tempFiles['merge'] = $this->createTempFileFromStorage($this->mergedFilePath, 'merge_target_');

            Log::info('Merge: Temp files created', [
                'source' => $tempFiles['source'],
                'merge' => $tempFiles['merge'],
                'memory_usage' => memory_get_usage(true)
            ]);

            // Загружаем файлы с оптимизированными настройками
            $sourceSpreadsheet = $this->loadSpreadsheetOptimized($tempFiles['source']);
            $mergeSpreadsheet = $this->loadSpreadsheetOptimized($tempFiles['merge']);

            Log::info('Merge: Files loaded successfully', [
                'source_sheets' => $sourceSpreadsheet->getSheetCount(),
                'merge_sheets' => $mergeSpreadsheet->getSheetCount(),
                'memory_usage' => memory_get_usage(true)
            ]);

            // Оптимизированное копирование листов
            $this->copySheetsWithMemoryManagement($mergeSpreadsheet, $sourceSpreadsheet);

            Log::info('Merge: Sheets copied successfully', [
                'memory_usage' => memory_get_usage(true)
            ]);

            // Сохраняем результат с оптимизацией
            $storagePath = $this->saveMergedResult($sourceSpreadsheet);

            $this->markAsCompleted($storagePath);

            Log::info('Merge: Operation completed successfully', [
                'output_path' => $storagePath,
                'file_size' => Storage::disk('local')->size($storagePath),
                'final_memory' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true)
            ]);

        } catch (\Exception $e) {
            $this->markAsFailed($e->getMessage());
            Log::error('Merge: Error: ' . $e->getMessage(), [
                'memory_at_error' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true)
            ]);
            throw $e;
        } finally {
            // Приоритетная очистка памяти
            $this->cleanupSpreadsheets([$sourceSpreadsheet, $mergeSpreadsheet]);
            $this->restoreMemoryLimitSafely($originalMemoryLimit);
            $this->cleanupTempFiles($tempFiles);
            $this->cleanupMergeFile();
            $this->forceGarbageCollection();
        }
    }

    /**
     * Оптимизированное копирование ячеек и стилей ВСЕХ ячеек в диапазоне
     */
    private function copyCellsAndStylesOptimized(Worksheet $sourceSheet, Worksheet $targetSheet): void
    {
        // Используем getHighestRow() и getHighestColumn() вместо getHighestDataRow/Column
        // чтобы охватить ВСЕ ячейки, включая те, что имеют только стили
        $highestRow = $sourceSheet->getHighestRow();
        $highestColumn = $sourceSheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        Log::info('Starting complete cell and style copying', [
            'rows' => $highestRow,
            'columns' => $highestColumn,
            'total_cells' => $highestRow * $highestColumnIndex
        ]);

        // Определяем оптимальный размер батча
        $batchSize = $this->calculateOptimalBatchSize($highestRow * $highestColumnIndex);
        $processedRows = 0;

        for ($row = 1; $row <= $highestRow; $row += $batchSize) {
            $endRow = min($row + $batchSize - 1, $highestRow);

            // Обрабатываем батч строк - копируем и данные, и стили для ВСЕХ ячеек
            $this->processCompleteRowBatch($sourceSheet, $targetSheet, $row, $endRow, $highestColumnIndex);

            $processedRows += ($endRow - $row + 1);

            Log::debug('Processed complete row batch', [
                'rows' => "$row-$endRow",
                'total_processed' => $processedRows,
                'memory_usage' => memory_get_usage(true)
            ]);

            // Очистка памяти после каждого батча
            $this->forceGarbageCollection();
        }

        Log::info('Complete cell and style copying finished', [
            'total_rows_processed' => $processedRows
        ]);
    }

    /**
     * Обработка батча строк с копированием ВСЕХ ячеек и их стилей
     */
    private function processCompleteRowBatch(Worksheet $sourceSheet, Worksheet $targetSheet, int $startRow, int $endRow, int $highestColumnIndex): void
    {
        for ($currentRow = $startRow; $currentRow <= $endRow; $currentRow++) {
            $rowData = [];
            $rowHasData = false;

            // Собираем данные для всей строки
            for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $cellCoordinate = $col . $currentRow;

                $sourceCell = $sourceSheet->getCell($cellCoordinate);
                $cellValue = $this->getSafeCellValue($sourceCell);

                $rowData[] = $cellValue === null ? '' : $cellValue;

                // Проверяем, есть ли в строке хотя бы одна непустая ячейка
                if (!$rowHasData && $cellValue !== null && $cellValue !== '') {
                    $rowHasData = true;
                }
            }

            // Записываем данные строки (даже если строка полностью пустая)
            // Это важно для сохранения форматирования пустых ячеек
            $targetSheet->fromArray($rowData, null, 'A' . $currentRow);

            // Копируем стили для ВСЕХ ячеек в строке
            $this->copyAllStylesForRow($sourceSheet, $targetSheet, $currentRow, $highestColumnIndex);

            unset($rowData);
        }
    }

    /**
     * Копирование стилей для ВСЕХ ячеек в строке (включая пустые)
     */
    private function copyAllStylesForRow(Worksheet $sourceSheet, Worksheet $targetSheet, int $rowNumber, int $highestColumnIndex): void
    {
        for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $cellCoordinate = $col . $rowNumber;

            $sourceCell = $sourceSheet->getCell($cellCoordinate);

            // Копируем стили для ЛЮБОЙ ячейки, независимо от содержимого
            try {
                $styleArray = $sourceCell->getStyle()->exportArray();

                // Применяем стиль только если массив стилей не пустой
                if (!empty(array_filter($styleArray))) {
                    $targetSheet->getStyle($cellCoordinate)->applyFromArray($styleArray);
                }

                unset($styleArray);
            } catch (\Exception $e) {
                Log::warning('Style copy error for cell ' . $cellCoordinate . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Улучшенное копирование содержимого листа с полным сохранением стилей
     */
    private function copySheetContentOptimized(Worksheet $sourceSheet, Worksheet $targetSheet): void
    {
        try {
            // 1. Копируем основные свойства листа
            $this->copySheetProperties($sourceSheet, $targetSheet);

            // 2. Копируем объединенные ячейки
            $this->copyMergedCells($sourceSheet, $targetSheet);

            // 3. Копируем размеры столбцов и строк
            $this->copyDimensions($sourceSheet, $targetSheet);

            // 4. Копируем ВСЕ ячейки и их стили в полном диапазоне
            $this->copyCellsAndStylesOptimized($sourceSheet, $targetSheet);

            // 5. Копируем дополнительные свойства, которые могли быть пропущены
            $this->copyAdditionalSheetProperties($sourceSheet, $targetSheet);

        } catch (\Exception $e) {
            Log::error('Error copying sheet content: ' . $e->getMessage(), [
                'source_sheet' => $sourceSheet->getTitle()
            ]);
            throw $e;
        }
    }

    /**
     * Копирование дополнительных свойств листа
     */
    private function copyAdditionalSheetProperties(Worksheet $sourceSheet, Worksheet $targetSheet): void
    {
        try {
            // Копируем настройки закрепления областей
            if ($sourceSheet->getFreezePane()) {
                $targetSheet->freezePane($sourceSheet->getFreezePane());
            }

            // Копируем настройки защиты листа
            $targetSheet->getProtection()->setPassword($sourceSheet->getProtection()->getPassword());
            $targetSheet->getProtection()->setObjects($sourceSheet->getProtection()->getObjects());
            $targetSheet->getProtection()->setScenarios($sourceSheet->getProtection()->getScenarios());
            $targetSheet->getProtection()->setFormatCells($sourceSheet->getProtection()->getFormatCells());
            $targetSheet->getProtection()->setFormatColumns($sourceSheet->getProtection()->getFormatColumns());
            $targetSheet->getProtection()->setFormatRows($sourceSheet->getProtection()->getFormatRows());
            $targetSheet->getProtection()->setInsertColumns($sourceSheet->getProtection()->getInsertColumns());
            $targetSheet->getProtection()->setInsertRows($sourceSheet->getProtection()->getInsertRows());
            $targetSheet->getProtection()->setInsertHyperlinks($sourceSheet->getProtection()->getInsertHyperlinks());
            $targetSheet->getProtection()->setDeleteColumns($sourceSheet->getProtection()->getDeleteColumns());
            $targetSheet->getProtection()->setDeleteRows($sourceSheet->getProtection()->getDeleteRows());
            $targetSheet->getProtection()->setSelectLockedCells($sourceSheet->getProtection()->getSelectLockedCells());
            $targetSheet->getProtection()->setSort($sourceSheet->getProtection()->getSort());
            $targetSheet->getProtection()->setAutoFilter($sourceSheet->getProtection()->getAutoFilter());
            $targetSheet->getProtection()->setPivotTables($sourceSheet->getProtection()->getPivotTables());
            $targetSheet->getProtection()->setSelectUnlockedCells($sourceSheet->getProtection()->getSelectUnlockedCells());

        } catch (\Exception $e) {
            Log::warning('Error copying additional sheet properties: ' . $e->getMessage());
        }
    }

    /**
     * Безопасное управление памятью
     */
    private function setMemoryLimitSafely(string $limit): void
    {
        $currentUsage = memory_get_usage(true);
        $newLimitBytes = $this->convertToBytes($limit);

        if ($currentUsage * 1.2 < $newLimitBytes) {
            ini_set('memory_limit', $limit);
            Log::info('Memory limit increased', ['to' => $limit]);
        } else {
            Log::warning('Memory limit not increased - current usage too high', [
                'current_usage' => $currentUsage,
                'requested_limit' => $newLimitBytes
            ]);
        }
    }

    /**
     * Безопасное восстановление лимита памяти
     */
    private function restoreMemoryLimitSafely(string $originalLimit): void
    {
        $currentUsage = memory_get_usage(true);
        $originalBytes = $this->convertToBytes($originalLimit);

        if ($currentUsage < $originalBytes * 0.7) {
            ini_set('memory_limit', $originalLimit);
        } else {
            Log::warning('Memory limit not restored - current usage too high', [
                'current_usage' => $currentUsage,
                'original_limit' => $originalBytes
            ]);
        }
    }

    /**
     * Конвертация размера памяти в байты
     */
    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '-1')
            return -1;

        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    /**
     * Создание временного файла из storage
     */
    private function createTempFileFromStorage(string $storagePath, string $prefix): string
    {
        $extension = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));
        $tempPath = tempnam(sys_get_temp_dir(), $prefix) . '.' . $extension;

        $stream = Storage::disk('local')->readStream($storagePath);
        $tempHandle = fopen($tempPath, 'w');

        if ($stream && $tempHandle) {
            stream_copy_to_stream($stream, $tempHandle);
            fclose($stream);
            fclose($tempHandle);
        } else {
            $fileContent = Storage::disk('local')->get($storagePath);
            file_put_contents($tempPath, $fileContent);
            unset($fileContent);
        }

        return $tempPath;
    }

    /**
     * Оптимизированная загрузка spreadsheet
     */
    private function loadSpreadsheetOptimized(string $filePath): Spreadsheet
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $reader = ($extension === "xlsx") ?
            IOFactory::createReader('Xlsx') :
            IOFactory::createReader('Xls');

        $reader->setReadEmptyCells(true); // Важно: загружаем ВСЕ ячейки
        $reader->setIncludeCharts(false);

        if (method_exists($reader, 'setReadDataOnly')) {
            $reader->setReadDataOnly(false); // Нам нужны стили
        }

        return $reader->load($filePath);
    }

    /**
     * Копирование листов с управлением памятью
     */
    private function copySheetsWithMemoryManagement(Spreadsheet $source, Spreadsheet $target): void
    {
        $sheetCount = $source->getSheetCount();
        $processedSheets = 0;

        Log::info('Starting sheet copying process', ['total_sheets' => $sheetCount]);

        foreach ($source->getAllSheets() as $sourceSheet) {
            $sheetName = $this->getUniqueSheetName($target, $sourceSheet->getTitle());

            Log::info('Merge: Processing sheet', [
                'original_name' => $sourceSheet->getTitle(),
                'new_name' => $sheetName,
                'memory_before' => memory_get_usage(true)
            ]);

            $newSheet = new Worksheet($target, $sheetName);
            $target->addSheet($newSheet);

            // Копируем содержимое с полным сохранением стилей
            $this->copySheetContentOptimized($sourceSheet, $newSheet);

            $processedSheets++;

            Log::info('Merge: Sheet processed', [
                'progress' => "$processedSheets/$sheetCount",
                'memory_after' => memory_get_usage(true)
            ]);

            // Управление памятью
            if ($processedSheets % 2 === 0) {
                $this->forceGarbageCollection();
            }
        }
    }

    /**
     * Расчет оптимального размера батча
     */
    private function calculateOptimalBatchSize(int $totalCells): int
    {
        if ($totalCells > 100000)
            return 5;
        if ($totalCells > 50000)
            return 10;
        if ($totalCells > 10000)
            return 25;
        return 50;
    }

    /**
     * Сохранение объединенного результата
     */
    private function saveMergedResult(Spreadsheet $spreadsheet): string
    {
        $outputExtension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

        Log::info('Saving merged result', [
            'format' => $outputExtension,
            'sheet_count' => $spreadsheet->getSheetCount()
        ]);

        $tempOutputPath = tempnam(sys_get_temp_dir(), 'merge_output_') . '.' . $outputExtension;

        $writer = ($outputExtension === "xlsx") ?
            IOFactory::createWriter($spreadsheet, 'Xlsx') :
            IOFactory::createWriter($spreadsheet, 'Xls');

        if (method_exists($writer, 'setPreCalculateFormulas')) {
            $writer->setPreCalculateFormulas(false);
        }

        $writer->save($tempOutputPath);
        unset($writer);

        $this->cleanupSpreadsheets([$spreadsheet]);

        if (!file_exists($tempOutputPath)) {
            throw new \Exception("Merged output file was not created");
        }

        $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '_merged_' . uniqid() . '.' . $outputExtension;
        $storagePath = 'converted_files/' . $outputFileName;

        $this->ensureConvertedFilesDirectory();

        $outputStream = fopen($tempOutputPath, 'r');
        Storage::disk('local')->put($storagePath, $outputStream);
        if (is_resource($outputStream)) {
            fclose($outputStream);
        }

        $this->safeUnlink($tempOutputPath);

        if (!Storage::disk('local')->exists($storagePath)) {
            throw new \Exception("Merged file not saved to storage");
        }

        return $storagePath;
    }

    /**
     * Генерирует уникальное имя для листа, чтобы избежать конфликтов
     */
    private function getUniqueSheetName($spreadsheet, $baseName): string
    {
        $name = $baseName;
        $counter = 1;

        while ($spreadsheet->getSheetByName($name) !== null) {
            $name = $baseName . '_' . $counter;
            $counter++;

            if ($counter > 500) {
                throw new \Exception("Cannot generate unique sheet name for: " . $baseName);
            }
        }

        return $name;
    }

    // private function split(): void
    // {
    //     $fileContent = Storage::disk('local')->get($this->path);
    //     $this->fileMetaData->update(["status" => "processing"]);

    //     $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
    //     file_put_contents($tempFilePath, $fileContent);
    //     Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

    //     $zipTempPath = null;
    //     $tempSheetFiles = [];

    //     try {
    //         $fileExtension = pathinfo($this->original_name, PATHINFO_EXTENSION);
    //         $reader = ($fileExtension === "xlsx") ?
    //             IOFactory::createReader('Xlsx') :
    //             IOFactory::createReader('Xls');

    //         $sourceSpreadsheet = $reader->load($tempFilePath);

    //         // Создаем временный ZIP файл
    //         $zipTempPath = tempnam(sys_get_temp_dir(), 'split_zip_') . '.zip';
    //         $zip = new \ZipArchive();

    //         if ($zip->open($zipTempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
    //             foreach ($sourceSpreadsheet->getAllSheets() as $sheet) {
    //                 // Создаем новый Excel документ для каждого листа
    //                 $newSpreadsheetForSheet = new Spreadsheet();
    //                 $newSpreadsheetForSheet->removeSheetByIndex(0);

    //                 $newSheet = new Worksheet($newSpreadsheetForSheet, $sheet->getTitle());
    //                 $newSpreadsheetForSheet->addSheet($newSheet);

    //                 // Копируем содержимое и структуры
    //                 try {
    //                     $this->copyMergedCells($sheet, $newSheet);
    //                     $this->copyDimensions($sheet, $newSheet);
    //                     $this->copyCellsWithArrayStyles($sheet, $newSheet);
    //                 } catch (\Exception $e) {
    //                     Log::warning('Split: warning while copying sheet content: ' . $e->getMessage(), ['sheet' => $sheet->getTitle()]);
    //                 }

    //                 // Сохраняем лист во временный файл
    //                 $sheetName = $this->sanitizeFilename($sheet->getTitle());
    //                 $tempSheetPath = tempnam(sys_get_temp_dir(), 'split_sheet_') . '.' . $fileExtension;

    //                 $writer = ($fileExtension === "xlsx") ?
    //                     IOFactory::createWriter($newSpreadsheetForSheet, 'Xlsx') :
    //                     IOFactory::createWriter($newSpreadsheetForSheet, 'Xls');

    //                 $writer->save($tempSheetPath);

    //                 // Добавляем файл в ZIP
    //                 $zip->addFile($tempSheetPath, $sheetName . '.' . $fileExtension);
    //                 $tempSheetFiles[] = $tempSheetPath;

    //                 // Освобождаем память
    //                 $newSpreadsheetForSheet->disconnectWorksheets();
    //                 unset($newSpreadsheetForSheet, $newSheet);

    //                 gc_collect_cycles();
    //             }

    //             $zip->close();
    //         } else {
    //             throw new \Exception("Cannot create ZIP archive for split operation");
    //         }

    //         // Сохраняем ZIP файл в storage
    //         $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '_split.zip';
    //         $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;

    //         // Создаем директорию если не существует
    //         if (!Storage::disk('local')->exists('converted_files')) {
    //             Storage::disk('local')->makeDirectory('converted_files');
    //         }

    //         // Сохраняем в storage
    //         Storage::disk('local')->put($storagePath, file_get_contents($zipTempPath));

    //         // Проверяем что файл сохранен в storage
    //         if (!Storage::disk('local')->exists($storagePath)) {
    //             Log::error('Function split: File not saved to storage', ['path' => $storagePath]);
    //             $this->fileMetaData->update(["status" => "failed"]);
    //             throw new \Exception("Function split: File not saved to storage");
    //         }

    //         $fileSize = Storage::disk('local')->size($storagePath);
    //         Log::info('Function split: File saved to storage', [
    //             'storage_path' => $storagePath,
    //             'file_size' => $fileSize
    //         ]);

    //         // Обновляем статус
    //         $this->fileMetaData->update([
    //             "status" => "completed",
    //             "output_path" => $storagePath
    //         ]);

    //         Log::info('Function split: Operation completed successfully', [
    //             'file_id' => $this->fileMetaData->id,
    //             'original_file' => $this->original_name,
    //             'output_file' => $storagePath,
    //             'sheet_count' => $sourceSpreadsheet->getSheetCount()
    //         ]);

    //         // Освобождаем память
    //         $sourceSpreadsheet->disconnectWorksheets();
    //         unset($sourceSpreadsheet);

    //     } catch (\Exception $e) {
    //         $this->fileMetaData->update(["status" => "failed"]);
    //         Log::error('Function split: Conversion error: ' . $e->getMessage());
    //         throw $e;
    //     } finally {
    //         $this->safeCleanup($tempFilePath, $zipTempPath);

    //         // Удаляем временные файлы листов
    //         foreach ($tempSheetFiles as $tempSheetFile) {
    //             $this->safeCleanup($tempSheetFile);
    //         }
    //     }
    // }

    private function split(): void
    {
        $originalMemoryLimit = ini_get('memory_limit');
        $tempFiles = [];
        $sourceSpreadsheet = null;

        try {
            // Безопасное управление памятью
            $this->setMemoryLimitSafely('1024M');

            $this->markAsProcessing();

            Log::info('Split: Starting split operation', [
                'source_file' => $this->path,
                'original_name' => $this->original_name
            ]);

            // Создаем временный файл с оптимизацией памяти
            $tempFiles['source'] = $this->createTempFileFromStorage($this->path, 'split_source_');

            Log::info('Split: Temp file created', [
                'source' => $tempFiles['source'],
                'memory_usage' => memory_get_usage(true)
            ]);

            // Загружаем исходный файл с оптимизацией
            $sourceSpreadsheet = $this->loadSpreadsheetOptimized($tempFiles['source']);
            $sheetCount = $sourceSpreadsheet->getSheetCount();

            Log::info('Split: Source file loaded', [
                'sheet_count' => $sheetCount,
                'memory_usage' => memory_get_usage(true)
            ]);

            // Создаем ZIP архив
            $zipTempPath = tempnam(sys_get_temp_dir(), 'split_zip_') . '.zip';
            $zip = new \ZipArchive();

            if ($zip->open($zipTempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception("Cannot create ZIP archive for split operation");
            }

            $processedSheets = 0;
            $tempSheetFiles = [];

            // Обрабатываем каждый лист с управлением памятью
            foreach ($sourceSpreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
                Log::info('Split: Processing sheet', [
                    'sheet_name' => $sheetName,
                    'sheet_index' => $sheetIndex,
                    'progress' => ($processedSheets + 1) . '/' . $sheetCount,
                    'memory_before' => memory_get_usage(true)
                ]);

                // Создаем отдельный файл для листа
                $sheetFilePath = $this->createSingleSheetFile($sourceSpreadsheet, $sheetIndex, $sheetName);
                $tempSheetFiles[] = $sheetFilePath;

                // Добавляем в ZIP
                $safeSheetName = $this->sanitizeFilename($sheetName);
                $zip->addFile($sheetFilePath, $safeSheetName . '.' . pathinfo($this->original_name, PATHINFO_EXTENSION));

                $processedSheets++;

                Log::info('Split: Sheet processed', [
                    'sheet_name' => $sheetName,
                    'memory_after' => memory_get_usage(true)
                ]);

                // Очистка памяти после каждого 2-го листа
                if ($processedSheets % 2 === 0) {
                    $this->forceGarbageCollection();
                }
            }

            $zip->close();
            Log::info('Split: ZIP archive created', [
                'zip_path' => $zipTempPath,
                'total_sheets' => $processedSheets
            ]);

            // Сохраняем ZIP в storage
            $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '_split.zip';
            $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;

            $this->ensureConvertedFilesDirectory();

            // Используем потоковое копирование для ZIP
            $zipStream = fopen($zipTempPath, 'r');
            Storage::disk('local')->put($storagePath, $zipStream);
            if (is_resource($zipStream)) {
                fclose($zipStream);
            }

            // Проверяем сохранение
            if (!Storage::disk('local')->exists($storagePath)) {
                throw new \Exception("Split ZIP file not saved to storage");
            }

            $fileSize = Storage::disk('local')->size($storagePath);
            Log::info('Split: File saved to storage', [
                'storage_path' => $storagePath,
                'file_size' => $fileSize
            ]);

            $this->markAsCompleted($storagePath);

            Log::info('Split: Operation completed successfully', [
                'file_id' => $this->fileMetaData->id,
                'original_file' => $this->original_name,
                'output_file' => $storagePath,
                'sheet_count' => $sheetCount,
                'final_memory' => memory_get_usage(true)
            ]);

        } catch (\Exception $e) {
            $this->markAsFailed($e->getMessage());
            Log::error('Split: Error: ' . $e->getMessage(), [
                'memory_at_error' => memory_get_usage(true)
            ]);
            throw $e;
        } finally {
            // Приоритетная очистка памяти
            $this->cleanupSpreadsheets([$sourceSpreadsheet]);
            $this->restoreMemoryLimitSafely($originalMemoryLimit);

            // Очистка временных файлов
            $this->cleanupTempFiles($tempFiles);
            if (isset($zipTempPath)) {
                $this->safeUnlink($zipTempPath);
            }
            if (isset($tempSheetFiles)) {
                foreach ($tempSheetFiles as $tempSheetFile) {
                    $this->safeUnlink($tempSheetFile);
                }
            }

            $this->forceGarbageCollection();
        }
    }

    /**
     * Создание отдельного файла для одного листа
     */
    private function createSingleSheetFile(Spreadsheet $sourceSpreadsheet, int $sheetIndex, string $sheetName): string
    {
        $extension = pathinfo($this->original_name, PATHINFO_EXTENSION);
        $tempSheetPath = tempnam(sys_get_temp_dir(), 'split_sheet_') . '.' . $extension;

        try {
            // Создаем новый spreadsheet только с одним листом
            $newSpreadsheet = new Spreadsheet();

            // Удаляем стандартный лист
            $newSpreadsheet->removeSheetByIndex(0);

            // Получаем исходный лист
            $sourceSheet = $sourceSpreadsheet->getSheet($sheetIndex);

            // Создаем новый лист с тем же именем
            $newSheet = new Worksheet($newSpreadsheet, $sheetName);
            $newSpreadsheet->addSheet($newSheet);

            // Оптимизированное копирование содержимого
            $this->copySheetContentOptimized($sourceSheet, $newSheet);

            // Сохраняем файл
            $writer = ($extension === "xlsx") ?
                IOFactory::createWriter($newSpreadsheet, 'Xlsx') :
                IOFactory::createWriter($newSpreadsheet, 'Xls');

            // Оптимизации для writer
            if (method_exists($writer, 'setPreCalculateFormulas')) {
                $writer->setPreCalculateFormulas(false);
            }

            $writer->save($tempSheetPath);

            // Очищаем память
            $newSpreadsheet->disconnectWorksheets();
            unset($newSpreadsheet, $writer);

            return $tempSheetPath;

        } catch (\Exception $e) {
            // Очистка в случае ошибки
            if (isset($newSpreadsheet)) {
                $newSpreadsheet->disconnectWorksheets();
            }
            $this->safeUnlink($tempSheetPath);
            throw new \Exception("Failed to create sheet file for '{$sheetName}': " . $e->getMessage());
        }
    }

    /**
     * Оптимизированное копирование данных и стилей для split операции
     */
    private function copyDataAndStylesForSplit(Worksheet $sourceSheet, Worksheet $targetSheet): void
    {
        $highestRow = $sourceSheet->getHighestRow();
        $highestColumn = $sourceSheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Автоматический подбор размера батча
        $batchSize = $this->calculateOptimalBatchSizeForSplit($highestRow, $highestColumnIndex);

        Log::debug('Split: Starting sheet data copy', [
            'sheet' => $sourceSheet->getTitle(),
            'rows' => $highestRow,
            'columns' => $highestColumn,
            'batch_size' => $batchSize
        ]);

        $processedRows = 0;

        for ($row = 1; $row <= $highestRow; $row += $batchSize) {
            $endRow = min($row + $batchSize - 1, $highestRow);

            // Обрабатываем батч строк
            $this->processBatchForSplit($sourceSheet, $targetSheet, $row, $endRow, $highestColumnIndex);

            $processedRows += ($endRow - $row + 1);

            Log::debug('Split: Processed row batch', [
                'sheet' => $sourceSheet->getTitle(),
                'rows' => "$row-$endRow",
                'total_processed' => $processedRows,
                'memory_usage' => memory_get_usage(true)
            ]);

            // Очистка памяти после каждого батча
            $this->forceGarbageCollection();
        }

        Log::debug('Split: Sheet data copy completed', [
            'sheet' => $sourceSheet->getTitle(),
            'total_rows' => $processedRows
        ]);
    }

    /**
     * Обработка батча строк для split операции
     */
    private function processBatchForSplit(Worksheet $sourceSheet, Worksheet $targetSheet, int $startRow, int $endRow, int $highestColumnIndex): void
    {
        for ($currentRow = $startRow; $currentRow <= $endRow; $currentRow++) {
            $rowData = [];
            $rowHasData = false;

            // Собираем данные строки
            for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $cellCoordinate = $col . $currentRow;

                $sourceCell = $sourceSheet->getCell($cellCoordinate);
                $cellValue = $this->getSafeCellValue($sourceCell);

                $rowData[] = $cellValue === null ? '' : $cellValue;

                // Отмечаем, если в строке есть данные
                if (!$rowHasData && $cellValue !== null && $cellValue !== '') {
                    $rowHasData = true;
                }
            }

            // Записываем данные строки (только если есть данные)
            if ($rowHasData) {
                $targetSheet->fromArray($rowData, null, 'A' . $currentRow);
            }

            // Копируем стили для ВСЕХ ячеек в строке
            $this->copyAllStylesForRow($sourceSheet, $targetSheet, $currentRow, $highestColumnIndex);

            unset($rowData);
        }
    }

    /**
     * Расчет оптимального размера батча для split операции
     */
    private function calculateOptimalBatchSizeForSplit(int $totalRows, int $totalColumns): int
    {
        $totalCells = $totalRows * $totalColumns;

        if ($totalCells > 50000)
            return 5;       // Очень большие листы
        if ($totalCells > 20000)
            return 10;      // Большие листы
        if ($totalCells > 5000)
            return 25;       // Средние листы
        return 50;                               // Малые листы
    }

    private function ExcelToHtml(): void
    {
        // Получаем содержимое файла и обновляем статус
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);

        $tempHtmlDir = null;
        $outputFilePath = null;

        try {
            // Определяем расширение исходного файла
            $extension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

            // Если файл не xlsx, конвертируем его во временный xlsx файл
            if ($extension !== 'xlsx') {
                $conversionResult = $this->convertToXlsx($tempFilePath);
                $tempFilePath = $conversionResult['path'];
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

            // Обрабатываем файл в зависимости от количества листов
            if ($sheetCount === 1) {
                $outputFilePath = $this->processSingleSheetHtml($tempFilePath, $tempHtmlDir);
            } else {
                $outputFilePath = $this->processMultipleSheetsToZipHtml($tempFilePath, $tempHtmlDir);
            }

            // Сохраняем результат в storage
            $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) .
                ($sheetCount === 1 ? '.html' : '_html.zip');
            $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;

            // Создаем директорию если не существует
            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            // Сохраняем в storage
            Storage::disk('local')->put($storagePath, file_get_contents($outputFilePath));

            // Проверяем что файл сохранен в storage
            if (!Storage::disk('local')->exists($storagePath)) {
                Log::error('Function ExcelToHtml: File not saved to storage', ['path' => $storagePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function ExcelToHtml: File not saved to storage");
            }

            $fileSize = Storage::disk('local')->size($storagePath);
            Log::info('Function ExcelToHtml: File saved to storage', [
                'storage_path' => $storagePath,
                'file_size' => $fileSize
            ]);

            // Обновляем метаданные файла с результатом конвертации
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath
            ]);

            Log::info('Function ExcelToHtml: Conversion completed successfully', [
                'file_id' => $this->fileMetaData->id,
                'original_file' => $this->original_name,
                'output_file' => $storagePath,
                'sheet_count' => $sheetCount
            ]);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function ExcelToHtml: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Очистка временных файлов/директорий
            $this->safeCleanup($tempFilePath, $outputFilePath);

            if (isset($tempHtmlDir) && is_dir($tempHtmlDir)) {
                $this->deleteDirectory($tempHtmlDir);
            }
        }
    }

    private function convertToXlsx(string $tempFilePath): array
    {
        $outputFilePath = null;

        try {
            // Настройка читателя
            $reader = IOFactory::createReader('Xls');
            $reader->setReadDataOnly(true);

            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый XLSX документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

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

            $outputFilePath = tempnam(sys_get_temp_dir(), 'converted_xlsx_') . '.xlsx';

            $writer = IOFactory::createWriter($newSpreadsheet, 'Xlsx');
            $writer->save($outputFilePath);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

            if (!file_exists($outputFilePath)) {
                throw new \Exception("XLSX conversion failed - output file not created");
            }

            return ['path' => $outputFilePath, 'name' => pathinfo($this->original_name, PATHINFO_FILENAME)];
        } catch (\Exception $e) {
            $this->safeCleanup($outputFilePath);
            Log::error('XLS to XLSX conversion error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processSingleSheetHtml(string $filePath, string $tempHtmlDir): string
    {
        $outputFilePath = $tempHtmlDir . DIRECTORY_SEPARATOR . 'output.html';
        $html = $this->getHtmlHeader(pathinfo($this->original_name, PATHINFO_FILENAME));

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

    private function processMultipleSheetsToZipHtml(string $filePath, string $tempHtmlDir): string
    {
        $zipFilePath = $tempHtmlDir . DIRECTORY_SEPARATOR . 'html_export.zip';
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
        font-family: Arial, sans-serif;
        color: #2d3748;
        background: white;
    }
    body { font-size: 12px; margin: 20px; }
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
        padding: 0.5rem;
        border-right: 1px solid #e2e8f0;
    }
    td {
        padding: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    tr:nth-of-type(even) { background-color: #f7fafc; }
    td:first-child { border-right: 1px solid #e2e8f0; }
    tr { transition: background-color 0.2s ease; }
    tr:hover { background-color: #ebf8ff; }";

        return "<!DOCTYPE html><html><head>\n<meta charset=\"UTF-8\">\n<title>" . htmlspecialchars($title) . "</title>\n<style>$styles</style>\n</head><body>\n<h1>" . htmlspecialchars($title) . "</h1>\n";
    }

    private function getHtmlFooter(): string
    {
        return "</body></html>";
    }

    private function generateSheetHtmlContent($sheet): string
    {
        $html = "<div class='table-container'><table><caption>" . htmlspecialchars($sheet->getName()) . "</caption>\n<thead>";
        $isFirstRow = true;

        foreach ($sheet->getRowIterator() as $row) {
            $html .= "<tr>";
            foreach ($row->getCells() as $cell) {
                $value = htmlspecialchars($cell->getValue());
                $html .= $isFirstRow ? "<th>$value</th>" : "<td>$value</td>";
            }
            $html .= "</tr>";

            if ($isFirstRow) {
                $html .= "</thead><tbody>";
                $isFirstRow = false;
            }
        }

        $html .= "</tbody></table></div><br>";
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

    private function excelToCsv(): void
    {
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);
        Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

        $tempCsvPath = null;
        $zipTempPath = null;
        $tempCsvFiles = [];

        try {
            // Определяем ридер по расширению файла
            $fileExtension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

            $reader = ($fileExtension === 'xlsx') ? IOFactory::createReader('Xlsx') : IOFactory::createReader('Xls');

            // Настройка читателя - читаем только данные для CSV
            $reader->setReadDataOnly(true);

            // Загружаем исходный excel файл
            $sourceSpreadsheet = $reader->load($tempFilePath);
            $storagePath = '';

            // Создаем директорию если не существует
            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            if ($sourceSpreadsheet->getSheetCount() === 1) {
                // Один лист - создаем один CSV файл
                $sourceSheet = $sourceSpreadsheet->getSheet(0);
                $sheetName = $this->sanitizeFilename($sourceSheet->getTitle());

                // Создаем временный CSV файл
                $tempCsvPath = tempnam(sys_get_temp_dir(), 'csv_') . '.csv';
                $this->convertSheetToCsv($sourceSheet, $tempCsvPath, $this->delimiter);

                // Сохраняем в storage
                $outputFileName = $sheetName . '.csv';
                $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;
                Storage::disk('local')->put($storagePath, file_get_contents($tempCsvPath));

            } else {
                // Множество листов - создаем ZIP архив
                $zipTempPath = tempnam(sys_get_temp_dir(), 'csv_zip_') . '.zip';
                $zip = new \ZipArchive();

                if ($zip->open($zipTempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                    foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                        $sheetName = $this->sanitizeFilename($sourceSheet->getTitle());
                        $csvTempPath = tempnam(sys_get_temp_dir(), 'csv_sheet_') . '.csv';

                        $this->convertSheetToCsv($sourceSheet, $csvTempPath, $this->delimiter);

                        // Добавляем файл в ZIP
                        $zip->addFile($csvTempPath, $sheetName . '.csv');
                        $tempCsvFiles[] = $csvTempPath;
                    }

                    $zip->close();

                    // Сохраняем ZIP в storage
                    $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '_csv.zip';
                    $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;
                    Storage::disk('local')->put($storagePath, file_get_contents($zipTempPath));
                } else {
                    throw new \Exception("Cannot create ZIP archive");
                }
            }

            // Проверяем что файл сохранен в storage
            if (empty($storagePath) || !Storage::disk('local')->exists($storagePath)) {
                Log::error('Function excelToCsv: Output file does not exist in storage', ['path' => $storagePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function excelToCsv: Output file was not created in storage");
            }

            $fileSize = Storage::disk('local')->size($storagePath);
            Log::info('Function excelToCsv: File saved to storage', [
                'storage_path' => $storagePath,
                'file_size' => $fileSize
            ]);

            // Обновляем статус
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath
            ]);

            Log::info('Function excelToCsv: Conversion completed successfully', [
                'file_id' => $this->fileMetaData->id,
                'original_file' => $this->original_name,
                'output_file' => $storagePath
            ]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function excelToCsv: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временный исходный файл
            $this->safeCleanup($tempFilePath, $tempCsvPath, $zipTempPath);
            // Удаляем временные CSV файлы
            foreach ($tempCsvFiles as $tempCsvFile) {
                $this->safeCleanup($tempCsvFile);
            }
        }
    }

    private function convertSheetToCsv($sourceSheet, string $outputFilePath, string $delimiter = ','): void
    {
        $highestRow = $sourceSheet->getHighestDataRow();
        $highestColumn = $sourceSheet->getHighestDataColumn();

        // Пропускаем пустые листы
        if ($highestRow == 1 && $sourceSheet->getCell('A1')->getValue() === null) {
            file_put_contents($outputFilePath, '');
            return;
        }

        $csvFile = fopen($outputFilePath, 'w');
        fwrite($csvFile, "\xEF\xBB\xBF"); // BOM

        // Обрабатываем построчно для экономии памяти
        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $sourceSheet->getCell($col . $row)->getValue();
                $rowData[] = $cellValue === null ? '' : $cellValue;
            }

            // Проверяем, что строка не полностью пустая
            if (
                !empty(array_filter($rowData, function ($v) {
                    return $v !== '';
                }))
            ) {
                fputcsv($csvFile, $rowData, $delimiter);
            }
        }

        fclose($csvFile);
    }

    private function sanitizeFilename(string $filename): string
    {
        return preg_replace('/[\/:*?"<>|]/', '_', $filename);
    }

    private function excelToOds(): void
    {
        $originalMemoryLimit = ini_get('memory_limit');
        $tempFilePath = null;
        $tempOutputPath = null;

        try {
            ini_set('memory_limit', '1024M'); // Увеличиваем до 1GB
            Log::info('Memory limit set to 1GB');

            // Получаем содержимое файла
            $fileContent = Storage::disk('local')->get($this->path);
            $this->fileMetaData->update(["status" => "processing"]);

            // Создаем временный файл
            $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
            file_put_contents($tempFilePath, $fileContent);

            $fileExtension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

            // Настройка читателя с оптимизацией памяти
            $reader = ($fileExtension === 'xlsx') ? IOFactory::createReader('Xlsx') : IOFactory::createReader('Xls');

            // ВКЛЮЧАЕМ оптимизацию памяти для читателя
            if (method_exists($reader, 'setReadDataOnly')) {
                $reader->setReadDataOnly(false); // Но нам нужны стили, поэтому false
            }

            if (method_exists($reader, 'setReadEmptyCells')) {
                $reader->setReadEmptyCells(false);
            }

            Log::info('Loading source spreadsheet...');
            $sourceSpreadsheet = $reader->load($tempFilePath);
            Log::info('Source spreadsheet loaded', [
                'memory_usage' => memory_get_usage(true),
                'peak_usage' => memory_get_peak_usage(true)
            ]);

            // ОСВОБОЖДАЕМ ПАМЯТЬ сразу после загрузки
            unset($fileContent);
            gc_collect_cycles();

            // Создаем новый документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            $sheetCount = $sourceSpreadsheet->getSheetCount();
            $processedSheets = 0;

            Log::info('Starting sheet processing', [
                'total_sheets' => $sheetCount,
                'file_size' => filesize($tempFilePath),
                'current_memory' => memory_get_usage(true)
            ]);

            // Обрабатываем каждый лист с оптимизацией памяти
            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                Log::info("Processing sheet: {$sourceSheet->getTitle()}", [
                    'memory_before' => memory_get_usage(true)
                ]);

                // Копируем только самое необходимое
                $this->copySheetProperties($sourceSheet, $newSheet);
                $this->copyMergedCells($sourceSheet, $newSheet);
                $this->copyDimensions($sourceSheet, $newSheet);

                // Оптимизированное копирование ячеек
                $this->copyCellsWithArrayStyles($sourceSheet, $newSheet);

                $processedSheets++;

                Log::info("Processed sheet {$processedSheets} of {$sheetCount}", [
                    'memory_after' => memory_get_usage(true)
                ]);

                // Агрессивная очистка памяти после каждого листа
                gc_collect_cycles();
            }

            // ОСВОБОЖДАЕМ ПАМЯТЬ исходного документа
            $sourceSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet);
            gc_collect_cycles();

            Log::info('All sheets processed, starting ODS conversion', [
                'memory_before_conversion' => memory_get_usage(true)
            ]);

            // Создаем временный файл для конвертации
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'converted_') . '.ods';

            // Устанавливаем таймаут и увеличиваем память для writer
            set_time_limit(300); // 5 минут

            $writer = IOFactory::createWriter($newSpreadsheet, 'Ods');

            // Оптимизации для writer если доступны
            if (method_exists($writer, 'setPreCalculateFormulas')) {
                $writer->setPreCalculateFormulas(false);
            }

            Log::info('Starting ODS file save...');
            $writer->save($tempOutputPath);
            Log::info('ODS file saved successfully');

            // ОСВОБОЖДАЕМ ПАМЯТЬ сразу после сохранения
            $newSpreadsheet->disconnectWorksheets();
            unset($newSpreadsheet, $writer);
            gc_collect_cycles();

            // Проверяем что файл создан
            if (!file_exists($tempOutputPath)) {
                throw new \Exception("ODS output file was not created");
            }

            Log::info('ODS file verified', [
                'file_size' => filesize($tempOutputPath),
                'memory_after_save' => memory_get_usage(true)
            ]);

            // Сохраняем файл в storage
            $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '.ods';
            $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;

            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            // Читаем и сохраняем частями если файл большой
            $outputContent = file_get_contents($tempOutputPath);
            Storage::disk('local')->put($storagePath, $outputContent);
            unset($outputContent); // Освобождаем память

            if (!Storage::disk('local')->exists($storagePath)) {
                throw new \Exception("File not saved to storage");
            }

            // Обновляем статус
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath
            ]);

            Log::info('Function excelToOds: Conversion completed successfully', [
                'storage_path' => $storagePath,
                'file_size' => Storage::disk('local')->size($storagePath),
                'final_memory' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true)
            ]);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function excelToOds: Conversion error: ' . $e->getMessage(), [
                'memory_at_error' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true)
            ]);
            throw $e;
        } finally {
            // Восстанавливаем оригинальный лимит памяти
            if ($originalMemoryLimit) {
                ini_set('memory_limit', $originalMemoryLimit);
            }
            set_time_limit(0); // Сбрасываем таймаут

            // Удаляем временные файлы
            $this->safeUnlink($tempFilePath);
            $this->safeUnlink($tempOutputPath);

            // Финальная очистка памяти
            gc_collect_cycles();
        }
    }

    private function xlsToXlsx(): void
    {
        $memoryLimit = ini_get('memory_limit');

        $tempFilePath = null;
        $tempOutputPath = null;

        try {
            // Получаем содержимое файла
            $fileContent = Storage::disk('local')->get($this->path);
            $this->fileMetaData->update(["status" => "processing"]);

            // Создаем временный файл
            $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
            file_put_contents($tempFilePath, $fileContent);

            ini_set('memory_limit', '512M');

            // Настройка читателя
            $reader = IOFactory::createReader('Xls');

            // Загружаем исходный XLSX файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            // Ограничиваем количество одновременно обрабатываемых листов для больших файлов
            $sheetCount = $sourceSpreadsheet->getSheetCount();
            $processedSheets = 0;

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

                $processedSheets++;
                Log::info("Processed sheet {$processedSheets} of {$sheetCount}");

                // Освобождаем память после обработки каждого листа
                gc_collect_cycles();
            }

            // Создаем временный файл для конвертации
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'converted_') . '.xlsx';

            Log::info('New path', ['path' => $tempOutputPath]);

            $writer = IOFactory::createWriter($newSpreadsheet, 'Xlsx');
            $writer->save($tempOutputPath);

            // Проверяем что файл создан
            if (!file_exists($tempOutputPath)) {
                Log::error('Function xlsToXlsx: Output file does not exist', ['path' => $tempOutputPath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function xlsToXlsx: Output file was not created");
            }

            // Сохраняем файл в storage
            $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '.xlsx';
            $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;

            // Создаем директорию если не существует
            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            // Сохраняем в storage
            Storage::disk('local')->put($storagePath, file_get_contents($tempOutputPath));

            // Проверяем что файл сохранен в storage
            if (!Storage::disk('local')->exists($storagePath)) {
                Log::error('Function xlsToXlsx: File not saved to storage', ['path' => $storagePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function xlsToXlsx: File not saved to storage");
            }

            // Обновляем статус и путь
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath
            ]);

            Log::info('Function xlsxToXls: Conversion completed successfully', [
                'storage_path' => $storagePath,
                'file_size' => Storage::disk('local')->size($storagePath),
                'memory_usage' => memory_get_usage(true)
            ]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function xlsxToXls: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeCleanup($tempFilePath, $tempOutputPath);
        }
    }

    private function xlsxToXls(): void
    {
        $memoryLimit = ini_get('memory_limit');

        $tempFilePath = null;
        $tempOutputPath = null;

        try {
            // Получаем содержимое файла
            $fileContent = Storage::disk('local')->get($this->path);
            $this->fileMetaData->update(["status" => "processing"]);

            // Создаем временный файл
            $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
            file_put_contents($tempFilePath, $fileContent);

            ini_set('memory_limit', '512M');

            // Настройка читателя
            $reader = IOFactory::createReader('Xlsx');

            // Загружаем исходный XLSX файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый XLS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            // Ограничиваем количество одновременно обрабатываемых листов для больших файлов
            $sheetCount = $sourceSpreadsheet->getSheetCount();
            $processedSheets = 0;

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

                $processedSheets++;
                Log::info("Processed sheet {$processedSheets} of {$sheetCount}");

                // Освобождаем память после обработки каждого листа
                gc_collect_cycles();
            }

            // Создаем временный файл для конвертации
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'converted_') . '.xls';

            Log::info('New path', ['path' => $tempOutputPath]);

            $writer = IOFactory::createWriter($newSpreadsheet, 'Xls');
            $writer->save($tempOutputPath);

            // Проверяем что файл создан
            if (!file_exists($tempOutputPath)) {
                Log::error('Function xlsxToXls: Output file does not exist', ['path' => $tempOutputPath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function xlsxToXls: Output file was not created");
            }

            // Сохраняем файл в storage
            $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '.xls';
            $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;

            // Создаем директорию если не существует
            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            // Сохраняем в storage
            Storage::disk('local')->put($storagePath, file_get_contents($tempOutputPath));

            // Проверяем что файл сохранен в storage
            if (!Storage::disk('local')->exists($storagePath)) {
                Log::error('Function xlsxToXls: File not saved to storage', ['path' => $storagePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function xlsxToXls: File not saved to storage");
            }

            // Обновляем статус и путь
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath
            ]);

            Log::info('Function xlsxToXls: Conversion completed successfully', [
                'storage_path' => $storagePath,
                'file_size' => Storage::disk('local')->size($storagePath),
                'memory_usage' => memory_get_usage(true)
            ]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function xlsxToXls: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeCleanup($tempFilePath, $tempOutputPath);
        }
    }

    /**
     * Безопасная очистка временных файлов без возможности ошибок
     */
    private function safeCleanup(?string ...$paths): void
    {
        foreach ($paths as $path) {
            if ($path && file_exists($path)) {
                try {
                    @unlink($path);
                } catch (\Exception $e) {
                }
            }
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

        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $batchSize = $highestColumnIndex > 50 ? 25 : 50;

        $processedRows = 0;

        Log::info('Function copyCellsWithArrayStyles: Starting optimized array-based style copying', [
            'rows' => $highestRow,
            'columns' => $highestColumn,
            'batch_size' => $batchSize,
            'total_cells' => $highestRow * $highestColumnIndex
        ]);

        for ($row = 1; $row <= $highestRow; $row += $batchSize) {
            $endRow = min($row + $batchSize - 1, $highestRow);

            for ($currentRow = $row; $currentRow <= $endRow; $currentRow++) {
                // Формируем индексированный массив значений длиной highestColumnIndex
                $rowArray = array_fill(0, $highestColumnIndex, '');

                for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                    $col = Coordinate::stringFromColumnIndex($colIndex);
                    $cellCoordinate = $col . $currentRow;

                    $sourceCell = $sourceSheet->getCell($cellCoordinate);
                    $cellValue = $this->getSafeCellValue($sourceCell);

                    // Всегда записываем значение (включая пустые строки)
                    $rowArray[$colIndex - 1] = $cellValue === null ? '' : $cellValue;
                }

                // Массовая запись всей строки (включая пустые ячейки)
                $newSheet->fromArray($rowArray, null, 'A' . $currentRow);

                // Применяем стили для каждой ячейки в строке
                $this->applyStylesForRow($sourceSheet, $newSheet, $currentRow, $highestColumnIndex);

                $processedRows++;

                if ($processedRows % 10 === 0) {
                    gc_collect_cycles();
                }
            }

            Log::debug("Function copyCellsWithArrayStyles: Completed processing rows {$row} to {$endRow}", ['memory_usage' => memory_get_usage(true)]);
            gc_collect_cycles();
        }

        Log::info('Function copyCellsWithArrayStyles: Completed optimized array-based style copying', [
            'total_rows' => $processedRows,
            'memory_peak' => memory_get_peak_usage(true)
        ]);
    }

    private function applyStylesForRow($sourceSheet, $newSheet, $rowNumber, $highestColumnIndex): void
    {
        for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $cellCoordinate = $col . $rowNumber;

            $sourceCell = $sourceSheet->getCell($cellCoordinate);

            if ($this->hasVisibleStyle($sourceCell)) {
                try {
                    $styleArray = $sourceCell->getStyle()->exportArray();
                    $newSheet->getStyle($cellCoordinate)->applyFromArray($styleArray);

                    unset($styleArray);
                } catch (\Exception $e) {
                    Log::warning('Apply styles error for cell ' . $cellCoordinate . ': ' . $e->getMessage());
                }
            }
        }
    }

    private function getSafeCellValue($cell)
    {
        try {
            $value = $cell->getValue();

            // RichText -> plain text
            if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                return $value->getPlainText();
            }

            // Формула: брать вычисленное значение, если возможно
            if ($cell->isFormula()) {
                try {
                    $calculated = $cell->getCalculatedValue();
                    return $calculated;
                } catch (\Exception $e) {
                }
            }

            if (is_array($value)) {
                return isset($value[0]) ? $value[0] : null;
            }

            if (is_object($value)) {
                if (method_exists($value, 'getPlainText')) {
                    return $value->getPlainText();
                }
                if (method_exists($value, '__toString')) {
                    return (string) $value;
                }
                return null;
            }

            return $value;
        } catch (\Exception $e) {
            Log::warning("Function getSafeCellValue was finished unsuccessfully: Error getting cell value: " . $e->getMessage());
            return null;
        }
    }

    private function hasVisibleStyle($cell): bool
    {
        try {
            $style = $cell->getStyle();

            if ($style->getFill()->getFillType() !== null) {
                return true;
            }

            $font = $style->getFont();
            if ($font->getBold() || $font->getItalic() || $font->getSize() !== 11 || $font->getName() !== 'Calibri') {
                return true;
            }

            $borders = $style->getBorders();
            if (
                $borders->getLeft()->getBorderStyle() !== 'none' ||
                $borders->getRight()->getBorderStyle() !== 'none' ||
                $borders->getTop()->getBorderStyle() !== 'none' ||
                $borders->getBottom()->getBorderStyle() !== 'none'
            ) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::warning('Error checking cell style: ' . $e->getMessage());
            return false;
        }
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
            if (is_file($path)) {
                if (@unlink($path)) {
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
