<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConverterController extends Controller
{
    public function index()
    {
        return view('converter');
    }

    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'format' => 'required|in:json,csv,xml,tsv,pdf,html,xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            $format = $request->input('format');
            $includeHeaders = $request->boolean('include_headers', true);
            $prettyPrint = $request->boolean('pretty_print', false);

            // Сохраняем временный файл
            $tempPath = $file->store('temp');
            $fullPath = storage_path('app/' . $tempPath);

            // Обрабатываем файл в зависимости от формата
            $resultPath = $this->convertFile($fullPath, $format, $includeHeaders, $prettyPrint);

            // Возвращаем файл для скачивания
            $downloadName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $format;
            
            return response()->download($resultPath, $downloadName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка конвертации: ' . $e->getMessage());
        }
    }

    private function convertFile($filePath, $format, $includeHeaders, $prettyPrint)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $data = [];
        $headers = [];
        
        // Получаем данные
        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getCalculatedValue();
            }
            
            if (empty($headers) && $includeHeaders) {
                $headers = $rowData;
            } else {
                $data[] = $includeHeaders ? array_combine($headers, $rowData) : $rowData;
            }
        }

        // Конвертируем в нужный формат
        $outputPath = storage_path('app/temp/converted_' . time() . '.' . $format);
        
        switch ($format) {
            case 'json':
                $jsonOptions = $prettyPrint ? JSON_PRETTY_PRINT : 0;
                file_put_contents($outputPath, json_encode($data, $jsonOptions));
                break;
                
            case 'csv':
                $writer = new Csv($spreadsheet);
                $writer->save($outputPath);
                break;
                
            case 'xlsx':
                $writer = new Xlsx($spreadsheet);
                $writer->save($outputPath);
                break;
                
            default:
                throw new \Exception("Формат {$format} пока не поддерживается");
        }

        return $outputPath;
    }
}