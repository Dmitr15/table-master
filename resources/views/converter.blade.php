@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Конвертер файлов</h1>
                
                <form action="{{ route('converter.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">Выберите файл</label>
                        <input type="file" name="file" id="file" accept=".xlsx,.xls" 
                               class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    </div>
                    
                    <div>
                        <label for="format" class="block text-sm font-medium text-gray-700">Формат назначения</label>
                        <select name="format" id="format" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                            <option value="json">JSON</option>
                            <option value="csv">CSV</option>
                            <option value="xml">XML</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Конвертировать
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection