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

    //Checking download status in 5 sec
    public function checkStatus(string $id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            Log::info('Status checked', ['file_id' => $id, 'user_id' => $user->id]);

            // Ищем файл только среди файлов текущего пользователя
            $file = $user->excelFiles()->findOrFail($id);

            Log::info('File status response', [
                'file_id' => $file->id,
                'status' => $file->status,
                'output_path' => $file->output_path,
                'has_output' => !empty($file->output_path)
            ]);

            return response()->json([
                'status' => $file->status,
                'file' => $file->status === 'completed' && !empty($file->output_path) ? route('download.file', ['id' => $file->id]) : null
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('File not found in checkStatus', ['file_id' => $id, 'user_id' => $user->id ?? 'unknown']);
            return response()->json(['error' => 'File not found or access denied'], 404);
        } catch (\Exception $e) {
            Log::error('Error in checkStatus', ['file_id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while checking file status'], 500);
        }
    }

    public function downloadFile(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $download = null;

        try {
            $download = $user->excelFiles()->findOrFail($id);
            if ($download->status !== 'completed') {
                abort(404, 'File not ready');
            }

            $convertedFilePath = $download->output_path;

            Log::info('Downloading start');

            if (!empty($convertedFilePath) && Storage::disk('local')->exists($convertedFilePath)) {

                $outputExtension = pathinfo($download->output_path, PATHINFO_EXTENSION);
                $outputFileName = pathinfo($download->original_name, PATHINFO_FILENAME) . '.' . $outputExtension;

                $absolutePath = Storage::disk('local')->path($convertedFilePath);

                return response()->download($absolutePath, $outputFileName, ['Content-Type' => 'application/octet-stream', 'Content-Disposition' => 'attachment; filename="' . $outputFileName . '"'])->deleteFileAfterSend(true);
            } else {
                return response()->json(['error' => 'Conversation failed'], 404);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('File not found: ' . $e->getMessage());
            abort(404, 'File not found');
        } catch (\Exception $e) {
            Log::error('Downloading error after conversion: ' . $e->getMessage());
            abort(500, 'Download error: ' . $e->getMessage());
        } finally {
            $download->update(["status" => NULL]);
            $download->update(["output_path" => NULL]);
        }
    }
}