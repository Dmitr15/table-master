<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Jobs\ConvertionJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\UserFile;

error_reporting(E_ALL);
ini_set('display_errors', 1);

class ProcessFileController extends Controller
{
    public function merge(Request $request, string $id)
    {
        Log::info('Starting merge operation', ['file_id' => $id]);
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $mainFile = $user->excelFiles()->findOrFail($id);

            Log::info('Starting merge operation', ['file_id' => $id]);

            $request->validate([
                'merge_file' => 'required|file|mimes:xls,xlsx|max:50000'
            ]);

            // Сохраняем загруженный файл для merge           
            $mergeFilePath = $request->file('merge_file')->store('temp_merge_files');

            ConvertionJob::dispatch($mainFile, $mainFile->original_name, $mainFile->path, "merge", ',', $mergeFilePath);

            return response()->json([
                'success' => true,
                'message' => 'Merge operation started successfully',
                'file_id' => $mainFile->id
            ]);

        } catch (\Exception $e) {
            Log::error('Merge error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function mergeFiles(Request $request)
    {
        Log::info('Starting mergeFiles operation', ['request' => $request->all()]);
        
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $request->validate([
                'file1' => 'required|file|mimes:xlsx,xls,csv|max:50000',
                'file2' => 'required|file|mimes:xlsx,xls,csv|max:50000',
                'format' => 'sometimes|in:xlsx,xls'
            ]);

            // Сохраняем оба файла
            $file1 = $request->file('file1');
            $file2 = $request->file('file2');
            $format = $request->input('format', 'xlsx');

            $file1Path = $file1->store('uploads', 'local');
            $file2Path = $file2->store('uploads', 'local');

            // Создаем записи в базе данных для обоих файлов
            $userFile1 = $user->excelFiles()->create([
                'original_name' => $file1->getClientOriginalName(),
                'path' => $file1Path,
                'size' => $file1->getSize(),
            ]);

            $userFile2 = $user->excelFiles()->create([
                'original_name' => $file2->getClientOriginalName(),
                'path' => $file2Path,
                'size' => $file2->getSize(),
            ]);

            // Создаем запись для результата слияния
            $mergedFile = $user->excelFiles()->create([
                'original_name' => 'merged_file.' . $format,
                'path' => '', // будет заполнено после слияния
                'size' => 0,
                'status' => 'processing',
            ]);

            Log::info('Files uploaded and ready for merge', [
                'file1_id' => $userFile1->id,
                'file2_id' => $userFile2->id,
                'merged_file_id' => $mergedFile->id
            ]);

            // Запускаем процесс слияния
            ConvertionJob::dispatch($mergedFile, 'merged_file.' . $format, '', "merge", ',', '', $userFile1->id, $userFile2->id);

            return response()->json([
                'success' => true,
                'message' => 'Merge operation started successfully',
                'file_id' => $mergedFile->id
            ]);

        } catch (\Exception $e) {
            Log::error('Merge files error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function xlsxToXls_v1(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            return response()->json([
                'success' => false,
                'message' => 'Temporary directory is not writable'
            ], 500);
        }

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $file = $user->excelFiles()->findOrFail($id);

            if (pathinfo($file->path, PATHINFO_EXTENSION) === 'xls') {
                Log::info('Trying to convert xls to xls with xlsxToXls_v1');
                return view('dashboard');
            }

            Log::info('Starting XLSX to XLS conversion with array-based styling', ['file_id' => $id]);

            ConvertionJob::dispatch($file, $file->original_name, $file->path, "xlsxToXls");

            return response()->json([
                'success' => true,
                'message' => 'Conversion started successfully',
                'file_id' => $file->id
            ]);

        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            if (isset($file)) {
                $file->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function split(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            return response()->json([
                'success' => false,
                'message' => 'Temporary directory is not writable'
            ], 500);
        }
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $file = $user->excelFiles()->findOrFail($id);

            Log::info('Starting splitting with array-based styling', ['file_id' => $id]);

            ConvertionJob::dispatch($file, $file->original_name, $file->path, "split");

            return response()->json([
                'success' => true,
                'message' => 'Splitting started successfully',
                'file_id' => $file->id
            ]);
        } catch (\Exception $e) {
            Log::error('Splitting error: ' . $e->getMessage());
            if (isset($file)) {
                $file->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function splitFile(Request $request)
    {
        Log::info('Starting split file operation', ['request' => $request->all()]);
        
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10000',
                'method' => 'required|in:sheets,rows',
                'format' => 'sometimes|in:xlsx,xls,zip',
                'rows_per_file' => 'required_if:method,rows|integer|min:1'
            ]);

            // Сохраняем файл
            $file = $request->file('file');
            $format = $request->input('format', 'zip');
            $method = $request->input('method');
            $rowsPerFile = $request->input('rows_per_file', 100);

            $filePath = $file->store('uploads', 'local');

            // Создаем запись в базе данных для исходного файла
            $sourceFile = $user->excelFiles()->create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $filePath,
                'size' => $file->getSize(),
            ]);

