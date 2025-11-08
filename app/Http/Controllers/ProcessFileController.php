<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Jobs\ConvertionJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
}
