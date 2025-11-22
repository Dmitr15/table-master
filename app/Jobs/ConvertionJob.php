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

    private function merge(): void
    {
        // Проверяем, используем ли мы новую логику слияния двух файлов
        if ($this->file1Id && $this->file2Id) {
            $this->mergeTwoFiles();
        } else {
            // Старая логика слияния (один файл + merge_file)
            $this->mergeWithFile();
        }
    }

    private function mergeTwoFiles(): void
    {
        try {
            Log::info("Starting merge of two files", [
                'file1_id' => $this->file1Id,
                'file2_id' => $this->file2Id,
                'result_file_id' => $this->fileMetaData->id
            ]);

            $this->fileMetaData->update(["status" => "processing"]);

            // Получаем файлы из базы данных
            $file1 = UserFile::find($this->file1Id);
            $file2 = UserFile::find($this->file2Id);

            if (!$file1 || !$file2) {
                throw new \Exception("One or both files not found in database");
            }

            Log::info("Files found in database", [
                'file1' => $file1->original_name . ' (path: ' . $file1->path . ')',
                'file2' => $file2->original_name . ' (path: ' . $file2->path . ')'
            ]);

            // Проверяем существование файлов в storage
            if (!Storage::disk('local')->exists($file1->path)) {
                throw new \Exception("File1 not found in storage: " . $file1->path);
            }
            if (!Storage::disk('local')->exists($file2->path)) {
                throw new \Exception("File2 not found in storage: " . $file2->path);
            }

            // Проверяем размеры файлов
            $file1Size = Storage::disk('local')->size($file1->path);
            $file2Size = Storage::disk('local')->size($file2->path);
            
            Log::info("File sizes", [
                'file1_size' => $file1Size,
                'file2_size' => $file2Size
            ]);

            if ($file1Size === 0 || $file2Size === 0) {
                throw new \Exception("One or both files are empty");
            }

            // Загружаем оба файла
            $file1Content = Storage::disk('local')->get($file1->path);
            $file2Content = Storage::disk('local')->get($file2->path);

            // Создаем временные файлы
            $file1Extension = strtolower(pathinfo($file1->original_name, PATHINFO_EXTENSION));
            $file2Extension = strtolower(pathinfo($file2->original_name, PATHINFO_EXTENSION));

            $tempFilePath1 = tempnam(sys_get_temp_dir(), 'merge_file1_') . '.' . $file1Extension;
            $tempFilePath2 = tempnam(sys_get_temp_dir(), 'merge_file2_') . '.' . $file2Extension;

            file_put_contents($tempFilePath1, $file1Content);
            file_put_contents($tempFilePath2, $file2Content);

            Log::info('Merge two files: Temp files created and verified', [
                'file1_temp' => $tempFilePath1 . ' (size: ' . filesize($tempFilePath1) . ')',
                'file2_temp' => $tempFilePath2 . ' (size: ' . filesize($tempFilePath2) . ')'
            ]);

            // Проверяем, что временные файлы созданы и не пустые
            if (!file_exists($tempFilePath1) || filesize($tempFilePath1) === 0) {
                throw new \Exception("Temporary file1 creation failed");
            }
            if (!file_exists($tempFilePath2) || filesize($tempFilePath2) === 0) {
                throw new \Exception("Temporary file2 creation failed");
            }

            // Создаем readers для обоих файлов
            $reader1 = ($file1Extension === "xlsx") ? IOFactory::createReader('Xlsx') : IOFactory::createReader('Xls');
            $reader2 = ($file2Extension === "xlsx") ? IOFactory::createReader('Xlsx') : IOFactory::createReader('Xls');

            // Включаем чтение только данных для ускорения
            $reader1->setReadDataOnly(false);
            $reader2->setReadDataOnly(false);

            // Загружаем оба файла
            Log::info("Loading spreadsheets...");
            $spreadsheet1 = $reader1->load($tempFilePath1);
            $spreadsheet2 = $reader2->load($tempFilePath2);

            Log::info('Merge two files: Files loaded successfully', [
                'file1_sheets' => $spreadsheet1->getSheetCount(),
                'file2_sheets' => $spreadsheet2->getSheetCount()
            ]);

            // Детальная информация о листах
            foreach ($spreadsheet1->getAllSheets() as $index => $sheet) {
                $highestRow = $sheet->getHighestDataRow();
                $highestColumn = $sheet->getHighestDataColumn();
                Log::info("File1 Sheet {$index}: " . $sheet->getTitle(), [
                    'rows' => $highestRow,
                    'columns' => $highestColumn,
                    'has_data' => $highestRow > 1 || ($highestRow == 1 && $sheet->getCell('A1')->getValue() !== null)
                ]);
            }

            foreach ($spreadsheet2->getAllSheets() as $index => $sheet) {
                $highestRow = $sheet->getHighestDataRow();
                $highestColumn = $sheet->getHighestDataColumn();
                Log::info("File2 Sheet {$index}: " . $sheet->getTitle(), [
                    'rows' => $highestRow,
                    'columns' => $highestColumn,
                    'has_data' => $highestRow > 1 || ($highestRow == 1 && $sheet->getCell('A1')->getValue() !== null)
                ]);
            }

            // Создаем новый spreadsheet для результата
            $resultSpreadsheet = new Spreadsheet();
            $resultSpreadsheet->removeSheetByIndex(0); // Удаляем дефолтный лист

            $sheetCounter = 0;

            // Копируем листы из первого файла
            Log::info("Copying sheets from file1...");
            foreach ($spreadsheet1->getAllSheets() as $sheet1) {
                $sheetName = $this->getUniqueSheetName($resultSpreadsheet, $sheet1->getTitle());
                
                Log::info("Creating sheet: " . $sheetName);
                $newSheet = new Worksheet($resultSpreadsheet, $sheetName);
                $resultSpreadsheet->addSheet($newSheet);

                // Копируем содержимое листа
                $this->copySheetProperties($sheet1, $newSheet);
                $this->copyMergedCells($sheet1, $newSheet);
                $this->copyDimensions($sheet1, $newSheet);
                $this->copyCellsWithArrayStyles($sheet1, $newSheet);

                // Проверяем, что данные скопировались
                $newHighestRow = $newSheet->getHighestDataRow();
                $newHighestColumn = $newSheet->getHighestDataColumn();
                Log::info("Sheet copied: " . $sheetName, [
                    'rows' => $newHighestRow,
                    'columns' => $newHighestColumn
                ]);

                $sheetCounter++;
                gc_collect_cycles();
            }

            // Копируем листы из второго файла
            Log::info("Copying sheets from file2...");
            foreach ($spreadsheet2->getAllSheets() as $sheet2) {
                $sheetName = $this->getUniqueSheetName($resultSpreadsheet, $sheet2->getTitle());
                
                Log::info("Creating sheet: " . $sheetName);
                $newSheet = new Worksheet($resultSpreadsheet, $sheetName);
                $resultSpreadsheet->addSheet($newSheet);

                // Копируем содержимое листа
                $this->copySheetProperties($sheet2, $newSheet);
                $this->copyMergedCells($sheet2, $newSheet);
                $this->copyDimensions($sheet2, $newSheet);
                $this->copyCellsWithArrayStyles($sheet2, $newSheet);

                // Проверяем, что данные скопировались
                $newHighestRow = $newSheet->getHighestDataRow();
                $newHighestColumn = $newSheet->getHighestDataColumn();
                Log::info("Sheet copied: " . $sheetName, [
                    'rows' => $newHighestRow,
                    'columns' => $newHighestColumn
                ]);

                $sheetCounter++;
                gc_collect_cycles();
            }

            // Проверяем итоговое количество листов
            Log::info("Total sheets in result: " . $resultSpreadsheet->getSheetCount());

            // Сохраняем объединенный файл
            $outputExtension = 'xlsx';
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'merge_output_') . '.' . $outputExtension;

            Log::info("Saving result to: " . $tempOutputPath);
            $outputWriter = IOFactory::createWriter($resultSpreadsheet, 'Xlsx');
            $outputWriter->save($tempOutputPath);

            // Проверяем, что выходной файл создан и не пустой
            if (!file_exists($tempOutputPath) || filesize($tempOutputPath) === 0) {
                throw new \Exception("Output file creation failed - file is empty or doesn't exist");
            }

            Log::info("Output file created successfully", ['size' => filesize($tempOutputPath)]);

            // Сохраняем в storage
            $outputFileName = 'merged_' . pathinfo($file1->original_name, PATHINFO_FILENAME) . '_' . pathinfo($file2->original_name, PATHINFO_FILENAME) . '.' . $outputExtension;
            $storagePath = 'converted_files/' . $outputFileName;

            // Создаем директорию если не существует
            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            // Сохраняем в Storage
            Storage::disk('local')->put($storagePath, file_get_contents($tempOutputPath));

            // Проверяем что файл сохранен в storage
            if (!Storage::disk('local')->exists($storagePath)) {
                Log::error('Merge two files: File not saved to storage', ['path' => $storagePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Merge two files: File not saved to storage");
            }

            $fileSize = Storage::disk('local')->size($storagePath);
            Log::info('Merge two files: File saved to storage', [
                'storage_path' => $storagePath,
                'file_size' => $fileSize
            ]);

            // Обновляем статус
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath,
                "original_name" => $outputFileName
            ]);

            Log::info('Merge two files: Operation completed successfully', [
                'merged_file_name' => $outputFileName,
                'output_path' => $storagePath,
                'total_sheets' => $sheetCounter,
                'final_size' => $fileSize
            ]);

            // Освобождаем память
            $spreadsheet1->disconnectWorksheets();
            $spreadsheet2->disconnectWorksheets();
            $resultSpreadsheet->disconnectWorksheets();
            unset($spreadsheet1, $spreadsheet2, $resultSpreadsheet);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Merge two files: Error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временные файлы
            if (isset($tempFilePath1)) $this->safeUnlink($tempFilePath1);
            if (isset($tempFilePath2)) $this->safeUnlink($tempFilePath2);
            if (isset($tempOutputPath)) $this->safeUnlink($tempOutputPath);
        }
    }

    private function mergeWithFile(): void
    {
        // Старая логика слияния (один файл + merge_file)
        // Проверяем, что путь к файлу для слияния существует
        if (empty($this->mergedFilePath) || !Storage::disk('local')->exists($this->mergedFilePath)) {
            $this->fileMetaData->update(["status" => "failed"]);
            throw new \Exception("Merge file not found or path is empty");
        }

        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        // Получаем расширения файлов
        $sourceExtension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        $mergeExtension = strtolower(pathinfo($this->mergedFilePath, PATHINFO_EXTENSION));

        $tempFilePath = tempnam(sys_get_temp_dir(), 'merge_source_') . '.' . $sourceExtension;
        file_put_contents($tempFilePath, $fileContent);

        try {
            // Получаем содержимое файла для слияния, второй файл
            $mergeFileContent = Storage::disk('local')->get($this->mergedFilePath);
            $mergeTempFilePath = tempnam(sys_get_temp_dir(), 'merge_target_') . '.' . $mergeExtension;
            file_put_contents($mergeTempFilePath, $mergeFileContent);

            Log::info('Merge: Temp files created', [
                'source' => $tempFilePath,
                'merge' => $mergeTempFilePath,
                'source_extension' => $sourceExtension,
                'merge_extension' => $mergeExtension,
            ]);

            $sourceReader = ($sourceExtension === "xlsx") ?
                IOFactory::createReader('Xlsx') :
                IOFactory::createReader('Xls');

            $mergeReader = ($mergeExtension === "xlsx") ?
                IOFactory::createReader('Xlsx') :
                IOFactory::createReader('Xls');

            Log::info('Readers was created');

            // Загружаем оба файла
            $sourceSpreadsheet = $sourceReader->load($tempFilePath);
            $mergeSpreadsheet = $mergeReader->load($mergeTempFilePath);

            Log::info('Merge: Files loaded', [
                'source_sheets' => $sourceSpreadsheet->getSheetCount(),
                'merge_sheets' => $mergeSpreadsheet->getSheetCount()
            ]);

            // Копируем все листы из файла для слияния в исходный файл
            foreach ($mergeSpreadsheet->getAllSheets() as $mergeSheet) {
                $sheetName = $this->getUniqueSheetName($sourceSpreadsheet, $mergeSheet->getTitle());

                Log::info('Merge: Copying sheet', [
                    'original_name' => $mergeSheet->getTitle(),
                    'new_name' => $sheetName
                ]);

                // Создаем новый лист в исходном файле
                $newSheet = new Worksheet($sourceSpreadsheet, $sheetName);
                $sourceSpreadsheet->addSheet($newSheet);

                // Копируем содержимое
                $this->copySheetProperties($mergeSheet, $newSheet);
                $this->copyMergedCells($mergeSheet, $newSheet);
                $this->copyDimensions($mergeSheet, $newSheet);
                $this->copyCellsWithArrayStyles($mergeSheet, $newSheet);

                gc_collect_cycles();
            }

            // Сохраняем объединенный файл во временный файл
            $outputExtension = $sourceExtension; // Сохраняем в формате исходного файла
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'merge_output_') . '.' . $outputExtension;

            $outputWriter = ($outputExtension === "xlsx") ?
                IOFactory::createWriter($sourceSpreadsheet, 'Xlsx') :
                IOFactory::createWriter($sourceSpreadsheet, 'Xls');

            $outputWriter->save($tempOutputPath);

            // Сохраняем в storage
            $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '_merged_' . uniqid() . '.' . $outputExtension;
            $storagePath = 'converted_files/' . $outputFileName;

            // Создаем директорию если не существует
            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            // Сохраняем в Storage
            Storage::disk('local')->put($storagePath, file_get_contents($tempOutputPath));

            // Проверяем что файл сохранен в storage
            if (!Storage::disk('local')->exists($storagePath)) {
                Log::error('Function merge: File not saved to storage', ['path' => $storagePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function merge: File not saved to storage");
            }

            $fileSize = Storage::disk('local')->size($storagePath);
            Log::info('Function merge: File saved to storage', [
                'storage_path' => $storagePath,
                'file_size' => $fileSize
            ]);

            // Обновляем статус
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath // Сохраняем относительный путь
            ]);

            Log::info('Merge: Operation completed successfully', [
                'merged_file_name' => $outputFileName,
                'output_path' => $storagePath,
                'total_sheets' => $sourceSpreadsheet->getSheetCount()
            ]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $mergeSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $mergeSpreadsheet);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Merge: Error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeCleanup($tempFilePath, $mergeTempFilePath, $tempOutputPath);

            // Удаляем временный merge файл из storage (если он был загружен)
            if (!empty($this->mergedFilePath) && Storage::disk('local')->exists($this->mergedFilePath)) {
                try {
                    Storage::disk('local')->delete($this->mergedFilePath);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete merge file from storage: ' . $e->getMessage());
                }
            }
        }
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

    private function split(): void
    {
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);
        Log::info('Temp file created', ['path' => $tempFilePath, 'size' => filesize($tempFilePath)]);

        $zipTempPath = null;
        $tempSheetFiles = [];

        try {
            $fileExtension = pathinfo($this->original_name, PATHINFO_EXTENSION);
            $reader = ($fileExtension === "xlsx") ?
                IOFactory::createReader('Xlsx') :
                IOFactory::createReader('Xls');

            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем временный ZIP файл
            $zipTempPath = tempnam(sys_get_temp_dir(), 'split_zip_') . '.zip';
            $zip = new \ZipArchive();

            if ($zip->open($zipTempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                foreach ($sourceSpreadsheet->getAllSheets() as $sheet) {
                    // Создаем новый Excel документ для каждого листа
                    $newSpreadsheetForSheet = new Spreadsheet();
                    $newSpreadsheetForSheet->removeSheetByIndex(0);

                    $newSheet = new Worksheet($newSpreadsheetForSheet, $sheet->getTitle());
                    $newSpreadsheetForSheet->addSheet($newSheet);

                    // Копируем содержимое и структуры
                    try {
                        $this->copyMergedCells($sheet, $newSheet);
                        $this->copyDimensions($sheet, $newSheet);
                        $this->copyCellsWithArrayStyles($sheet, $newSheet);
                    } catch (\Exception $e) {
                        Log::warning('Split: warning while copying sheet content: ' . $e->getMessage(), ['sheet' => $sheet->getTitle()]);
                    }

                    // Сохраняем лист во временный файл
                    $sheetName = $this->sanitizeFilename($sheet->getTitle());
                    $tempSheetPath = tempnam(sys_get_temp_dir(), 'split_sheet_') . '.' . $fileExtension;

                    $writer = ($fileExtension === "xlsx") ?
                        IOFactory::createWriter($newSpreadsheetForSheet, 'Xlsx') :
                        IOFactory::createWriter($newSpreadsheetForSheet, 'Xls');

                    $writer->save($tempSheetPath);

                    // Добавляем файл в ZIP
                    $zip->addFile($tempSheetPath, $sheetName . '.' . $fileExtension);
                    $tempSheetFiles[] = $tempSheetPath;

                    // Освобождаем память
                    $newSpreadsheetForSheet->disconnectWorksheets();
                    unset($newSpreadsheetForSheet, $newSheet);

                    gc_collect_cycles();
                }

                $zip->close();
            } else {
                throw new \Exception("Cannot create ZIP archive for split operation");
            }

            // Сохраняем ZIP файл в storage
            $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '_split.zip';
            $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;

            // Создаем директорию если не существует
            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            // Сохраняем в storage
            Storage::disk('local')->put($storagePath, file_get_contents($zipTempPath));

            // Проверяем что файл сохранен в storage
            if (!Storage::disk('local')->exists($storagePath)) {
                Log::error('Function split: File not saved to storage', ['path' => $storagePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function split: File not saved to storage");
            }

            $fileSize = Storage::disk('local')->size($storagePath);
            Log::info('Function split: File saved to storage', [
                'storage_path' => $storagePath,
                'file_size' => $fileSize
            ]);

            // Обновляем статус
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath
            ]);

            Log::info('Function split: Operation completed successfully', [
                'file_id' => $this->fileMetaData->id,
                'original_file' => $this->original_name,
                'output_file' => $storagePath,
                'sheet_count' => $sourceSpreadsheet->getSheetCount()
            ]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function split: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->safeCleanup($tempFilePath, $zipTempPath);

            // Удаляем временные файлы листов
            foreach ($tempSheetFiles as $tempSheetFile) {
                $this->safeCleanup($tempSheetFile);
            }
        }
    }

    private function splitBySheets($sourceSpreadsheet, $format, $originalExtension): void
    {
        $sheetCount = $sourceSpreadsheet->getSheetCount();
        Log::info("Splitting by sheets", ['sheet_count' => $sheetCount]);

        if ($format === 'zip' || $sheetCount > 1) {
            // Создаем ZIP архив для нескольких листов
            $this->createSheetsZip($sourceSpreadsheet, $originalExtension);
        } else {
            // Для одного листа и формата не-ZIP создаем отдельный файл
            $this->createSingleSheetFile($sourceSpreadsheet->getSheet(0), $originalExtension, $format);
        }
    }

    private function splitByRows($sourceSpreadsheet, $rowsPerFile, $format, $originalExtension): void
    {
        Log::info("Splitting by rows", ['rows_per_file' => $rowsPerFile]);
        
        $zipTempPath = tempnam(sys_get_temp_dir(), 'split_rows_zip_') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipTempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $tempFiles = [];
            $fileCounter = 1;

            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                $sheetName = $sourceSheet->getTitle();
                $highestRow = $sourceSheet->getHighestDataRow();
                
                if ($highestRow <= 1) continue; // Пропускаем пустые листы

                $totalFiles = ceil(($highestRow - 1) / $rowsPerFile); // -1 для заголовка

                for ($i = 0; $i < $totalFiles; $i++) {
                    $startRow = ($i * $rowsPerFile) + 1;
                    $endRow = min(($i + 1) * $rowsPerFile, $highestRow);

                    $newSpreadsheet = new Spreadsheet();
                    $newSheet = $newSpreadsheet->getActiveSheet();
                    $newSheet->setTitle($this->sanitizeFilename($sheetName));

                    // Копируем заголовок
                    $this->copyRow($sourceSheet, $newSheet, 1, 1);

                    // Копируем данные
                    for ($row = $startRow; $row <= $endRow; $row++) {
                        $targetRow = $row - $startRow + 2; // +2 потому что первая строка - заголовок
                        $this->copyRow($sourceSheet, $newSheet, $row, $targetRow);
                    }

                    // Сохраняем временный файл
                    $tempSheetPath = tempnam(sys_get_temp_dir(), 'split_rows_') . '.' . $originalExtension;
                    
                    $writer = ($originalExtension === "xlsx") ? 
                        IOFactory::createWriter($newSpreadsheet, 'Xlsx') : 
                        IOFactory::createWriter($newSpreadsheet, 'Xls');
                    
                    $writer->save($tempSheetPath);

                    $fileName = $this->sanitizeFilename($sheetName) . '_part_' . ($i + 1) . '.' . $originalExtension;
                    $zip->addFile($tempSheetPath, $fileName);
                    $tempFiles[] = $tempSheetPath;

                    // Освобождаем память
                    $newSpreadsheet->disconnectWorksheets();
                    unset($newSpreadsheet);

                    $fileCounter++;
                    gc_collect_cycles();
                }
            }

            $zip->close();

            // Сохраняем ZIP файл
            $this->saveResultFile($zipTempPath, 'split_rows.zip');

            // Удаляем временные файлы
            foreach ($tempFiles as $tempFile) {
                $this->safeUnlink($tempFile);
            }
            $this->safeUnlink($zipTempPath);

        } else {
            throw new \Exception("Cannot create ZIP archive for row splitting");
        }
    }

    private function createSheetsZip($sourceSpreadsheet, $originalExtension): void
    {
        $zipTempPath = tempnam(sys_get_temp_dir(), 'split_sheets_zip_') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipTempPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $tempSheetFiles = [];

            foreach ($sourceSpreadsheet->getAllSheets() as $sheet) {
                $newSpreadsheet = new Spreadsheet();
                $newSpreadsheet->removeSheetByIndex(0);

                $newSheet = new Worksheet($newSpreadsheet, $sheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                // Копируем содержимое
                $this->copySheetProperties($sheet, $newSheet);
                $this->copyMergedCells($sheet, $newSheet);
                $this->copyDimensions($sheet, $newSheet);
                $this->copyCellsWithArrayStyles($sheet, $newSheet);

                // Сохраняем лист во временный файл
                $sheetName = $this->sanitizeFilename($sheet->getTitle());
                $tempSheetPath = tempnam(sys_get_temp_dir(), 'split_sheet_') . '.' . $originalExtension;

                $writer = ($originalExtension === "xlsx") ? 
                    IOFactory::createWriter($newSpreadsheet, 'Xlsx') : 
                    IOFactory::createWriter($newSpreadsheet, 'Xls');

                $writer->save($tempSheetPath);

                // Добавляем файл в ZIP
                $zip->addFile($tempSheetPath, $sheetName . '.' . $originalExtension);
                $tempSheetFiles[] = $tempSheetPath;

                // Освобождаем память
                $newSpreadsheet->disconnectWorksheets();
                unset($newSpreadsheet);

                gc_collect_cycles();
            }

            $zip->close();

            // Сохраняем ZIP файл
            $this->saveResultFile($zipTempPath, 'split_sheets.zip');

            // Удаляем временные файлы
            foreach ($tempSheetFiles as $tempSheetFile) {
                $this->safeUnlink($tempSheetFile);
            }
            $this->safeUnlink($zipTempPath);

        } else {
            throw new \Exception("Cannot create ZIP archive for sheets splitting");
        }
    }

    private function createSingleSheetFile($sheet, $originalExtension, $format): void
    {
        $newSpreadsheet = new Spreadsheet();
        $newSpreadsheet->removeSheetByIndex(0);

        $newSheet = new Worksheet($newSpreadsheet, $sheet->getTitle());
        $newSpreadsheet->addSheet($newSheet);

        // Копируем содержимое
        $this->copySheetProperties($sheet, $newSheet);
        $this->copyMergedCells($sheet, $newSheet);
        $this->copyDimensions($sheet, $newSheet);
        $this->copyCellsWithArrayStyles($sheet, $newSheet);

        // Сохраняем файл
        $outputExtension = ($format === 'zip') ? $originalExtension : $format;
        $tempOutputPath = tempnam(sys_get_temp_dir(), 'split_single_') . '.' . $outputExtension;

        $writer = ($outputExtension === "xlsx") ? 
            IOFactory::createWriter($newSpreadsheet, 'Xlsx') : 
            IOFactory::createWriter($newSpreadsheet, 'Xls');

        $writer->save($tempOutputPath);

        $fileName = $this->sanitizeFilename($sheet->getTitle()) . '.' . $outputExtension;
        $this->saveResultFile($tempOutputPath, $fileName);

        // Освобождаем память
        $newSpreadsheet->disconnectWorksheets();
        unset($newSpreadsheet);
        $this->safeUnlink($tempOutputPath);
    }

    private function copyRow($sourceSheet, $targetSheet, $sourceRow, $targetRow): void
    {
        $highestColumn = $sourceSheet->getHighestDataColumn();
        
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $cellValue = $sourceSheet->getCell($col . $sourceRow)->getValue();
            if ($cellValue !== null) {
                $targetSheet->setCellValue($col . $targetRow, $cellValue);
            }
        }
    }

    private function saveResultFile($tempFilePath, $fileName): void
    {
        // Создаем директорию если не существует
        if (!Storage::disk('local')->exists('converted_files')) {
            Storage::disk('local')->makeDirectory('converted_files');
        }

        $storagePath = 'converted_files/' . uniqid() . '_' . $fileName;

        // Сохраняем в Storage
        Storage::disk('local')->put($storagePath, file_get_contents($tempFilePath));

        // Проверяем что файл сохранен в storage
        if (!Storage::disk('local')->exists($storagePath)) {
            Log::error('Split: File not saved to storage', ['path' => $storagePath]);
            $this->fileMetaData->update(["status" => "failed"]);
            throw new \Exception("Split: File not saved to storage");
        }

        $fileSize = Storage::disk('local')->size($storagePath);
        Log::info('Split: File saved to storage', [
            'storage_path' => $storagePath,
            'file_size' => $fileSize
        ]);

        // Обновляем статус
        $this->fileMetaData->update([
            "status" => "completed",
            "output_path" => $storagePath,
            "original_name" => $fileName
        ]);

        Log::info('Split: Operation completed successfully', [
            'output_file' => $fileName,
            'output_path' => $storagePath
        ]);
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
        // Получаем содержимое файла
        $fileContent = Storage::disk('local')->get($this->path);
        $this->fileMetaData->update(["status" => "processing"]);

        // Создаем временный файл
        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);

        $tempOutputPath = null;

        try {
            // Определяем ридер по расширению файла
            $fileExtension = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

            $reader = ($fileExtension === 'xlsx') ? IOFactory::createReader('Xlsx') : IOFactory::createReader('Xls');


            // Загружаем исходный файл
            $sourceSpreadsheet = $reader->load($tempFilePath);

            // Создаем новый ODS документ
            $newSpreadsheet = new Spreadsheet();
            $newSpreadsheet->removeSheetByIndex(0);

            foreach ($sourceSpreadsheet->getAllSheets() as $sourceSheet) {
                // Создаем лист в новом документе
                $newSheet = new Worksheet($newSpreadsheet, $sourceSheet->getTitle());
                $newSpreadsheet->addSheet($newSheet);

                // Копируем основные свойства листа
                $this->copySheetProperties($sourceSheet, $newSheet);

                // Копируем объединенные ячейки
                $this->copyMergedCells($sourceSheet, $newSheet);

                // Копируем размеры столбцов и строк
                $this->copyDimensions($sourceSheet, $newSheet);

                // Копируем данные и стили
                $this->copyCellsWithArrayStyles($sourceSheet, $newSheet);

                // Освобождаем память после обработки каждого листа
                gc_collect_cycles();
            }

            // Создаем временный файл для конвертации
            $tempOutputPath = tempnam(sys_get_temp_dir(), 'converted_ods_') . '.ods';

            $writer = IOFactory::createWriter($newSpreadsheet, 'Ods');
            $writer->save($tempOutputPath);

            Log::info('ODS file created', ['path' => $tempOutputPath, 'size' => filesize($tempOutputPath)]);

            // Проверяем что временный файл создан
            if (!file_exists($tempOutputPath)) {
                Log::error('Function excelToOds: Temporary output file does not exist', ['path' => $tempOutputPath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function excelToOds: Temporary output file was not created");
            }

            // Сохраняем файл в storage
            $outputFileName = pathinfo($this->original_name, PATHINFO_FILENAME) . '.ods';
            $storagePath = 'converted_files/' . uniqid() . '_' . $outputFileName;

            // Создаем директорию если не существует
            if (!Storage::disk('local')->exists('converted_files')) {
                Storage::disk('local')->makeDirectory('converted_files');
            }

            // Сохраняем в storage
            Storage::disk('local')->put($storagePath, file_get_contents($tempOutputPath));

            // Проверяем что файл сохранен в storage
            if (!Storage::disk('local')->exists($storagePath)) {
                Log::error('Function excelToOds: File not saved to storage', ['path' => $storagePath]);
                $this->fileMetaData->update(["status" => "failed"]);
                throw new \Exception("Function excelToOds: File not saved to storage");
            }

            $fileSize = Storage::disk('local')->size($storagePath);
            Log::info('Function excelToOds: File saved to storage', [
                'storage_path' => $storagePath,
                'file_size' => $fileSize
            ]);

            // Обновляем статус и путь
            $this->fileMetaData->update([
                "status" => "completed",
                "output_path" => $storagePath  // Сохраняем относительный путь storage
            ]);

            Log::info('Function excelToOds: Conversion completed successfully', [
                'file_id' => $this->fileMetaData->id,
                'original_file' => $this->original_name,
                'output_file' => $storagePath
            ]);

            // Освобождаем память
            $sourceSpreadsheet->disconnectWorksheets();
            $newSpreadsheet->disconnectWorksheets();
            unset($sourceSpreadsheet, $newSpreadsheet);

        } catch (\Exception $e) {
            $this->fileMetaData->update(["status" => "failed"]);
            Log::error('Function excelToOds: Conversion error: ' . $e->getMessage());
            throw $e;
        } finally {
            // Удаляем временные файлы
            $this->safeCleanup($tempFilePath, $tempOutputPath);
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

        // Уменьшаем размер батча для файлов с большим количеством столбцов
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $batchSize = $highestColumnIndex > 50 ? 25 : 50; // Динамический размер батча

        $processedRows = 0;

        Log::info('Function copyCellsWithArrayStyles: Starting optimized array-based style copying', [
            'rows' => $highestRow,
            'columns' => $highestColumn,
            'batch_size' => $batchSize,
            'total_cells' => $highestRow * $highestColumnIndex
        ]);

        for ($row = 1; $row <= $highestRow; $row += $batchSize) {
            $endRow = min($row + $batchSize - 1, $highestRow);

            // Обрабатываем диапазон строк
            for ($currentRow = $row; $currentRow <= $endRow; $currentRow++) {
                $rowData = [];

                // Используем числовой индекс для столбцов
                for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                    $col = Coordinate::stringFromColumnIndex($colIndex);
                    $cellCoordinate = $col . $currentRow;

                    $sourceCell = $sourceSheet->getCell($cellCoordinate);
                    $cellValue = $this->getSafeCellValue($sourceCell);

                    // Записываем только не-null значения
                    if ($cellValue !== null) {
                        $rowData[$col] = $cellValue;
                    }
                }

                // Массовая запись данных строки
                if (!empty($rowData)) {
                    $newSheet->fromArray([$rowData], null, 'A' . $currentRow);
                }

                // Применяем стили построчно для экономии памяти
                $this->applyStylesForRow($sourceSheet, $newSheet, $currentRow, $highestColumnIndex);

                $processedRows++;

                // Освобождаем память чаще
                if ($processedRows % 10 === 0) {
                    unset($rowData);
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
