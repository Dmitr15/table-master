<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

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

            // Создаем временную директорию если не существует
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Сохраняем временный файл
            $tempPath = $file->store('temp');
            $fullPath = storage_path('app/' . $tempPath);

            // Обрабатываем файл в зависимости от формата
            $result = $this->convertFile($fullPath, $format, [
                'include_headers' => $includeHeaders,
                'pretty_print' => $prettyPrint,
                'original_filename' => $file->getClientOriginalName()
            ]);

            // Удаляем временный загруженный файл
            unlink($fullPath);

            // Возвращаем файл для скачивания
            return response()->download($result['path'], $result['filename'])
                            ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Для AJAX запросов возвращаем JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Ошибка конвертации: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Ошибка конвертации: ' . $e->getMessage());
        }
    }

    private function convertFile($filePath, $format, $options)
{
    \Log::info('🚀 STARTING FILE CONVERSION', [
        'file_path' => $filePath,
        'target_format' => $format,
        'options' => $options,
        'file_exists' => file_exists($filePath),
        'file_size' => file_exists($filePath) ? filesize($filePath) : 0,
        'file_readable' => is_readable($filePath)
    ]);

    try {
        // Шаг 1: Проверка файла
        \Log::info('📁 CHECKING FILE ACCESS', [
            'file_path' => $filePath,
            'exists' => file_exists($filePath),
            'size' => file_exists($filePath) ? filesize($filePath) : 'not_found',
            'readable' => is_readable($filePath),
            'writable' => is_writable(dirname($filePath))
        ]);

        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new \Exception("File not readable: {$filePath}");
        }

        // Шаг 2: Загрузка spreadsheet
        \Log::info('📊 LOADING SPREADSHEET', [
            'file_type' => pathinfo($filePath, PATHINFO_EXTENSION),
            'memory_usage_before' => memory_get_usage(true) / 1024 / 1024 . ' MB'
        ]);

        $startTime = microtime(true);
        $spreadsheet = IOFactory::load($filePath);
        $loadTime = round(microtime(true) - $startTime, 3);

        \Log::info('✅ SPREADSHEET LOADED SUCCESSFULLY', [
            'load_time_seconds' => $loadTime,
            'sheet_count' => $spreadsheet->getSheetCount(),
            'sheet_names' => $spreadsheet->getSheetNames(),
            'memory_usage_after' => memory_get_usage(true) / 1024 / 1024 . ' MB'
        ]);

        $worksheet = $spreadsheet->getActiveSheet();
        
        \Log::info('📝 ACTIVE WORKSHEET INFO', [
            'title' => $worksheet->getTitle(),
            'highest_row' => $worksheet->getHighestRow(),
            'highest_column' => $worksheet->getHighestColumn(),
            'highest_data_row' => $worksheet->getHighestDataRow(),
            'highest_data_column' => $worksheet->getHighestDataColumn()
        ]);

        // Шаг 3: Подготовка выходного файла
        $originalName = pathinfo($options['original_filename'], PATHINFO_FILENAME);
        $outputFilename = $originalName . '.' . $format;
        $outputPath = storage_path('app/temp/converted_' . uniqid() . '_' . $outputFilename);

        \Log::info('📄 PREPARING OUTPUT', [
            'original_name' => $originalName,
            'output_filename' => $outputFilename,
            'output_path' => $outputPath,
            'output_dir_writable' => is_writable(dirname($outputPath))
        ]);

        // Шаг 4: Конвертация по формату
        \Log::info('🔄 STARTING CONVERSION', [
            'format' => $format,
            'conversion_method' => 'convertTo' . ucfirst($format)
        ]);

        $conversionStart = microtime(true);
        
        switch ($format) {
            case 'json':
                \Log::info('🟢 CONVERTING TO JSON');
                $this->convertToJson($spreadsheet, $outputPath, $options);
                break;

            case 'csv':
                \Log::info('🟢 CONVERTING TO CSV');
                $writer = new Csv($spreadsheet);
                $writer->setDelimiter(',');
                $writer->setEnclosure('"');
                $writer->setLineEnding("\n");
                $writer->setSheetIndex(0);
                $writer->save($outputPath);
                break;

            case 'tsv':
                \Log::info('🟢 CONVERTING TO TSV');
                $writer = new Csv($spreadsheet);
                $writer->setDelimiter("\t");
                $writer->setEnclosure('');
                $writer->setLineEnding("\n");
                $writer->setSheetIndex(0);
                $writer->save($outputPath);
                break;

            case 'xlsx':
                \Log::info('🟢 CONVERTING TO XLSX');
                $writer = new Xlsx($spreadsheet);
                $writer->save($outputPath);
                break;

            case 'xls':
                \Log::info('🟢 CONVERTING TO XLS');
                $writer = new Xls($spreadsheet);
                $writer->save($outputPath);
                break;

            case 'html':
                \Log::info('🟢 CONVERTING TO HTML');
                $writer = new Html($spreadsheet);
                $writer->save($outputPath);
                break;

            case 'xml':
                \Log::info('🟢 CONVERTING TO XML');
                $this->convertToXml($spreadsheet, $outputPath, $options);
                break;

            case 'pdf':
                \Log::info('🟢 CONVERTING TO PDF');
                $this->convertToPdf($spreadsheet, $outputPath, $options);
                break;

            default:
                \Log::error('🔴 UNSUPPORTED FORMAT', ['format' => $format]);
                throw new \Exception("Формат {$format} пока не поддерживается");
        }

        $conversionTime = round(microtime(true) - $conversionStart, 3);
        
        // Шаг 5: Проверка результата
        \Log::info('✅ CONVERSION COMPLETED', [
            'conversion_time_seconds' => $conversionTime,
            'total_time_seconds' => round(microtime(true) - $startTime, 3),
            'output_file_exists' => file_exists($outputPath),
            'output_file_size' => file_exists($outputPath) ? filesize($outputPath) : 0,
            'memory_usage_final' => memory_get_usage(true) / 1024 / 1024 . ' MB',
            'peak_memory_usage' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB'
        ]);

        if (!file_exists($outputPath)) {
            throw new \Exception("Output file was not created: {$outputPath}");
        }

        if (filesize($outputPath) === 0) {
            \Log::warning('⚠️ OUTPUT FILE IS EMPTY', ['output_path' => $outputPath]);
        }

        return [
            'path' => $outputPath,
            'filename' => $outputFilename
        ];

    } catch (\Exception $e) {
        \Log::error('🔴 CONVERSION FAILED', [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'error_trace' => $e->getTraceAsString(),
            'format' => $format,
            'file_path' => $filePath
        ]);
        
        throw $e;
    }
}

    private function convertToJson($spreadsheet, $outputPath, $options)
    {
        \Log::info('📋 STARTING JSON CONVERSION', [
            'include_headers' => $options['include_headers'],
            'pretty_print' => $options['pretty_print']
        ]);

        try {
            $worksheet = $spreadsheet->getActiveSheet();
            $data = [];
            $headers = [];
            
            $rowCount = 0;
            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                
                foreach ($cellIterator as $cellIndex => $cell) {
                    $value = $cell->getCalculatedValue();
                    
                    if ($rowIndex === 1 && $options['include_headers']) {
                        $headers[$cellIndex] = $value ?: "column_" . ($cellIndex + 1);
                    } else {
                        if ($options['include_headers']) {
                            $header = $headers[$cellIndex] ?? "column_" . ($cellIndex + 1);
                            $rowData[$header] = $value;
                        } else {
                            $rowData[] = $value;
                        }
                    }
                }
                
                if ($rowIndex === 1 && $options['include_headers']) {
                    continue;
                }
                
                if (!empty(array_filter($rowData, function($v) { 
                    return $v !== null && $v !== ''; 
                }))) {
                    $data[] = $rowData;
                    $rowCount++;
                }
            }
            
            \Log::info('📊 JSON DATA PREPARED', [
                'total_rows' => $rowCount,
                'total_columns' => !empty($headers) ? count($headers) : 'unknown',
                'data_size' => count($data)
            ]);

            $jsonOptions = $options['pretty_print'] 
                ? JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                : JSON_UNESCAPED_UNICODE;
                
            file_put_contents($outputPath, json_encode($data, $jsonOptions));
            
            \Log::info('✅ JSON CONVERSION COMPLETED', [
                'output_size' => filesize($outputPath),
                'output_path' => $outputPath
            ]);

        } catch (\Exception $e) {
            \Log::error('🔴 JSON CONVERSION FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function convertToXml($spreadsheet, $outputPath, $options)
    {
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];
        $headers = [];

        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cellIndex => $cell) {
                $value = $cell->getCalculatedValue();

                if ($rowIndex === 1 && $options['include_headers']) {
                    $headers[$cellIndex] = $this->sanitizeXmlTag($value) ?: "column_" . ($cellIndex + 1);
                } else {
                    if ($options['include_headers']) {
                        $header = $headers[$cellIndex] ?? "column_" . ($cellIndex + 1);
                        $rowData[$header] = $value;
                    } else {
                        $rowData["column_" . ($cellIndex + 1)] = $value;
                    }
                }
            }

            if ($rowIndex === 1 && $options['include_headers']) {
                continue;
            }

            if (!empty(array_filter($rowData))) {
                $data[] = $rowData;
            }
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><data></data>');
        foreach ($data as $item) {
            $row = $xml->addChild('row');
            foreach ($item as $key => $value) {
                // Экранируем специальные XML символы
                $row->addChild($key, htmlspecialchars($value ?? ''));
            }
        }

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = $options['pretty_print'];
        $dom->loadXML($xml->asXML());
        $dom->save($outputPath);
    }

    private function convertToPdf($spreadsheet, $outputPath, $options)
    {
        // Сначала конвертируем Excel в HTML
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
        $html = $writer->generateHTMLAll();
        try {
            // Настройка mpdf
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'L', // landscape
                'tempDir' => storage_path('app/temp'),
            ]);

            // Добавляем стили для лучшего отображения таблицы
            $mpdf->WriteHTML('
            <style>
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
            </style>
            ');

            $mpdf->WriteHTML($html);
            $mpdf->Output($outputPath, 'F'); // 'F' — сохранить в файл
        } catch (\Mpdf\MpdfException $e) {
            Log::error('Mpdf Error: ' . $e->getMessage());
            throw $e; // Перебросить исключение, чтобы его поймал внешний catch
        } catch (\Exception $e) {
            Log::error('General Error in PDF conversion: ' . $e->getMessage());
            throw $e;
        }
    }

    private function sanitizeXmlTag($tag)
    {
        // Заменяем недопустимые символы в XML тегах
        $tag = preg_replace('/[^a-zA-Z0-9_]/', '_', $tag);
        $tag = preg_replace('/^[0-9]/', '_$0', $tag); // Не может начинаться с цифры
        return $tag ?: 'column';
    }
}