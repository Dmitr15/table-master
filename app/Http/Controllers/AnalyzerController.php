<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AnalyzerController extends Controller
{
    public function index()
    {
        return view('analyzer');
    }

    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'report_type' => 'required|in:financial,sales,expenses,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        try {
            $file = $request->file('file');
            $reportType = $request->input('report_type');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Сохраняем временный файл
            $tempPath = $file->store('temp');
            $fullPath = storage_path('app/' . $tempPath);

            // Анализируем данные
            $analysisResult = $this->analyzeData($fullPath, $reportType, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $analysisResult
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка анализа: ' . $e->getMessage()
            ], 500);
        }
    }

    private function analyzeData($filePath, $reportType, $startDate, $endDate)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $data = [];
        $headers = [];
        
        // Парсим данные из Excel
        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getCalculatedValue();
            }
            
            if (empty($headers)) {
                $headers = $rowData;
            } else {
                $data[] = array_combine($headers, $rowData);
            }
        }

        // Анализируем данные в зависимости от типа отчета
        return $this->generateReport($data, $reportType, $startDate, $endDate);
    }

    private function generateReport($data, $reportType, $startDate, $endDate)
    {
        // Здесь будет логика анализа данных и генерации отчета
        // Пока возвращаем демо-данные
        
        return [
            'metrics' => [
                'total_income' => 1250000,
                'total_expenses' => 875000,
                'net_profit' => 375000,
                'income_change' => 15.2,
                'expenses_change' => 8.7,
                'profit_change' => 28.5
            ],
            'monthly_data' => [
                ['month' => 'Янв', 'income' => 98000, 'expenses' => 72000],
                ['month' => 'Фев', 'income' => 105000, 'expenses' => 68000],
                ['month' => 'Мар', 'income' => 112000, 'expenses' => 75000],
            ],
            'expenses_by_category' => [
                ['category' => 'Зарплаты', 'amount' => 350000],
                ['category' => 'Аренда', 'amount' => 180000],
                ['category' => 'Маркетинг', 'amount' => 120000],
            ]
        ];
    }
}