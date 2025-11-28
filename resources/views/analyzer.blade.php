<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Анализ данных - Table Master</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* === Базовые стили === */
    body {
        font-family: 'Figtree', -apple-system, BlinkMacSystemFont, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8fafc;
        min-height: 100vh;
    }

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

    .header nav a.active {
        color: #e2e8f0;
        font-weight: 600;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .main-content {
        padding: 3rem 0;
    }

    .page-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .page-subtitle {
        font-size: 1.125rem;
        color: #6b7280;
        max-width: 600px;
        margin: 0 auto;
    }

    .converter-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-bottom: 2rem;
    }

    .file-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        padding: 3rem 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .file-upload-area:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .upload-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: #e0e7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .upload-icon svg {
        width: 28px;
        height: 28px;
        color: #667eea;
    }

    .upload-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .upload-subtext {
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .upload-note {
        font-size: 0.875rem;
        color: #9ca3af;
    }

    .settings-section {
        margin-top: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-select,
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: white;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus,
    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.875rem 2rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .result-section {
        text-align: center;
        padding: 3rem 2rem;
    }

    .result-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .result-icon.success {
        background: #dcfce7;
    }

    .result-icon.processing {
        background: #fef3c7;
    }

    .result-icon svg {
        width: 40px;
        height: 40px;
    }

    .result-icon.success svg {
        color: #16a34a;
    }

    .result-icon.processing svg {
        color: #d97706;
    }

    .result-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .result-text {
        color: #6b7280;
        margin-bottom: 2rem;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        margin: 1.5rem 0;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 3px;
        transition: width 0.3s ease;
        width: 0%;
    }

    .hidden {
        display: none;
    }

    /* === Специфичные стили для анализатора === */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #667eea;
    }

    .metric-card.income {
        border-left-color: #10b981;
    }

    .metric-card.expenses {
        border-left-color: #ef4444;
    }

    .metric-card.profit {
        border-left-color: #8b5cf6;
    }

    .metric-card.trend {
        border-left-color: #f59e0b;
    }

    .metric-value {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .metric-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .metric-change {
        font-size: 0.75rem;
        font-weight: 500;
    }

    .metric-change.positive {
        color: #10b981;
    }

    .metric-change.negative {
        color: #ef4444;
    }

    .tabs {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 2rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .tab {
        padding: 1rem 1.5rem;
        background: none;
        border: none;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 2px solid transparent;
        white-space: nowrap;
    }

    .tab:hover {
        color: #374151;
    }

    .tab.active {
        color: #667eea;
        border-bottom-color: #667eea;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 1rem;
    }

    .chart-placeholder {
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        border-radius: 8px;
        color: #6b7280;
    }

    .data-table {
        width: 100%;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .data-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .data-table tr:hover {
        background: #f9fafb;
    }

    .positive-amount {
        color: #10b981;
        font-weight: 500;
    }

    .negative-amount {
        color: #ef4444;
        font-weight: 500;
    }

    .analysis-progress {
        text-align: center;
        padding: 2rem;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e5e7eb;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .insight-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-left: 3px solid #667eea;
    }

    .insight-card.warning {
        border-left-color: #f59e0b;
    }

    .insight-card.success {
        border-left-color: #10b981;
    }

    .insight-card.danger {
        border-left-color: #ef4444;
    }

    .insight-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .insight-text {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.4;
    }

    /* === Адаптивность === */

    /* Планшеты: iPad (портрет ~768px) */
    @media (max-width: 768px) {
        .header .container {
            flex-direction: column;
            height: auto;
            padding: 15px;
        }

        .header nav {
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            padding: 0.5rem 0;
        }

        .header nav a {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        .container {
            padding: 0 15px;
        }

        .main-content {
            padding: 1.5rem 0;
        }

        .page-title {
            font-size: 1.75rem;
        }

        .page-subtitle {
            font-size: 1rem;
            padding: 0 1rem;
        }

        .converter-card {
            padding: 1.5rem;
            border-radius: 16px;
        }

        .file-upload-area {
            padding: 2rem 1rem;
        }

        .upload-text {
            font-size: 1rem;
        }

        .upload-subtext {
            font-size: 0.875rem;
        }

        .btn-primary {
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
        }

        .metric-grid {
            grid-template-columns: 1fr;
        }

        .chart-grid {
            grid-template-columns: 1fr;
        }

        .insights-grid {
            grid-template-columns: 1fr;
        }

        .tabs {
            padding: 0 0.5rem;
        }

        .tab {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
        }

        .metric-value {
            font-size: 1.5rem;
        }

        .form-input,
        .form-select {
            font-size: 0.875rem;
            padding: 0.6rem 0.8rem;
        }
    }

    /* Маленькие планшеты и телефоны в альбомной ориентации */
    @media (min-width: 769px) and (max-width: 1024px) {
        .chart-grid {
            grid-template-columns: 1fr;
        }

        .metric-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .converter-card {
            padding: 2rem;
        }

        .file-upload-area {
            padding: 2.5rem 1.5rem;
        }
    }

    /* Очень узкие телефоны: Pixel 7 портрет (~393px) */
    @media (max-width: 480px) {
        .header .logo {
            font-size: 1.25rem;
        }

        .page-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            font-size: 0.875rem;
        }

        .converter-card {
            padding: 1rem;
            border-radius: 12px;
        }

        .file-upload-area {
            padding: 1.5rem 0.75rem;
        }

        .upload-icon {
            width: 50px;
            height: 50px;
            margin-bottom: 1rem;
        }

        .upload-icon svg {
            width: 24px;
            height: 24px;
        }

        .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .result-section {
            padding: 2rem 1rem;
        }

        .metric-value {
            font-size: 1.25rem;
        }

        .chart-placeholder {
            height: 200px;
            font-size: 0.875rem;
        }

        .tabs {
            padding: 0;
        }

        .tab {
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }

        .data-table {
            display: block;
            overflow-x: auto;
        }

        .data-table table {
            min-width: 500px;
        }
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
                <a href="{{ route('analyzer') }}" class="active">Анализ</a>
            </nav>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="main-content">
        <div class="container">
            <!-- Заголовок страницы -->
            <div class="page-header">
                <h1 class="page-title">Анализ данных</h1>
                <p class="page-subtitle">Анализируйте и визуализируйте данные из Excel и CSV файлов с помощью продвинутых инструментов</p>
            </div>

            <!-- Основная карточка анализа -->
            <div class="converter-card">
                <!-- Форма загрузки файла -->
                <div id="uploadSection">
                    <div class="form-group">
                        <label class="form-label">Файл для анализа</label>
                        <div id="fileDropZone" class="file-upload-area">
                            <input type="file" id="fileInput" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                            <div id="uploadContent">
                                <div class="upload-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="upload-text">Перетащите файл с данными</div>
                                <div class="upload-subtext">или кликните для выбора</div>
                                <div class="upload-note">Поддерживаемые форматы: XLSX, XLS, CSV • Максимальный размер: 10MB</div>
                            </div>
                            <div id="filePreview" class="hidden">
                                <!-- Превью файла будет добавляться сюда -->
                            </div>
                        </div>
                    </div>

                    <!-- Настройки анализа -->
                    <div id="analysisSettings" class="settings-section hidden">
                        <h3 class="form-label">Настройки анализа</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Тип анализа</label>
                            <select id="analysisType" class="form-select">
                                <option value="financial">Финансовый анализ</option>
                                <option value="sales">Анализ продаж</option>
                                <option value="inventory">Анализ запасов</option>
                                <option value="custom">Произвольный анализ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Столбцы для анализа</label>
                            <div id="columnSelection" class="space-y-2">
                                <!-- Динамически заполнится после загрузки файла -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Период анализа</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <input type="date" id="startDate" class="form-input" placeholder="Начальная дата">
                                <input type="date" id="endDate" class="form-input" placeholder="Конечная дата">
                            </div>
                        </div>

                        <!-- Кнопка анализа -->
                        <div class="form-group" style="text-align: right; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                            <button id="analyzeBtn" class="btn-primary">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Начать анализ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Прогресс анализа -->
                <div id="analysisProgress" class="result-section hidden">
                    <div class="result-icon processing">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="result-title">Анализ в процессе...</h3>
                    <p class="result-text" id="analysisProgressText">Анализируем данные и строим отчеты</p>
                    <div class="progress-bar">
                        <div id="analysisProgressFill" class="progress-fill"></div>
                    </div>
                </div>

                <!-- Результаты анализа -->
                <div id="analysisResults" class="hidden">
                    <div class="result-icon success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="result-title">Анализ завершен!</h3>
                    <p class="result-text">Данные успешно проанализированы</p>

                    <button id="newAnalysis" class="btn-primary" style="margin-top: 1rem;">
                        Новый анализ
                    </button>
                </div>
            </div>

            <!-- Детальные результаты (показываются после анализа) -->
            <div id="detailedResults" class="hidden">
                <!-- Ключевые метрики -->
                <div class="metric-grid">
                    <div class="metric-card income">
                        <div class="metric-label">Общий доход</div>
                        <div class="metric-value" id="totalIncome">₽ 0</div>
                        <div class="metric-change positive" id="incomeChange">+0%</div>
                    </div>
                    
                    <div class="metric-card expenses">
                        <div class="metric-label">Общие расходы</div>
                        <div class="metric-value" id="totalExpenses">₽ 0</div>
                        <div class="metric-change negative" id="expensesChange">+0%</div>
                    </div>
                    
                    <div class="metric-card profit">
                        <div class="metric-label">Чистая прибыль</div>
                        <div class="metric-value" id="netProfit">₽ 0</div>
                        <div class="metric-change positive" id="profitChange">+0%</div>
                    </div>
                    
                    <div class="metric-card trend">
                        <div class="metric-label">Темп роста</div>
                        <div class="metric-value" id="growthRate">0%</div>
                        <div class="metric-change positive" id="growthTrend">Стабильный</div>
                    </div>
                </div>

                <!-- Инсайты -->
                <div class="insights-grid">
                    <div class="insight-card success">
                        <div class="insight-title">📈 Положительная динамика</div>
                        <div class="insight-text" id="positiveInsight">Загрузите данные для получения инсайтов</div>
                    </div>
                    
                    <div class="insight-card warning">
                        <div class="insight-title">⚠️ Внимание</div>
                        <div class="insight-text" id="warningInsight">Загрузите данные для получения предупреждений</div>
                    </div>
                    
                    <div class="insight-card">
                        <div class="insight-title">💡 Рекомендация</div>
                        <div class="insight-text" id="recommendationInsight">Загрузите данные для получения рекомендаций</div>
                    </div>
                </div>

                <!-- Вкладки -->
                <div class="tabs">
                    <button class="tab active" data-tab="overview">Обзор</button>
                    <button class="tab" data-tab="charts">Графики</button>
                    <button class="tab" data-tab="details">Детали</button>
                    <button class="tab" data-tab="export">Экспорт</button>
                </div>

                <!-- Контент вкладок -->
                <div class="tab-content active" id="tab-overview">
                    <div class="chart-grid">
                        <div class="chart-container">
                            <div class="chart-title">Динамика доходов и расходов</div>
                            <div id="incomeExpensesChart" class="chart-placeholder">
                                График будет построен после анализа
                            </div>
                        </div>
                        
                        <div class="chart-container">
                            <div class="chart-title">Структура расходов</div>
                            <div id="expensesPieChart" class="chart-placeholder">
                                Круговая диаграмма будет построена после анализа
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-charts">
                    <div class="chart-grid">
                        <div class="chart-container">
                            <div class="chart-title">Тренды продаж</div>
                            <div id="salesTrendChart" class="chart-placeholder">
                                График трендов будет построен после анализа
                            </div>
                        </div>
                        
                        <div class="chart-container">
                            <div class="chart-title">Сравнение категорий</div>
                            <div id="categoryComparisonChart" class="chart-placeholder">
                                График сравнения будет построен после анализа
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-details">
                    <div class="chart-container">
                        <div class="chart-title">Детализация данных</div>
                        <div class="data-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Категория</th>
                                        <th>Описание</th>
                                        <th>Доход</th>
                                        <th>Расход</th>
                                        <th>Прибыль</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTableBody">
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem; color: #6b7280;">
                                            Загрузите данные для просмотра деталей
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-export">
                    <div class="chart-container">
                        <div class="chart-title">Экспорт результатов</div>
                        <div style="text-align: center; padding: 2rem;">
                            <p style="color: #6b7280; margin-bottom: 2rem;">Экспортируйте результаты анализа в различных форматах</p>
                            <div style="display: flex; gap: 1rem; justify-content: center;">
                                <button id="exportPdf" class="btn-primary">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    PDF отчет
                                </button>
                                <button id="exportExcel" class="btn-primary">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Excel отчет
                                </button>
                                <button id="exportCharts" class="btn-primary">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Графики
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Контейнер для уведомлений -->
    <div id="notification-container"></div>
</body>
</html>

<script>
class DataAnalyzer {
    constructor() {
        this.currentFile = null;
        this.analysisData = null;
        this.analysisResults = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupTabs();
    }

    setupEventListeners() {
        const fileInput = document.getElementById('fileInput');
        const fileDropZone = document.getElementById('fileDropZone');
        const analyzeBtn = document.getElementById('analyzeBtn');
        const newAnalysisBtn = document.getElementById('newAnalysis');

        // Загрузка файла
        fileDropZone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => this.handleFileSelect(e.target.files[0]));

        // Drag & Drop
        fileDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileDropZone.style.borderColor = '#667eea';
            fileDropZone.style.background = '#f0f4ff';
        });

        fileDropZone.addEventListener('dragleave', () => {
            fileDropZone.style.borderColor = '#d1d5db';
            fileDropZone.style.background = '#f9fafb';
        });

        fileDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.handleFileSelect(files[0]);
            }
        });

        // Анализ данных
        analyzeBtn.addEventListener('click', () => this.startAnalysis());
        newAnalysisBtn.addEventListener('click', () => this.resetAnalysis());

        // Экспорт
        document.getElementById('exportPdf').addEventListener('click', () => this.exportPdf());
        document.getElementById('exportExcel').addEventListener('click', () => this.exportExcel());
        document.getElementById('exportCharts').addEventListener('click', () => this.exportCharts());
    }

    setupTabs() {
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const tabName = tab.dataset.tab;
                
                // Обновляем активную вкладку
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Показываем соответствующий контент
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(`tab-${tabName}`).classList.add('active');
            });
        });
    }

    handleFileSelect(file) {
        if (!file) return;

        const allowedTypes = ['.xlsx', '.xls', '.csv'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(fileExtension)) {
            this.showNotification('Ошибка: поддерживаются только XLSX, XLS, CSV файлы', 'error');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            this.showNotification('Ошибка: файл слишком большой (макс. 10MB)', 'error');
            return;
        }

        this.currentFile = file;
        this.showFilePreview(file);
        document.getElementById('analysisSettings').classList.remove('hidden');
    }

    showFilePreview(file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const filePreview = document.getElementById('filePreview');
        
        filePreview.innerHTML = `
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; background: #667eea; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg fill="white" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 600; color: #374151;">${file.name}</div>
                    <div style="color: #6b7280; font-size: 0.875rem;">${fileSize} MB</div>
                </div>
                <button type="button" onclick="dataAnalyzer.removeFile()" style="margin-left: auto; background: none; border: none; color: #6b7280; cursor: pointer;">
                    <svg fill="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                    </svg>
                </button>
            </div>
        `;
        
        document.getElementById('uploadContent').classList.add('hidden');
        filePreview.classList.remove('hidden');
    }

    removeFile() {
        this.currentFile = null;
        document.getElementById('fileInput').value = '';
        document.getElementById('uploadContent').classList.remove('hidden');
        document.getElementById('filePreview').classList.add('hidden');
        document.getElementById('analysisSettings').classList.add('hidden');
        document.getElementById('analysisResults').classList.add('hidden');
        document.getElementById('detailedResults').classList.add('hidden');
    }

    async startAnalysis() {
        if (!this.currentFile) {
            this.showNotification('Выберите файл для анализа', 'error');
            return;
        }

        const analyzeBtn = document.getElementById('analyzeBtn');
        const originalText = analyzeBtn.innerHTML;

        try {
            analyzeBtn.innerHTML = 'Подготовка...';
            analyzeBtn.disabled = true;

            // Показываем прогресс
            document.getElementById('uploadSection').classList.add('hidden');
            document.getElementById('analysisProgress').classList.remove('hidden');
            document.getElementById('analysisProgressFill').style.width = '30%';
            document.getElementById('analysisProgressText').textContent = 'Чтение файла...';

            const formData = new FormData();
            formData.append('file', this.currentFile);
            formData.append('analysis_type', document.getElementById('analysisType').value);
            
            // Безопасно получаем значения дат
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            
            if (startDate && startDate.value) {
                formData.append('start_date', startDate.value);
            }
            if (endDate && endDate.value) {
                formData.append('end_date', endDate.value);
            }
            
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Обновляем прогресс
            document.getElementById('analysisProgressFill').style.width = '60%';
            document.getElementById('analysisProgressText').textContent = 'Анализ данных...';

            const response = await fetch('/analyze-file', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text.substring(0, 500));
                throw new Error('Сервер вернул некорректный ответ');
            }

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || `HTTP error! status: ${response.status}`);
            }

            if (result.success) {
                document.getElementById('analysisProgressFill').style.width = '100%';
                document.getElementById('analysisProgressText').textContent = 'Завершение анализа...';

                setTimeout(() => {
                    this.analysisResults = result.data;
                    this.displayResults();
                    document.getElementById('analysisProgress').classList.add('hidden');
                    document.getElementById('analysisResults').classList.remove('hidden');
                    document.getElementById('detailedResults').classList.remove('hidden');
                    
                    this.showNotification('Анализ завершен успешно!', 'success');
                }, 1000);

            } else {
                throw new Error(result.message || 'Ошибка анализа данных');
            }

        } catch (error) {
            console.error('Analysis error:', error);
            this.showNotification(error.message, 'error');
            document.getElementById('analysisProgress').classList.add('hidden');
            document.getElementById('uploadSection').classList.remove('hidden');
        } finally {
            analyzeBtn.innerHTML = originalText;
            analyzeBtn.disabled = false;
        }
    }

    displayResults() {
        if (!this.analysisResults) return;

        // Обновляем метрики
        this.updateMetrics();
        
        // Строим графики
        this.renderCharts();
        
        // Заполняем таблицу
        this.populateDataTable();
        
        // Показываем инсайты
        this.showInsights();
    }

    updateMetrics() {
        const metrics = this.analysisResults.metrics || {};
        
        document.getElementById('totalIncome').textContent = `₽ ${this.formatNumber(metrics.total_income || 0)}`;
        document.getElementById('totalExpenses').textContent = `₽ ${this.formatNumber(metrics.total_expenses || 0)}`;
        document.getElementById('netProfit').textContent = `₽ ${this.formatNumber(metrics.net_profit || 0)}`;
        document.getElementById('growthRate').textContent = `${metrics.growth_rate || 0}%`;
        
        // Обновляем изменения
        const incomeChange = document.getElementById('incomeChange');
        const expensesChange = document.getElementById('expensesChange');
        const profitChange = document.getElementById('profitChange');
        const growthTrend = document.getElementById('growthTrend');
        
        incomeChange.textContent = `${metrics.income_change >= 0 ? '+' : ''}${metrics.income_change || 0}%`;
        incomeChange.className = `metric-change ${metrics.income_change >= 0 ? 'positive' : 'negative'}`;
        
        expensesChange.textContent = `${metrics.expenses_change >= 0 ? '+' : ''}${metrics.expenses_change || 0}%`;
        expensesChange.className = `metric-change ${metrics.expenses_change <= 0 ? 'positive' : 'negative'}`;
        
        profitChange.textContent = `${metrics.profit_change >= 0 ? '+' : ''}${metrics.profit_change || 0}%`;
        profitChange.className = `metric-change ${metrics.profit_change >= 0 ? 'positive' : 'negative'}`;
        
        growthTrend.textContent = metrics.growth_trend || 'Стабильный';
        growthTrend.className = `metric-change ${metrics.growth_trend === 'Рост' ? 'positive' : metrics.growth_trend === 'Спад' ? 'negative' : ''}`;
    }

    renderCharts() {
        this.renderIncomeExpensesChart();
        this.renderExpensesPieChart();
        this.renderSalesTrendChart();
        this.renderCategoryComparisonChart();
    }

    renderIncomeExpensesChart() {
        const container = document.getElementById('incomeExpensesChart');
        const data = this.analysisResults.monthly_data || [];
        
        if (data.length === 0) {
            container.innerHTML = '<div class="chart-placeholder">Недостаточно данных для построения графика</div>';
            return;
        }

        // Простая реализация столбчатой диаграммы
        let html = '<div style="display: flex; align-items: end; justify-content: space-around; height: 250px; padding: 20px 0;">';
        
        const maxValue = Math.max(...data.map(d => Math.max(d.income || 0, d.expenses || 0)));
        
        data.forEach(item => {
            const incomeHeight = ((item.income || 0) / maxValue) * 200;
            const expensesHeight = ((item.expenses || 0) / maxValue) * 200;
            
            html += `
                <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                    <div style="display: flex; align-items: end; gap: 2px;">
                        <div style="width: 20px; height: ${incomeHeight}px; background: linear-gradient(to top, #10b981, #34d399); border-radius: 3px 3px 0 0;" 
                             title="Доход: ₽${this.formatNumber(item.income || 0)}"></div>
                        <div style="width: 20px; height: ${expensesHeight}px; background: linear-gradient(to top, #ef4444, #f87171); border-radius: 3px 3px 0 0;"
                             title="Расход: ₽${this.formatNumber(item.expenses || 0)}"></div>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 5px;">${item.month}</div>
                </div>
            `;
        });
        
        html += '</div>';
        
        // Добавляем легенду
        html += `
            <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px;">
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 12px; height: 12px; background: #10b981; border-radius: 2px;"></div>
                    <span style="font-size: 12px;">Доходы</span>
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 12px; height: 12px; background: #ef4444; border-radius: 2px;"></div>
                    <span style="font-size: 12px;">Расходы</span>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
    }

    renderExpensesPieChart() {
        const container = document.getElementById('expensesPieChart');
        const data = this.analysisResults.expenses_by_category || [];
        
        if (data.length === 0) {
            container.innerHTML = '<div class="chart-placeholder">Недостаточно данных для построения диаграммы</div>';
            return;
        }

        // Цвета для категорий
        const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6366f1'];
        
        let html = '<div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: center; justify-content: center;">';
        
        // Круговая диаграмма
        html += '<div style="position: relative; width: 250px; height: 250px;">';
        
        const total = data.reduce((sum, item) => sum + (item.amount || 0), 0);
        let currentAngle = 0;
        
        data.forEach((item, index) => {
            const percentage = ((item.amount || 0) / total) * 100;
            const angle = (percentage / 100) * 360;
            
            if (percentage > 0) {
                html += `
                    <div style="
                        position: absolute;
                        width: 250px;
                        height: 250px;
                        border-radius: 50%;
                        background: conic-gradient(
                            ${colors[index % colors.length]} ${currentAngle}deg,
                            ${colors[index % colors.length]} ${currentAngle + angle}deg,
                            transparent ${currentAngle + angle}deg,
                            transparent 360deg
                        );
                    "></div>
                `;
                currentAngle += angle;
            }
        });
        
        // Центральный круг
        html += `
            <div style="
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 80px;
                height: 80px;
                background: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                color: #374151;
            ">
                ${total > 0 ? '₽' + this.formatNumber(total) : ''}
            </div>
        `;
        
        html += '</div>';
        
        // Легенда
        html += '<div style="min-width: 200px;">';
        data.forEach((item, index) => {
            const percentage = ((item.amount || 0) / total) * 100;
            if (percentage > 0) {
                const displayName = item.subcategory ? 
                    `${item.category} - ${item.subcategory}` : item.category;
                    
                html += `
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 14px;">
                        <div style="width: 16px; height: 16px; background: ${colors[index % colors.length]}; border-radius: 3px;"></div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${displayName}</div>
                            <div style="color: #6b7280; font-size: 12px;">${percentage.toFixed(1)}% (₽${this.formatNumber(item.amount)})</div>
                        </div>
                    </div>
                `;
            }
        });
        html += '</div>';
        
        html += '</div>';
        
        container.innerHTML = html;
    }

    renderSalesTrendChart() {
        const container = document.getElementById('salesTrendChart');
        const monthlyData = this.analysisResults.monthly_data || [];
        
        if (monthlyData.length === 0) {
            container.innerHTML = '<div class="chart-placeholder">Недостаточно данных для построения графика трендов</div>';
            return;
        }

        let html = '<div style="height: 300px; padding: 20px;">';
        html += '<div style="display: flex; align-items: end; justify-content: space-between; height: 200px; margin-bottom: 20px;">';
        
        const maxIncome = Math.max(...monthlyData.map(d => d.income || 0));
        
        monthlyData.forEach(item => {
            const height = ((item.income || 0) / maxIncome) * 180;
            const trend = this.calculateTrend(monthlyData, item.month);
            
            html += `
                <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                    <div style="width: 30px; height: ${height}px; background: linear-gradient(to top, #10b981, #34d399); border-radius: 5px 5px 0 0; position: relative;">
                        <div style="position: absolute; top: -25px; left: 50%; transform: translateX(-50%); font-size: 12px; color: #059669; font-weight: bold;">
                            ${trend > 0 ? '↗' : trend < 0 ? '↘' : '→'}
                        </div>
                    </div>
                    <div style="font-size: 12px; color: #6b7280; text-align: center;">${item.month}</div>
                    <div style="font-size: 11px; color: #374151; font-weight: 500;">₽${this.formatNumber(item.income)}</div>
                </div>
            `;
        });
        
        html += '</div>';
        
        // Линия тренда
        html += '<div style="border-top: 2px dashed #3b82f6; margin: 20px 0; position: relative;">';
        html += '<div style="position: absolute; top: -12px; right: 0; background: #3b82f6; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">Линия тренда</div>';
        html += '</div>';
        
        html += '</div>';
        
        container.innerHTML = html;
    }

    calculateTrend(monthlyData, currentMonth) {
        const currentIndex = monthlyData.findIndex(d => d.month === currentMonth);
        if (currentIndex <= 0) return 0;
        
        const current = monthlyData[currentIndex].income || 0;
        const previous = monthlyData[currentIndex - 1].income || 0;
        
        if (previous === 0) return 0;
        
        return ((current - previous) / previous) * 100;
    }

    renderCategoryComparisonChart() {
        const container = document.getElementById('categoryComparisonChart');
        const data = this.analysisResults.expenses_by_category || [];
        
        if (data.length === 0) {
            container.innerHTML = '<div class="chart-placeholder">Недостаточно данных для сравнения категорий</div>';
            return;
        }

        let html = '<div style="height: 300px; padding: 20px;">';
        html += '<div style="display: flex; align-items: end; justify-content: space-around; height: 200px; margin-bottom: 20px;">';
        
        const maxAmount = Math.max(...data.map(d => d.amount || 0));
        
        data.forEach((item, index) => {
            const height = ((item.amount || 0) / maxAmount) * 180;
            const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
            
            html += `
                <div style="display: flex; flex-direction: column; align-items: center; gap: 5px;">
                    <div style="width: 40px; height: ${height}px; background: ${colors[index % colors.length]}; border-radius: 5px 5px 0 0;"
                         title="${item.category}: ₽${this.formatNumber(item.amount)}">
                    </div>
                    <div style="font-size: 12px; color: #6b7280; text-align: center; max-width: 80px; overflow: hidden; text-overflow: ellipsis;">
                        ${item.category}
                    </div>
                    <div style="font-size: 11px; color: #374151; font-weight: 500;">₽${this.formatNumber(item.amount)}</div>
                </div>
            `;
        });
        
        html += '</div>';
        html += '</div>';
        
        container.innerHTML = html;
    }

    populateDataTable() {
        const tbody = document.getElementById('dataTableBody');
        const data = this.analysisResults.detailed_data || [];
        
        if (data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: #6b7280;">
                        Нет данных для отображения
                    </td>
                </tr>
            `;
            return;
        }
        
        let html = '';
        data.forEach(item => {
            html += `
                <tr>
                    <td>${item.date || '-'}</td>
                    <td>${item.category || '-'}</td>
                    <td>${item.description || '-'}</td>
                    <td class="${(item.income || 0) > 0 ? 'positive-amount' : ''}">
                        ${(item.income || 0) > 0 ? `₽${this.formatNumber(item.income)}` : '-'}
                    </td>
                    <td class="${(item.expense || 0) > 0 ? 'negative-amount' : ''}">
                        ${(item.expense || 0) > 0 ? `₽${this.formatNumber(item.expense)}` : '-'}
                    </td>
                    <td class="${(item.profit || 0) >= 0 ? 'positive-amount' : 'negative-amount'}">
                        ₽${this.formatNumber(item.profit || 0)}
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }

    showInsights() {
        const insights = this.analysisResults.insights || {};
        
        document.getElementById('positiveInsight').textContent = insights.positive || 'Анализ данных не выявил значительных положительных тенденций';
        document.getElementById('warningInsight').textContent = insights.warning || 'Критических проблем не обнаружено';
        document.getElementById('recommendationInsight').textContent = insights.recommendation || 'Рекомендуется продолжить мониторинг ключевых показателей';
    }

    resetAnalysis() {
        this.removeFile();
        document.getElementById('analysisResults').classList.add('hidden');
        document.getElementById('detailedResults').classList.add('hidden');
        document.getElementById('uploadSection').classList.remove('hidden');
    }

    formatNumber(num) {
        return new Intl.NumberFormat('ru-RU').format(num);
    }

    showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            animation: slideIn 0.3s ease;
            ${type === 'error' ? 'background: #ef4444;' : 'background: #10b981;'}
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        `;
        notification.textContent = message;
        container.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }

    async exportPdf() {
        this.showNotification('Функция экспорта в PDF в разработке', 'info');
    }

    async exportExcel() {
        this.showNotification('Функция экспорта в Excel в разработке', 'info');
    }

    async exportCharts() {
        this.showNotification('Функция экспорта графиков в разработке', 'info');
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    window.dataAnalyzer = new DataAnalyzer();
});
</script>