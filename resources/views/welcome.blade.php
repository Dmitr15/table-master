<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Table Master - Главная</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Базовые стили */
        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            min-height: 100vh;
        }
        
        /* Красивый хедер */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .header .logo {
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
            transition: color 0.3s ease;
        }

        .header .logo:hover {
            color: #e2e8f0;
        }

        .header nav {
            display: flex;
            gap: 2rem;
        }

        .header nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 0.5rem 0;
        }

        .header nav a:hover {
            color: #e2e8f0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Карточки функций */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin: 3rem 0;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem 1.5rem;
            text-align: center;
            text-decoration: none;
            color: #374151;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            color: #374151;
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .feature-icon svg {
            width: 32px;
            height: 32px;
            color: white;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .feature-description {
            color: #6b7280;
            line-height: 1.5;
        }

        /* Герой секция */
        .hero {
            text-align: center;
            padding: 4rem 0 2rem;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        /* Информационная секция */
        .info-section {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            margin: 4rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .info-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 3rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .info-item {
            text-align: center;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .info-icon svg {
            width: 24px;
            height: 24px;
        }

        .info-item-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .info-item-description {
            color: #6b7280;
            line-height: 1.5;
        }

        /* Цвета для иконок информации */
        .icon-green {
            background-color: #dcfce7;
        }

        .icon-green svg {
            color: #16a34a;
        }

        .icon-blue {
            background-color: #dbeafe;
        }

        .icon-blue svg {
            color: #2563eb;
        }

        .icon-purple {
            background-color: #f3e8ff;
        }

        .icon-purple svg {
            color: #9333ea;
        }
    </style>
</head>
<body>
    <!-- Навигация -->
    <header class="header">
        <div class="container">
            <a href="{{ route('home') }}" class="logo">📊 Table Master</a>
            <nav>
                <a href="{{ route('home') }}">Главная</a>
                <a href="{{ route('converter') }}">Конвертер</a>
                <a href="{{ route('merger') }}">Слияние</a>
                <a href="{{ route('splitter') }}">Разделение</a>
                <a href="{{ route('analyzer') }}">Анализ</a>
            </nav>
        </div>
    </header>

    <!-- Основной контент -->
    <main>
        <div class="container">
            <!-- Герой секция -->
            <section class="hero">
                <h1 class="hero-title">Table Master</h1>
                <p class="hero-subtitle">
                    Мощный инструмент для работы с табличными данными. Конвертируйте, объединяйте, разделяйте и анализируйте ваши данные с легкостью.
                </p>
            </section>

            <!-- Карточки функций -->
            <section class="features-grid">
                <a href="{{ route('converter') }}" class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Конвертер</h3>
                    <p class="feature-description">Конвертация между различными форматами файлов</p>
                </a>
                
                <a href="{{ route('merger') }}" class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Слияние</h3>
                    <p class="feature-description">Объединение нескольких таблиц в один файл</p>
                </a>
                
                <a href="{{ route('splitter') }}" class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Разделение</h3>
                    <p class="feature-description">Разделение больших таблиц на несколько частей</p>
                </a>
                
                <a href="{{ route('analyzer') }}" class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title">Анализ</h3>
                    <p class="feature-description">Визуализация и анализ данных с помощью графиков</p>
                </a>
            </section>

            <!-- Информационная секция -->
            <section class="info-section">
                <h2 class="info-title">Почему Table Master?</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon icon-green">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="info-item-title">Простота использования</h3>
                        <p class="info-item-description">Интуитивно понятный интерфейс для быстрой работы</p>
                    </div>
                    <div class="info-item">
                        <div class="info-icon icon-blue">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="info-item-title">Высокая скорость</h3>
                        <p class="info-item-description">Быстрая обработка даже больших файлов</p>
                    </div>
                    <div class="info-item">
                        <div class="info-icon icon-purple">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="info-item-title">Безопасность</h3>
                        <p class="info-item-description">Ваши данные обрабатываются локально</p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>