<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DownloadController extends Controller
{
    public function download(string $id)
    {
        $file = UserFile::findOrFail($id);

        // Проверяем существование файла
        if (!Storage::disk('local')->exists($file->path)) {
            abort(404, 'File not found.');
        }

        // Возвращаем потоковый ответ для скачивания
        return Storage::download($file->path, $file->original_name);
    }

    //Checking download status in 5 sec
    public function checkStatus(string $id)
    {
        $file = UserFile::findOrFail($id);
        return response()->json([
            'status' => $file->status,
            'file' => $file->status === 'completed' ? route('download.file', ['id' => $file->id]) : null
        ]);
    }

    public function downloadFile(string $id)
    {
        try {
            $download = UserFile::findOrFail($id);
            if ($download->status !== 'completed') {
                abort(404, 'File not ready');
            }

            $convertedFilePath = $download->output_path;

            if (!empty($convertedFilePath) && file_exists($convertedFilePath)) {

                $outputExtension = pathinfo($download->output_path, PATHINFO_EXTENSION);

                $outputFileName = pathinfo($download->original_name, PATHINFO_FILENAME) . '.' . $outputExtension;

                return response()->download($convertedFilePath, $outputFileName, ['Content-Type' => 'application/octet-stream', 'Content-Disposition' => 'attachment; filename="' . $outputFileName . '"'])->deleteFileAfterSend(true);
            } else {
                return response()->json(['error' => 'Conversation failed'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Downloading error after conversion: ' . $e->getMessage());
        } finally {
            $download->update(["status" => NULL]);
            $download->update(["output_path" => NULL]);
        }
    }
}