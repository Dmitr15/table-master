<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
}
