<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = UserFile::orderBy('id', 'desc')->get();
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
            'xls_file' => ['nullable', 'file', 'max:50000', 'mimes:xls,xlsx']
        ]);

        $path = null;
        if ($request->hasFile('xls_file')) {
            // Безопасное оригинальное имя
            $originalName = $this->sanitizeFileName($request->xls_file->getClientOriginalName());

            // Путь к файлу
            $path = Storage::disk('local')->put('excel_files', $request->xls_file);
        }
        $file = UserFile::create(['original_name' => $originalName, 'path' => $path]);
        return back()->with('success', 'Your file was loaded');
    }

    public function storeForMerge(Request $request)
    {
        $request->validate([
            'xls_file' => ['nullable', 'file', 'max:50000', 'mimes:xls,xlsx']
        ]);

        $path = null;
        if ($request->hasFile('xls_file')) {
            // Безопасное оригинальное имя
            $originalName = $this->sanitizeFileName($request->xls_file->getClientOriginalName());

            // Путь к файлу
            $path = Storage::disk('local')->put('excel_files', $request->xls_file);
        }
        //$file = UserFile::create(['original_name' => $originalName, 'path' => $path]);
        return back()->with('success', 'Your file was loaded');
    }

    private function sanitizeFileName($fileName)
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
        $file = UserFile::find($id);
        $file = UserFile::find("$id");

        if (!$file) {
            throw new \Exception("File record not found.");
        }

        if (!Storage::disk('local')->exists($file->path)) {
            $absolutePath = Storage::disk('local')->path($file->path);

            $storageRoot = Storage::disk('local')->path('');
            $fullPath = $storageRoot . $file->path;

            abort(404, "File not found. "
                . "Storage path: '{$file->path}'. "
                . "Absolute path: '{$absolutePath}'. "
                . "Full path: '{$fullPath}'. "
                . "Storage root: '{$storageRoot}'");
        }

        $fileContent = Storage::disk('local')->get($file->path);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'laravel_excel_');
        file_put_contents($tempFilePath, $fileContent);

        try {
            $spreadsheet = IOFactory::load($tempFilePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();
        } finally {
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }

        return view('view', ['data' => $data, 'name' => $file->original_name]);
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
            $file = UserFile::findOrFail($id);
            if (Storage::disk('local')->exists($file->path)) {
                Storage::disk('local')->delete($file->path);
            }

            $file->delete();
            return back()->with('success', 'File successfully deleted');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Если файл не найден в базе данных
            return back()->with('error', 'File not found in database');
        } catch (\Exception $e) {
            // Любая другая ошибка
            return back()->with('error', 'An error occurred while deleting the file: ' . $e->getMessage());
        }
    }
}