            // Создаем запись для результата разделения
            $splitFile = $user->excelFiles()->create([
                'original_name' => 'split_files.' . $format,
                'path' => '', // будет заполнено после разделения
                'size' => 0,
                'status' => 'processing',
            ]);

            Log::info('File uploaded and ready for splitting', [
                'source_file_id' => $sourceFile->id,
                'split_file_id' => $splitFile->id,
                'method' => $method,
                'format' => $format,
                'rows_per_file' => $rowsPerFile
            ]);

            // Запускаем процесс разделения
            ConvertionJob::dispatch(
                $splitFile, 
                'split_files.' . $format, 
                $sourceFile->path, 
                "split", 
                ',', 
                '', 
                null, 
                null,
                [
                    'method' => $method,
                    'format' => $format,
                    'rows_per_file' => $rowsPerFile,
                    'source_file_id' => $sourceFile->id
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Split operation started successfully',
                'file_id' => $splitFile->id
            ]);

        } catch (\Exception $e) {
            Log::error('Split file error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function xlsToXlsx_v1(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            return response()->json([
                'success' => false,
                'message' => 'Temporary directory is not writable'
            ], 500);
        }

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $file = $user->excelFiles()->findOrFail($id);

            if (pathinfo($file->path, PATHINFO_EXTENSION) === 'xlsx') {
                Log::info('Trying to convert xlsx to xlsx with xlsxToXlsx_v1');
                return view('dashboard');
            }

            Log::info('Starting XLS to XLSX conversion with array-based styling', ['file_id' => $id, '']);

            ConvertionJob::dispatch($file, $file->original_name, $file->path, "xlsToXlsx");

            return response()->json([
                'success' => true,
                'message' => 'Conversion started successfully',
                'file_id' => $file->id
            ]);
        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            if (isset($file)) {
                $file->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function excelToOds_v1(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            return response()->json([
                'success' => false,
                'message' => 'Temporary directory is not writable'
            ], 500);
        }

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $file = $user->excelFiles()->findOrFail($id);

            Log::info('Starting Excel to Ods conversion with array-based styling', ['file_id' => $id]);

            ConvertionJob::dispatch($file, $file->original_name, $file->path, "excelToOds");

            return response()->json([
                'success' => true,
                'message' => 'Conversion started successfully',
                'file_id' => $file->id
            ]);
        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            if (isset($file)) {
                $file->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function excelToCsv_v1(string $id, string $delimiter = ',')
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            throw new \Exception("Temporary directory is not writable");
        }

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $file = $user->excelFiles()->findOrFail($id);

            Log::info('file', ['file' => $file]);

            ConvertionJob::dispatch($file, $file->original_name, $file->path, "excelToCsv");

            return response()->json([
                'success' => true,
                'message' => 'Conversion started successfully',
                'file_id' => $file->id
            ]);
        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            if (isset($file)) {
                $file->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function ExcelToHtml_v1(string $id)
    {
        $tempDir = sys_get_temp_dir();
        if (!is_writable($tempDir)) {
            Log::error('Temp directory is not writable', ['path' => $tempDir]);
            return response()->json([
                'success' => false,
                'message' => 'Temporary directory is not writable'
            ], 500);
        }

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $file = $user->excelFiles()->findOrFail($id);

            Log::info('Starting Excel to HTML conversion', ['file_id' => $id]);

            ConvertionJob::dispatch($file, $file->original_name, $file->path, "ExcelToHtml");

            return response()->json([
                'success' => true,
                'message' => 'Conversion started successfully',
                'file_id' => $file->id
            ]);
        } catch (\Exception $e) {
            Log::error('Conversion error: ' . $e->getMessage());
            if (isset($file)) {
                $file->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function analyzeFile(Request $request)
    {
        Log::info('Starting file analysis', ['request' => $request->all()]);
        
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10000',
                'analysis_type' => 'sometimes|in:financial,sales,inventory,custom',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after_or_equal:start_date'
            ]);

            // Сохраняем файл
            $file = $request->file('file');
            $analysisType = $request->input('analysis_type', 'financial');
            
            // Сохраняем файл во временную директорию
            $filePath = $file->store('temp_analysis', 'local');
            
            Log::info('File stored for analysis', [
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $filePath,
                'file_exists' => Storage::disk('local')->exists($filePath)
            ]);

            // Выполняем анализ
            $analysisResults = $this->performDataAnalysis($filePath, $analysisType, $request->all());

            // Удаляем временный файл
            Storage::disk('local')->delete($filePath);

            return response()->json([
                'success' => true,
                'message' => 'Analysis completed successfully',
                'data' => $analysisResults
            ]);

        } catch (\Exception $e) {
            Log::error('File analysis error: ' . $e->getMessage());
            
            // Удаляем временный файл в случае ошибки
            if (isset($filePath)) {
                Storage::disk('local')->delete($filePath);
            }
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function performDataAnalysis($filePath, $analysisType, $options)
    {
        try {
            Log::info('Starting data analysis', [
                'file_path' => $filePath,
                'file_exists' => Storage::disk('local')->exists($filePath)
            ]);

            if (!Storage::disk('local')->exists($filePath)) {
                throw new \Exception("File not found in storage: " . $filePath);
            }

            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
            
            if ($fileExtension === 'csv') {
                $data = $this->analyzeCsvFile($filePath);
            } else {
                $data = $this->analyzeExcelFile($filePath);
            }

            return $this->generateAnalysisReport($data, $analysisType, $options);

        } catch (\Exception $e) {
            Log::error('Data analysis error: ' . $e->getMessage());
            throw new \Exception('Ошибка анализа данных: ' . $e->getMessage());
        }
    }

    private function analyzeExcelFile($filePath)
    {
        Log::info('Analyzing Excel file', ['path' => $filePath]);
        
        if (!Storage::disk('local')->exists($filePath)) {
            throw new \Exception("Excel file not found: " . $filePath);
        }

        $fileContent = Storage::disk('local')->get($filePath);
        $tempFilePath = tempnam(sys_get_temp_dir(), 'analysis_excel_');
        file_put_contents($tempFilePath, $fileContent);

        try {
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            $reader = ($fileExtension === 'xlsx') ? 
                IOFactory::createReader('Xlsx') : 
                IOFactory::createReader('Xls');
            
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tempFilePath);
            
            $data = [];
            $sheet = $spreadsheet->getActiveSheet();
            
            $highestRow = $sheet->getHighestDataRow();
            $highestColumn = $sheet->getHighestDataColumn();
            
            Log::info('Excel file dimensions', [
                'rows' => $highestRow,
                'columns' => $highestColumn
            ]);
            
            // Читаем заголовки
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn && count($headers) < 10; $col++) {
                $value = $sheet->getCell($col . '1')->getValue();
                $headers[] = $value ? $this->sanitizeHeader($value) : "Column_$col";
            }
            
            Log::info('Excel headers detected', ['headers' => $headers]);
            
            // Читаем данные
            $maxRows = min($highestRow, 1000);
            for ($row = 2; $row <= $maxRows; $row++) {
                $rowData = [];
                $hasData = false;
                
                for ($colIndex = 0; $colIndex < count($headers); $colIndex++) {
                    $col = chr(65 + $colIndex);
                    $value = $sheet->getCell($col . $row)->getValue();
                    if ($value !== null && $value !== '') {
                        $hasData = true;
                    }
                    $rowData[$headers[$colIndex]] = $value;
                }
                
                if ($hasData) {
                    $data[] = $rowData;
                }
            }
            
            Log::info('Excel file analyzed successfully', [
                'rows_processed' => count($data),
                'headers' => $headers
            ]);
            
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            
            return $data;
            
        } finally {
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }
    }



    private function analyzeCsvFile($filePath)
    {
        Log::info('Analyzing CSV file', ['path' => $filePath]);
        
        if (!Storage::disk('local')->exists($filePath)) {
            throw new \Exception("CSV file not found: " . $filePath);
        }

        $fileContent = Storage::disk('local')->get($filePath);
        
        // Проверяем содержимое файла
        if (empty($fileContent)) {
            throw new \Exception("CSV file is empty");
        }
        
        // Конвертируем в UTF-8 если нужно
        $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'Windows-1251', 'ISO-8859-1'], true);
        if ($encoding !== 'UTF-8') {
            $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
        }
        
        $tempFilePath = tempnam(sys_get_temp_dir(), 'analysis_csv_');
        file_put_contents($tempFilePath, $fileContent);

        try {
            $data = [];
            if (($handle = fopen($tempFilePath, 'r')) !== FALSE) {
                // Читаем заголовки
                $headers = fgetcsv($handle, 1000, ',');
                if ($headers === FALSE) {
                    throw new \Exception("Cannot read CSV headers - file may be empty or corrupted");
                }
                
                // Санитизируем заголовки
                $headers = array_map([$this, 'sanitizeHeader'], $headers);
                
                Log::info('CSV headers detected', ['headers' => $headers]);
                
                $rowCount = 0;
                $maxRows = 1000;
                
                while ($rowCount < $maxRows && ($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $rowData = [];
                    $hasData = false;
                    
                    foreach ($headers as $index => $header) {
                        $value = $row[$index] ?? null;
                        if ($value !== null && $value !== '') {
                            $hasData = true;
                            // Преобразуем числовые значения
                            if (is_numeric($value)) {
                                $value = floatval($value);
                            }
                        }
                        $rowData[$header] = $value;
                    }
                    
                    if ($hasData) {
                        $data[] = $rowData;
                        $rowCount++;
                    }
                }
                fclose($handle);
            } else {
                throw new \Exception("Cannot open CSV file for reading");
            }
            
            Log::info('CSV file analyzed successfully', [
                'rows_processed' => count($data),
                'headers' => $headers ?? []
            ]);
            
            return $data;
            
        } finally {
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }
    }

    private function sanitizeHeader($header)
    {
        // Убираем специальные символы и приводим к нижнему регистру
        $header = preg_replace('/[^\w\s]/u', '', $header);
        $header = trim($header);
        $header = str_replace(' ', '_', strtolower($header));
        return $header ?: 'column';
    }

    private function generateAnalysisReport($data, $analysisType, $options)
    {
        // Анализ данных и генерация отчета
        $report = [
            'metrics' => $this->calculateMetrics($data),
            'monthly_data' => $this->aggregateMonthlyData($data),
            'expenses_by_category' => $this->categorizeExpenses($data),
            'detailed_data' => $this->prepareDetailedData($data),
            'insights' => $this->generateInsights($data)
        ];
        
        return $report;
    }

    private function calculateMetrics($data)
    {
        if (empty($data)) {
            return $this->getEmptyMetrics();
        }

        $totalIncome = 0;
        $totalExpenses = 0;
        $monthlyData = [];

        // Автоматически определяем столбцы
        $financialColumns = $this->detectFinancialColumns($data[0]);
        
        foreach ($data as $row) {
            $rowIncome = 0;
            $rowExpenses = 0;
            $date = null;

            // Анализируем структуру данных
            if ($this->hasSeparateIncomeExpenseColumns($financialColumns)) {
                // Структура с отдельными столбцами Доход/Расход
                list($rowIncome, $rowExpenses) = $this->analyzeSeparateColumns($row, $financialColumns);
            } else {
                // Структура с одним столбцом Amount
                list($rowIncome, $rowExpenses) = $this->analyzeSingleAmountColumn($row, $financialColumns);
            }

            $totalIncome += $rowIncome;
            $totalExpenses += $rowExpenses;

            // Определяем дату для группировки по месяцам
            $date = $this->extractDateFromRow($row, $financialColumns);
            if ($date) {
                $monthKey = $date->format('Y-m');
                if (!isset($monthlyData[$monthKey])) {
                    $monthlyData[$monthKey] = [
                        'income' => 0,
                        'expenses' => 0,
                        'date' => $date
                    ];
                }
                $monthlyData[$monthKey]['income'] += $rowIncome;
                $monthlyData[$monthKey]['expenses'] += $rowExpenses;
            }
        }

        $netProfit = $totalIncome - $totalExpenses;
        $profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

        // Рассчитываем реальные изменения
        $changes = $this->calculateRealChanges($monthlyData);
        
        return [
            'total_income' => round($totalIncome, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($netProfit, 2),
            'profit_margin' => round($profitMargin, 2),
            'income_change' => $changes['income_change'],
            'expenses_change' => $changes['expenses_change'],
            'profit_change' => $changes['profit_change'],
            'growth_rate' => $changes['growth_rate'],
            'growth_trend' => $changes['growth_trend'],
            'data_structure' => $this->getDataStructureType($financialColumns)
        ];
    }

    private function getEmptyMetrics()
    {
        return [
            'total_income' => 0,
            'total_expenses' => 0,
            'net_profit' => 0,
            'profit_margin' => 0,
            'income_change' => 0,
            'expenses_change' => 0,
            'profit_change' => 0,
            'growth_rate' => 0,
            'growth_trend' => 'Недостаточно данных',
            'data_structure' => 'unknown'
        ];
    }

    private function getDataStructureType($financialColumns)
    {
        if (!empty($financialColumns['income']) && !empty($financialColumns['expense'])) {
            return 'separate_columns';
        } elseif (!empty($financialColumns['income']) && empty($financialColumns['expense'])) {
            return 'income_only';
        } elseif (empty($financialColumns['income']) && !empty($financialColumns['expense'])) {
            return 'expense_only';
        } else {
            return 'unknown';
        }
    }

    private function extractDateFromRow($row, $financialColumns)
    {
        // Сначала ищем в определенных столбцах дат
        foreach ($financialColumns['date'] as $dateColumn) {
            if (isset($row[$dateColumn])) {
                $date = $this->parseDate($row[$dateColumn]);
                if ($date) return $date;
            }
        }
        
        // Потом ищем в любом столбце
        foreach ($row as $value) {
            $date = $this->parseDate($value);
            if ($date) return $date;
        }
        
        return null;
    }

    private function analyzeSingleAmountColumn($row, $financialColumns)
    {
        $income = 0;
        $expenses = 0;

        // Ищем столбец с суммами
        $amountColumn = $this->findAmountColumn($financialColumns);
        
        if ($amountColumn && isset($row[$amountColumn])) {
            $value = $row[$amountColumn];
            if (is_numeric($value)) {
                $floatValue = floatval($value);
                if ($floatValue > 0) {
                    $income = $floatValue;
                } else {
                    $expenses = abs($floatValue);
                }
            }
        }

        return [$income, $expenses];
    }

    private function findAmountColumn($financialColumns)
    {
        // Приоритетные названия для столбца с суммой
        $amountKeywords = ['сумма', 'amount', 'сум', 'значение', 'value', 'итого'];
        
        foreach ($financialColumns as $type => $columns) {
            foreach ($columns as $column) {
                $lowerColumn = strtolower($column);
                foreach ($amountKeywords as $keyword) {
                    if (str_contains($lowerColumn, $keyword)) {
                        return $column;
                    }
                }
            }
        }
        
        // Если не нашли, берем первый числовой столбец
        if (!empty($financialColumns['income'])) {
            return $financialColumns['income'][0];
        }
        
        return null;
    }

    private function analyzeSeparateColumns($row, $financialColumns)
    {
        $income = 0;
        $expenses = 0;

        // Обрабатываем столбцы доходов
        foreach ($financialColumns['income'] as $incomeColumn) {
            $value = $row[$incomeColumn] ?? 0;
            if (is_numeric($value)) {
                $income += max(0, floatval($value)); // Только положительные значения
            }
        }

        // Обрабатываем столбцы расходов
        foreach ($financialColumns['expense'] as $expenseColumn) {
            $value = $row[$expenseColumn] ?? 0;
            if (is_numeric($value)) {
                $expenses += max(0, floatval($value)); // Только положительные значения
            }
        }

        return [$income, $expenses];
    }

    private function hasSeparateIncomeExpenseColumns($financialColumns)
    {
        return !empty($financialColumns['income']) && !empty($financialColumns['expense']);
    }

    private function assessDataQuality($data, $financialColumns)
    {
        $totalRows = count($data);
        $rowsWithNumbers = 0;
        $rowsWithDates = 0;
        
        foreach ($data as $row) {
            $hasNumbers = false;
            $hasDate = false;
            
            foreach ($row as $value) {
                if (is_numeric($value) && $value != 0) {
                    $hasNumbers = true;
                }
                if ($this->parseDate($value) !== null) {
                    $hasDate = true;
                }
            }
            
            if ($hasNumbers) $rowsWithNumbers++;
            if ($hasDate) $rowsWithDates++;
        }
        
        return [
            'total_rows' => $totalRows,
            'data_completeness' => round(($rowsWithNumbers / $totalRows) * 100, 1),
            'temporal_coverage' => round(($rowsWithDates / $totalRows) * 100, 1),
            'columns_detected' => $financialColumns
        ];
    }

    private function analyzeFinancialHealth($income, $expenses, $profit, $margin)
    {
        if ($income == 0) return 'Нет данных';
        
        $expenseRatio = $expenses / $income;
        $profitability = $margin;

        if ($profitability > 20) {
            return 'Отличное';
        } elseif ($profitability > 10) {
            return 'Хорошее';
        } elseif ($profitability > 0) {
            return 'Удовлетворительное';
        } else {
            return 'Проблемное';
        }
    }

    private function calculateRealChanges($monthlyData)
    {
        if (count($monthlyData) < 2) {
            return [
                'income_change' => 0,
                'expenses_change' => 0,
                'profit_change' => 0,
                'growth_rate' => 0,
                'growth_trend' => 'Недостаточно данных',
                'period_comparison' => null
            ];
        }

        // Сортируем по дате
        uasort($monthlyData, function($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        // Берем два последних полных периода
        $periods = array_slice($monthlyData, -2);
        $current = end($periods);
        $previous = prev($periods);

        $currentProfit = $current['income'] - $current['expenses'];
        $previousProfit = $previous['income'] - $previous['expenses'];

        $incomeChange = $previous['income'] > 0 ? 
            (($current['income'] - $previous['income']) / $previous['income']) * 100 : 0;
        
        $expensesChange = $previous['expenses'] > 0 ? 
            (($current['expenses'] - $previous['expenses']) / $previous['expenses']) * 100 : 0;
        
        $profitChange = $previousProfit != 0 ? 
            (($currentProfit - $previousProfit) / abs($previousProfit)) * 100 : 0;

        // Расчет темпа роста (CAGR-like)
        $firstPeriod = reset($monthlyData);
        $lastPeriod = end($monthlyData);
        
        $firstProfit = $firstPeriod['income'] - $firstPeriod['expenses'];
        $lastProfit = $lastPeriod['income'] - $lastPeriod['expenses'];
        
        $monthsDiff = $this->getMonthsDifference($firstPeriod['date'], $lastPeriod['date']);
        $growthRate = $monthsDiff > 0 && $firstProfit != 0 ? 
            (pow($lastProfit / $firstProfit, 12 / $monthsDiff) - 1) * 100 : 0;

        return [
            'income_change' => round($incomeChange, 1),
            'expenses_change' => round($expensesChange, 1),
            'profit_change' => round($profitChange, 1),
            'growth_rate' => round($growthRate, 1),
            'growth_trend' => $growthRate > 5 ? 'Сильный рост' : 
                            ($growthRate > 0 ? 'Умеренный рост' : 
                            ($growthRate < -5 ? 'Сильный спад' : 'Стабильный')),
            'period_comparison' => [
                'current_period' => [
                    'income' => $current['income'],
                    'expenses' => $current['expenses'],
                    'profit' => $currentProfit
                ],
                'previous_period' => [
                    'income' => $previous['income'],
                    'expenses' => $previous['expenses'],
                    'profit' => $previousProfit
                ]
            ]
        ];
    }

    private function getMonthsDifference($date1, $date2)
    {
        $diff = $date1->diff($date2);
        return $diff->y * 12 + $diff->m + ($diff->d > 15 ? 1 : 0);
    }

    private function isDateColumn($column, $value)
    {
        $lowerColumn = strtolower($column);
        $dateKeywords = ['дата', 'date', 'время', 'time'];
        
        foreach ($dateKeywords as $keyword) {
            if (str_contains($lowerColumn, $keyword)) {
                return true;
            }
        }
        
        // Пытаемся распарсить значение как дату
        return $this->parseDate($value) !== null;
    }

    private function parseDate($value)
    {
        if ($value instanceof \DateTime) {
            return $value;
        }
        
        if (is_numeric($value) && $value > 25569) { // Excel timestamp
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
        }
        
        try {
            return new \DateTime($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function isIncomeColumn($column, $value)
    {
        $lowerColumn = strtolower($column);
        $incomeKeywords = ['Доход', 'income', 'revenue', 'выручка', 'продаж', 'sales'];
        
        foreach ($incomeKeywords as $keyword) {
            if (str_contains($lowerColumn, $keyword)) {
                return $value > 0;
            }
        }
        
        // Эвристика: если название столбца не определено, но значение положительное
        return $value > 0 && !$this->isExpenseColumn($column, $value);
    }

    private function isExpenseColumn($column, $value)
    {
        $lowerColumn = strtolower($column);
        $expenseKeywords = ['Расход', 'expense', 'cost', 'затрат', 'издержк', 'оплат'];
        
        foreach ($expenseKeywords as $keyword) {
            if (str_contains($lowerColumn, $keyword)) {
                return true;
            }
        }
        
        // Эвристика: если значение отрицательное, считаем расходом
        return $value < 0;
    }

    private function detectFinancialColumns($sampleRow)
    {
        $financialColumns = [
            'income' => [],
            'expense' => [],
            'date' => [],
            'category' => []
        ];

        foreach ($sampleRow as $column => $value) {
            $lowerColumn = strtolower($column);
            
            // Определяем столбцы доходов (ищем конкретные названия)
            if (preg_match('/(^Доход$|^income$|^выручка$|^revenue$|^прибыль$|^profit$)/', $lowerColumn)) {
                $financialColumns['income'][] = $column;
            }
            
            // Определяем столбцы расходов (ищем конкретные названия)
            if (preg_match('/(^Расход$|^expense$|^cost$|^затраты$|^издержки$)/', $lowerColumn)) {
                $financialColumns['expense'][] = $column;
            }
            
            // Определяем столбцы дат
            if (preg_match('/(дата|date|время|time|период|period)/', $lowerColumn)) {
                $financialColumns['date'][] = $column;
            }
            
            // Определяем категории
            if (preg_match('/(категор|category|тип|type|вид)/', $lowerColumn)) {
                $financialColumns['category'][] = $column;
            }
            
            // Если есть числовые значения, но столбец не определен - проверяем по значению
            if (is_numeric($value) && empty($financialColumns['income']) && empty($financialColumns['expense'])) {
                // Пытаемся определить по названию столбца
                if (preg_match('/(сумм|amount|значен|value)/', $lowerColumn)) {
                    // Это может быть общий столбец сумм
                    $financialColumns['income'][] = $column;
                    $financialColumns['expense'][] = $column;
                }
            }
        }

        return $financialColumns;
    }

    private function aggregateMonthlyData($data)
    {
        // Создаем демо-данные по месяцам на основе реальных данных
        $monthlyData = [];
        $months = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
        
        $totalIncome = 0;
        $totalExpenses = 0;
        
        // Считаем общие суммы
        foreach ($data as $row) {
            foreach ($row as $key => $value) {
                if (is_numeric($value)) {
                    if ($value > 0) {
                        $totalIncome += floatval($value);
                    } else {
                        $totalExpenses += abs(floatval($value));
                    }
                }
            }
        }
        
        // Распределяем по месяцам
        $monthsCount = min(6, count($months)); // Показываем до 6 месяцев
        $monthlyIncome = $totalIncome / $monthsCount;
        $monthlyExpenses = $totalExpenses / $monthsCount;
        
        for ($i = 0; $i < $monthsCount; $i++) {
            // Добавляем случайные колебания
            $income = $monthlyIncome * (0.8 + (rand(0, 40) / 100));
            $expenses = $monthlyExpenses * (0.7 + (rand(0, 60) / 100));
            
            $monthlyData[] = [
                'month' => $months[$i],
                'income' => round($income, 2),
                'expenses' => round($expenses, 2)
            ];
        }
        
        return $monthlyData;
    }

    private function categorizeExpenses($data)
    {
        $categories = [];
        $subcategories = [];
        
        foreach ($data as $row) {
            $expense = 0;
            
            // Суммируем расходы из столбца Расход
            if (isset($row['Расход']) && is_numeric($row['Расход'])) {
                $expense = floatval($row['Расход']);
            }
            
            if ($expense > 0) {
                // Определяем категорию
                $category = $row['Основная категория'] ?? $row['Категория'] ?? 'Прочие';
                $subcategory = $row['Подкатегория'] ?? 'Основные';
                
                if (!isset($categories[$category])) {
                    $categories[$category] = 0;
                }
                $categories[$category] += $expense;
                
                // Для детализации по подкатегориям
                $subcatKey = $category . '|' . $subcategory;
                if (!isset($subcategories[$subcatKey])) {
                    $subcategories[$subcatKey] = [
                        'category' => $category,
                        'subcategory' => $subcategory,
                        'amount' => 0
                    ];
                }
                $subcategories[$subcatKey]['amount'] += $expense;
            }
        }
        
        // Если нет детальных подкатегорий, используем основные категории
        if (empty($subcategories)) {
            $result = [];
            foreach ($categories as $category => $amount) {
                if ($amount > 0) {
                    $result[] = [
                        'category' => $category,
                        'amount' => round($amount, 2)
                    ];
                }
            }
        } else {
            // Используем подкатегории для более детальной визуализации
            $result = array_values($subcategories);
            foreach ($result as &$item) {
                $item['amount'] = round($item['amount'], 2);
            }
        }
        
        // Сортируем по убыванию суммы
        usort($result, function($a, $b) {
            return $b['amount'] - $a['amount'];
        });
        
        return $result;
    }

    private function prepareDetailedData($data)
    {
        // Берем первые 10 строк для детального отображения
        $detailedData = array_slice($data, 0, 10);
        
        // Преобразуем данные для отображения
        $result = [];
        foreach ($detailedData as $index => $row) {
            $income = 0;
            $expense = 0;
            
            foreach ($row as $key => $value) {
                if (is_numeric($value)) {
                    if ($value > 0) {
                        $income += floatval($value);
                    } else {
                        $expense += abs(floatval($value));
                    }
                }
            }
            
            $result[] = [
                'date' => date('Y-m-d', strtotime("-$index days")), // Демо-даты
                'category' => 'Данные ' . ($index + 1),
                'description' => 'Запись из файла',
                'income' => round($income, 2),
                'expense' => round($expense, 2),
                'profit' => round($income - $expense, 2)
            ];
        }
        
        return $result;
    }

    private function generateInsights($data)
    {
        $totalRows = count($data);
        $numericColumns = 0;
        $totalValue = 0;
        
        if (!empty($data)) {
            $firstRow = $data[0];
            foreach ($firstRow as $value) {
                if (is_numeric($value)) {
                    $numericColumns++;
                }
            }
            
            // Считаем общую сумму
            foreach ($data as $row) {
                foreach ($row as $value) {
                    if (is_numeric($value)) {
                        $totalValue += abs(floatval($value));
                    }
                }
            }
        }
        
        return [
            'positive' => "Проанализировано {$totalRows} записей с {$numericColumns} числовыми столбцами",
            'warning' => $totalValue == 0 ? "В файле не обнаружено числовых данных для анализа" : "Общая сумма операций: ₽" . number_format($totalValue, 2),
            'recommendation' => "Рекомендуется проверить структуру данных и убедиться в наличии числовых столбцов для более точного анализа"
        ];
    }
}