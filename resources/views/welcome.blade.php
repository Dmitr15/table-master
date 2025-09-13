@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">Table Master</h1>
                <p class="mb-4">Мощный инструмент для работы с табличными данными</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('converter') }}" class="block p-4 bg-blue-100 rounded-lg hover:bg-blue-200 transition">
                        <h2 class="font-semibold">Конвертер</h2>
                        <p>Конвертация между форматами</p>
                    </a>
                    
                    <a href="{{ route('merger') }}" class="block p-4 bg-green-100 rounded-lg hover:bg-green-200 transition">
                        <h2 class="font-semibold">Слияние</h2>
                        <p>Объединение таблиц</p>
                    </a>
                    
                    <a href="{{ route('splitter') }}" class="block p-4 bg-yellow-100 rounded-lg hover:bg-yellow-200 transition">
                        <h2 class="font-semibold">Разделение</h2>
                        <p>Разделение таблиц</p>
                    </a>
                    
                    <a href="{{ route('analyzer') }}" class="block p-4 bg-purple-100 rounded-lg hover:bg-purple-200 transition">
                        <h2 class="font-semibold">Анализ</h2>
                        <p>Визуализация данных</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection