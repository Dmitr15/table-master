<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            //dd($path);
        }
        //Storage::disk('local')->put('excel_files', $request->xls_file);

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
        //
    }
}
