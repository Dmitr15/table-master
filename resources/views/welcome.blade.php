<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Table Master') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; color: #1a202c;">
    <!-- Простая навигация -->
    <nav style="background-color: white; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); padding: 1rem;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center;">
            <span style="font-size: 1.5rem; font-weight: bold;">Table Master</span>
        </div>
    </nav>

    <!-- Основной контент -->
    <main style="padding: 2rem 1rem;">
        <div style="max-width: 1200px; margin: 0 auto; background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <div style="padding: 2rem;">
                <h1 style="font-size: 2rem; font-weight: bold; margin-bottom: 1rem;">Table Master</h1>
                <p style="font-size: 1.25rem; margin-bottom: 2rem; color: #4b5563;">Мощный инструмент для работы с табличными данными</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <!-- Конвертер -->
                    <a href="{{ route('converter') }}" style="display: block; text-decoration: none; color: inherit; background-color: #e0f2fe; border-radius: 0.5rem; padding: 1.5rem; text-align: center; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div style="width: 60px; height: 60px; background-color: #0ea5e9; border-radius: 9999px; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 30px; height: 30px; fill: white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Конвертер</h2>
                        <p style="font-size: 1rem; color: #6b7280;">Конвертация между форматами</p>
                    </a>
                    
                    <!-- Слияние -->
                    <a href="{{ route('merger') }}" style="display: block; text-decoration: none; color: inherit; background-color: #dcfce7; border-radius: 0.5rem; padding: 1.5rem; text-align: center; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div style="width: 60px; height: 60px; background-color: #22c55e; border-radius: 9999px; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 30px; height: 30px; fill: white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Слияние</h2>
                        <p style="font-size: 1rem; color: #6b7280;">Объединение таблиц</p>
                    </a>
                    
                    <!-- Разделение -->
                    <a href="{{ route('splitter') }}" style="display: block; text-decoration: none; color: inherit; background-color: #fef9c3; border-radius: 0.5rem; padding: 1.5rem; text-align: center; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div style="width: 60px; height: 60px; background-color: #eab308; border-radius: 9999px; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 30px; height: 30px; fill: white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Разделение</h2>
                        <p style="font-size: 1rem; color: #6b7280;">Разделение таблиц</p>
                    </a>
                    
                    <!-- Анализ -->
                    <a href="{{ route('analyzer') }}" style="display: block; text-decoration: none; color: inherit; background-color: #f3e8ff; border-radius: 0.5rem; padding: 1.5rem; text-align: center; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <div style="width: 60px; height: 60px; background-color: #8b5cf6; border-radius: 9999px; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 30px; height: 30px; fill: white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h2 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">Анализ</h2>
                        <p style="font-size: 1rem; color: #6b7280;">Визуализация данных</p>
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>