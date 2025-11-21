<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $files = $user->excelFiles()->orderBy('id', 'desc')->get();

        return view('dashboard', ['files' => $files]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'xls_file' => ['required', 'file', 'max:500000', 'mimes:xls,xlsx']
        ]);

        if (!Auth::check()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходима авторизация'
                ], 401);
            }
            return back()->with('error', 'Необходима авторизация');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            $originalName = $this->sanitizeFileName($request->xls_file->getClientOriginalName());

            // Создаем путь с ID пользователя: excel_files/user_{id}/
            $userDirectory = 'excel_files/user_' . $user->id;

            $path = $request->file('xls_file')->store($userDirectory, 'local');

            // Используем UserFile и правильные названия колонок
            $file = UserFile::create([
                'user_id' => $user->id,
                'original_name' => $originalName,
                'path' => $path,
            ]);

            // Для AJAX-запросов возвращаем JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'id' => $file->id,
                    'file_id' => $file->id,
                    'message' => 'File loaded successfully'
                ]);
            }

            return back()->with('success', 'File loaded successfully');

        } catch (\Exception $e) {
            // Для AJAX-запросов возвращаем JSON с ошибкой
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while loading file: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error while loading file: ' . $e->getMessage());
        }
    }

    private function sanitizeFileName($fileName): string
    {
        // Удаляем небезопасные символы
        $dangerousCharacters = array(" ", '"', "'", "&", "/", "\\", "?", "#", "%", "<", ">", "|", ":", ";", "*", "+", "=", "{", "}", "[", "]", ",", "~", "`", "!");
        $fileName = str_replace($dangerousCharacters, '_', $fileName);

        // Удаляем несколько подряд идущих подчеркиваний
        $fileName = preg_replace('/_+/', '_', $fileName);

        // Удаляем начальные и конечные подчеркивания/точки
        $fileName = trim($fileName, '_.');

        // Ограничиваем длину имени файла 
        if (strlen($fileName) > 100) {
            $fileName = substr($fileName, 0, 100);
        }

        return $fileName;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $file = $user->excelFiles()->findOrFail($id);

            // Удаляем основной файл
            if (Storage::disk('local')->exists($file->path)) {
                Storage::disk('local')->delete($file->path);
            }

            // Удаляем обработанный файл (если существует)
            if ($file->output_path && Storage::disk('local')->exists($file->output_path)) {
                Storage::disk('local')->delete($file->output_path);
            }

            // Удаляем запись из базы данных
            $file->delete();

            return back()->with('success', 'File deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'File not found in database');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the file: ' . $e->getMessage());
        }
    }

    public function uploadForMerge(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
            ]);

            /** @var \App\Models\User $user */
            $user = Auth::user();

            $file = $request->file('file');
            $filePath = $file->store('uploads', 'local');

            $userFile = $user->excelFiles()->create([
                'original_name' => $file->getClientOriginalName(),
                'path' => $filePath,
                'size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'id' => $userFile->id,
                'message' => 'File uploaded successfully for merge'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error uploading file for merge: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}