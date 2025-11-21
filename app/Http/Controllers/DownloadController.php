<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class DownloadController extends Controller
{
    public function download(string $id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Ищем файл только среди файлов текущего пользователя
            $file = $user->excelFiles()->findOrFail($id);

            // Проверяем существование файла
            if (!Storage::disk('local')->exists($file->path)) {
                abort(404, 'Файл не найден в хранилище.');
            }

            Log::info('File downloaded');

            // Возвращаем потоковый ответ для скачивания
            return Storage::download($file->path, $file->original_name);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'File not found.');
        } catch (\Exception $e) {
            abort(500, 'An error occurred while downloading the file: ' . $e->getMessage());
        }
    }

    public function checkStatus(string $id)
    {
        try {
            \Log::info('checkStatus called', ['id' => $id]);

            $file = \App\Models\UserFile::findOrFail($id);
            $file->refresh();
            
            \Log::info('checkStatus file data', [
                'file_id' => $file->id, 
                'output_path' => $file->output_path,
                'has_status_column' => \Schema::hasColumn('user_files', 'status'),
                'status_value' => $file->status ?? 'NULL'
            ]);

            // Определяем статус
            $status = 'pending';
            $downloadUrl = null;

            // Если есть output_path — файл сконвертирован
            if (!empty($file->output_path)) {
                $status = 'completed';
                // ИСПРАВЛЕНО: используем правильное имя маршрута
                $downloadUrl = route('download.file', ['id' => $file->id]);
                \Log::info('✅ File is ready for download', ['url' => $downloadUrl]);
            }
            // Если есть колонка status и она установлена в completed
            elseif (\Schema::hasColumn('user_files', 'status') && $file->status === 'completed') {
                $status = 'completed';
                if (!empty($file->output_path)) {
                    // ИСПРАВЛЕНО: используем правильное имя маршрута
                    $downloadUrl = route('download.file', ['id' => $file->id]);
                }
            }
            // Если есть колонка status и она processing
            elseif (\Schema::hasColumn('user_files', 'status') && $file->status === 'processing') {
                $status = 'processing';
            }
            // В остальных случаях — pending
            else {
                $status = 'pending';
            }

            \Log::info('Final status determination', [
                'status' => $status,
                'download_url' => $downloadUrl
            ]);

            return response()->json([
                'status' => $status,
                'file' => $downloadUrl,
                'file_id' => $file->id
            ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('File not found for checkStatus', ['id' => $id]);
            return response()->json(['status' => 'not_found'], 404);
        } catch (\Exception $e) {
            \Log::error('Error in checkStatus: ' . $e->getMessage(), ['id' => $id]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadFile(string $id)
    {
        try {
            $file = \App\Models\UserFile::findOrFail($id);
            
            // ВРЕМЕННО: проверяем по output_path вместо status
            if (empty($file->output_path)) {
                return response()->json(['error' => 'File not ready'], 404);
            }

            $convertedFilePath = $file->output_path;

            Log::info('Downloading converted file', ['file_id' => $id]);

            if (!empty($convertedFilePath) && Storage::disk('local')->exists($convertedFilePath)) {
                $outputExtension = pathinfo($file->output_path, PATHINFO_EXTENSION);
                $outputFileName = pathinfo($file->original_name, PATHINFO_FILENAME) . '.' . $outputExtension;

                $absolutePath = Storage::disk('local')->path($convertedFilePath);

                return response()->download($absolutePath, $outputFileName, [
                    'Content-Type' => 'application/octet-stream', 
                    'Content-Disposition' => 'attachment; filename="' . $outputFileName . '"'
                ])->deleteFileAfterSend(true);
            } else {
                return response()->json(['error' => 'Converted file not found'], 404);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('File not found: ' . $e->getMessage());
            return response()->json(['error' => 'File not found'], 404);
        } catch (\Exception $e) {
            Log::error('Downloading error after conversion: ' . $e->getMessage());
            return response()->json(['error' => 'Download error: ' . $e->getMessage()], 500);
        }
    }
}