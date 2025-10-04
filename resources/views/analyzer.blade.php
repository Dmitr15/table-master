<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Анализ данных - Table Master</title>
    
    <style>
        /* Базовые стили */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            color: #374151;
        }
        
        /* Утилиты */
        .min-h-screen { min-height: 100vh; }
        .bg-white { background-color: white; }
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-blue-50 { background-color: #eff6ff; }
        .bg-green-50 { background-color: #f0fdf4; }
        .bg-purple-50 { background-color: #faf5ff; }
        .bg-red-50 { background-color: #fef2f2; }
        .bg-yellow-50 { background-color: #fefce8; }
        .bg-blue-100 { background-color: #dbeafe; }
        .bg-green-100 { background-color: #dcfce7; }
        .bg-purple-100 { background-color: #e9d5ff; }
        
        .text-white { color: white; }
        .text-gray-900 { color: #111827; }
        .text-gray-700 { color: #374151; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-blue-600 { color: #2563eb; }
        .text-green-600 { color: #059669; }
        .text-red-600 { color: #dc2626; }
        .text-purple-600 { color: #7c3aed; }
        .text-yellow-600 { color: #d97706; }
        
        .border { border-width: 1px; }
        .border-b { border-bottom-width: 1px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-300 { border-color: #d1d5db; }
        
        .rounded { border-radius: 0.375rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-full { border-radius: 9999px; }
        
        .shadow { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .p-8 { padding: 2rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
        
        .m-0 { margin: 0; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        
        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-3xl { font-size: 1.875rem; }
        
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        
        .flex { display: flex; }
        .hidden { display: none; }
        .grid { display: grid; }
        .block { display: block; }
        .inline-block { display: inline-block; }
        
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .justify-end { justify-content: flex-end; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        
        .space-x-3 > * + * { margin-left: 0.75rem; }
        .space-x-4 > * + * { margin-left: 1rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        
        .cursor-pointer { cursor: pointer; }
        .overflow-hidden { overflow: hidden; }
        .overflow-auto { overflow: auto; }
        .overflow-x-auto { overflow-x: auto; }
        
        .max-w-7xl { max-width: 80rem; }
        .max-w-4xl { max-width: 56rem; }
        .w-full { width: 100%; }
        .w-8 { width: 2rem; }
        .w-10 { width: 2.5rem; }
        .w-12 { width: 3rem; }
        .w-16 { width: 4rem; }
        .h-8 { height: 2rem; }
        .h-10 { height: 2.5rem; }
        .h-12 { height: 3rem; }
        .h-16 { height: 4rem; }
        .h-64 { height: 16rem; }
        .h-80 { height: 20rem; }
        .h-96 { height: 24rem; }
        
        .max-h-96 { max-height: 24rem; }
        
        /* Кастомные классы */
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-secondary:hover {
            background-color: #d1d5db;
        }
        
        .form-select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: white;
        }
        
        .form-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: white;
        }
        
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
        }
        
        .file-upload-area:hover {
            border-color: #9ca3af;
        }
        
        .tab-active {
            background-color: #3b82f6;
            color: white;
        }
        
        .tab-inactive {
            background-color: #f3f4f6;
            color: #6b7280;
        }
        
        .chart-container {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .metric-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border-left: 4px solid #3b82f6;
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
        
        /* Анимации */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        .transition-colors {
            transition: color 0.2s, background-color 0.2s, border-color 0.2s;
        }
        
        /* Адаптивность */
        @media (min-width: 768px) {
            .md\:flex { display: flex; }
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .md\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        
        @media (min-width: 1024px) {
            .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        
        /* Стили для графиков */
        .chart-bar {
            background: linear-gradient(to top, #3b82f6, #60a5fa);
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .chart-bar:hover {
            opacity: 0.8;
        }
        
        .chart-legend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Навигация -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
                        Table Master
                    </a>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Главная</a>
                        <a href="{{ route('converter') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Конвертер</a>
                        <a href="{{ route('merger') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Слияние</a>
                        <a href="{{ route('splitter') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Разделение</a>
                        <a href="{{ route('analyzer') }}" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm font-medium">Анализ</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Заголовок -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Анализ и визуализация данных</h1>
                <p class="text-lg text-gray-600">Визуализируйте и анализируйте бухгалтерские данные с помощью графиков и диаграмм</p>
            </div>

            <!-- Загрузка файла -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Область загрузки -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Загрузите файл для анализа</h2>
                        <div id="fileDropZone" class="file-upload-area p-8 text-center cursor-pointer mb-4">
                            <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" class="hidden">
                            <div id="uploadContent">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 mb-2">Перетащите файл с данными</p>
                                <p class="text-sm text-gray-500">Поддерживаемые форматы: XLSX, XLS, CSV</p>
                            </div>
                            <div id="filePreview" class="hidden">
                                <div class="flex items-center justify-center space-x-4 p-4 bg-blue-50 rounded-lg">
                                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div class="text-left">
                                        <p id="fileName" class="font-medium text-gray-900"></p>
                                        <p id="fileSize" class="text-sm text-gray-500"></p>
                                    </div>
                                    <button type="button" id="removeFile" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button id="analyzeBtn" class="btn-primary" style="width: auto; padding: 0.5rem 1rem; font-size: 0.875rem; display: flex; align-items: center; justify-content: center;">
                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Анализировать данные
                        </button>
                    </div>

                    <!-- Настройки анализа -->
                    <div id="analysisSettings" class="hidden">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Настройки анализа</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Тип отчета</label>
                                <select id="reportType" class="form-select">
                                    <option value="financial">Финансовый отчет</option>
                                    <option value="sales">Отчет по продажам</option>
                                    <option value="expenses">Анализ расходов</option>
                                    <option value="custom">Произвольный анализ</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Период анализа</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="date" id="startDate" class="form-input" placeholder="Начальная дата">
                                    <input type="date" id="endDate" class="form-input" placeholder="Конечная дата">
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="font-medium text-gray-900 mb-3">Дополнительные опции</h3>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="showTrends" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Показывать тренды</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" id="comparePeriods" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Сравнение с предыдущим периодом</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" id="exportCharts" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Экспорт графиков</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Результаты анализа -->
            <div id="analysisResults" class="hidden">
                <!-- Ключевые метрики -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="metric-card income">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Общий доход</p>
                                <p class="text-2xl font-bold text-green-600" id="totalIncome">₽ 0</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2" id="incomeChange">+0% с прошлого периода</p>
                    </div>
                    
                    <div class="metric-card expenses">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Общие расходы</p>
                                <p class="text-2xl font-bold text-red-600" id="totalExpenses">₽ 0</p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2" id="expensesChange">+0% с прошлого периода</p>
                    </div>
                    
                    <div class="metric-card profit">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Чистая прибыль</p>
                                <p class="text-2xl font-bold text-purple-600" id="netProfit">₽ 0</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2" id="profitChange">+0% с прошлого периода</p>
                    </div>
                </div>

                <!-- Вкладки с графиками -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                    <div class="border-b border-gray-200">
                        <div class="flex overflow-x-auto">
                            <button class="tab-btn tab-active px-6 py-4 font-medium" data-tab="overview">
                                Обзор
                            </button>
                            <button class="tab-btn tab-inactive px-6 py-4 font-medium" data-tab="trends">
                                Тренды
                            </button>
                            <button class="tab-btn tab-inactive px-6 py-4 font-medium" data-tab="categories">
                                Категории
                            </button>
                            <button class="tab-btn tab-inactive px-6 py-4 font-medium" data-tab="comparison">
                                Сравнение
                            </button>
                        </div>
                    </div>

                    <!-- Контент вкладок -->
                    <div class="p-6">
                        <!-- Вкладка Обзор -->
                        <div id="tab-overview" class="tab-content">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div class="chart-container">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Доходы и расходы по месяцам</h3>
                                    <div id="incomeExpensesChart" class="h-80 flex items-end justify-between gap-2">
                                        <!-- График будет генерироваться JavaScript -->
                                        <div class="text-center text-sm text-gray-500">
                                            Загрузите данные для построения графиков
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="chart-container">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Структура расходов</h3>
                                    <div id="expensesPieChart" class="h-80 flex items-center justify-center">
                                        <div class="text-center text-sm text-gray-500">
                                            Загрузите данные для построения графиков
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка Тренды -->
                        <div id="tab-trends" class="tab-content hidden">
                            <div class="chart-container">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Тренды доходов и расходов</h3>
                                <div id="trendsChart" class="h-96">
                                    <div class="text-center text-sm text-gray-500">
                                        Анализ трендов будет доступен после загрузки данных
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка Категории -->
                        <div id="tab-categories" class="tab-content hidden">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div class="chart-container">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Топ категорий доходов</h3>
                                    <div id="incomeCategoriesChart" class="h-80">
                                        <div class="text-center text-sm text-gray-500">
                                            Данные по категориям будут отображены здесь
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="chart-container">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Топ категорий расходов</h3>
                                    <div id="expenseCategoriesChart" class="h-80">
                                        <div class="text-center text-sm text-gray-500">
                                            Данные по категориям будут отображены здесь
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Вкладка Сравнение -->
                        <div id="tab-comparison" class="tab-content hidden">
                            <div class="chart-container">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Сравнение периодов</h3>
                                <div id="comparisonChart" class="h-96">
                                    <div class="text-center text-sm text-gray-500">
                                        Сравнение периодов будет доступно после загрузки данных
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Детальная таблица -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Детализация данных</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категория</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Описание</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Доход</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Расход</th>
                                </tr>
                            </thead>
                            <tbody id="dataTable" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Загрузите файл для просмотра данных
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        class DataAnalyzer {
            constructor() {
                this.currentFile = null;
                this.sampleData = null;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.generateSampleData();
            }

            setupEventListeners() {
                const fileInput = document.getElementById('fileInput');
                const fileDropZone = document.getElementById('fileDropZone');
                const removeFileBtn = document.getElementById('removeFile');
                const analyzeBtn = document.getElementById('analyzeBtn');

                // Загрузка файла
                fileDropZone.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', (e) => this.handleFileSelect(e.target.files[0]));

                // Drag & Drop
                fileDropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    fileDropZone.style.borderColor = '#3b82f6';
                    fileDropZone.style.backgroundColor = '#eff6ff';
                });

                fileDropZone.addEventListener('dragleave', () => {
                    fileDropZone.style.borderColor = '#d1d5db';
                    fileDropZone.style.backgroundColor = '';
                });

                fileDropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    fileDropZone.style.borderColor = '#d1d5db';
                    fileDropZone.style.backgroundColor = '';
                    if (e.dataTransfer.files.length > 0) {
                        this.handleFileSelect(e.dataTransfer.files[0]);
                    }
                });

                removeFileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.removeFile();
                });

                analyzeBtn.addEventListener('click', () => this.analyzeData());

                // Переключение вкладок
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.addEventListener('click', () => this.switchTab(btn.dataset.tab));
                });
            }

            handleFileSelect(file) {
                if (!file) return;

                const isValidType = file.name.endsWith('.xlsx') || 
                    file.name.endsWith('.xls') ||
                    file.name.endsWith('.csv');

                if (!isValidType) {
                    alert('ОШИБКА: Неподдерживаемый формат файла');
                    return;
                }

                this.currentFile = file;
                this.displayFileInfo(file);
                document.getElementById('analyzeBtn').classList.remove('hidden');
                document.getElementById('analysisSettings').classList.remove('hidden');
            }

            displayFileInfo(file) {
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = this.formatFileSize(file.size);
                document.getElementById('uploadContent').classList.add('hidden');
                document.getElementById('filePreview').classList.remove('hidden');
            }

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            removeFile() {
                this.currentFile = null;
                document.getElementById('fileInput').value = '';
                document.getElementById('uploadContent').classList.remove('hidden');
                document.getElementById('filePreview').classList.add('hidden');
                document.getElementById('analyzeBtn').classList.add('hidden');
                document.getElementById('analysisSettings').classList.add('hidden');
                document.getElementById('analysisResults').classList.add('hidden');
            }

            generateSampleData() {
                // Генерация демо-данных для примера
                this.sampleData = {
                    metrics: {
                        totalIncome: 1250000,
                        totalExpenses: 875000,
                        netProfit: 375000,
                        incomeChange: 15.2,
                        expensesChange: 8.7,
                        profitChange: 28.5
                    },
                    monthlyData: [
                        { month: 'Янв', income: 98000, expenses: 72000 },
                        { month: 'Фев', income: 105000, expenses: 68000 },
                        { month: 'Мар', income: 112000, expenses: 75000 },
                        { month: 'Апр', income: 108000, expenses: 71000 },
                        { month: 'Май', income: 115000, expenses: 69000 },
                        { month: 'Июн', income: 125000, expenses: 82000 }
                    ],
                    expensesByCategory: [
                        { category: 'Зарплаты', amount: 350000, color: '#3b82f6' },
                        { category: 'Аренда', amount: 180000, color: '#10b981' },
                        { category: 'Маркетинг', amount: 120000, color: '#f59e0b' },
                        { category: 'Оборудование', amount: 85000, color: '#ef4444' },
                        { category: 'Прочие', amount: 140000, color: '#8b5cf6' }
                    ]
                };
            }

            analyzeData() {
                // Показываем индикатор загрузки
                const analyzeBtn = document.getElementById('analyzeBtn');
                const originalText = analyzeBtn.innerHTML;
                analyzeBtn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mx-auto"></div>';
                analyzeBtn.disabled = true;

                // Имитация анализа данных
                setTimeout(() => {
                    this.displayAnalysisResults();
                    analyzeBtn.innerHTML = originalText;
                    analyzeBtn.disabled = false;
                }, 1500);
            }

            displayAnalysisResults() {
                document.getElementById('analysisResults').classList.remove('hidden');
                
                // Обновляем метрики
                this.updateMetrics();
                
                // Строим графики
                this.renderCharts();
                
                // Заполняем таблицу
                this.populateDataTable();
            }

            updateMetrics() {
                const data = this.sampleData.metrics;
                
                document.getElementById('totalIncome').textContent = `₽ ${this.formatNumber(data.totalIncome)}`;
                document.getElementById('totalExpenses').textContent = `₽ ${this.formatNumber(data.totalExpenses)}`;
                document.getElementById('netProfit').textContent = `₽ ${this.formatNumber(data.netProfit)}`;
                
                document.getElementById('incomeChange').textContent = `+${data.incomeChange}% с прошлого периода`;
                document.getElementById('expensesChange').textContent = `+${data.expensesChange}% с прошлого периода`;
                document.getElementById('profitChange').textContent = `+${data.profitChange}% с прошлого периода`;
            }

            formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }

            renderCharts() {
                this.renderIncomeExpensesChart();
                this.renderExpensesPieChart();
            }

            renderIncomeExpensesChart() {
                const container = document.getElementById('incomeExpensesChart');
                const data = this.sampleData.monthlyData;
                
                container.innerHTML = '';
                
                const maxValue = Math.max(...data.map(d => Math.max(d.income, d.expenses)));
                
                data.forEach(item => {
                    const incomeHeight = (item.income / maxValue) * 280;
                    const expensesHeight = (item.expenses / maxValue) * 280;
                    
                    const column = document.createElement('div');
                    column.className = 'flex flex-col items-center gap-1';
                    
                    // Столбец доходов
                    const incomeBar = document.createElement('div');
                    incomeBar.className = 'chart-bar w-6 rounded-t';
                    incomeBar.style.height = `${incomeHeight}px`;
                    incomeBar.style.background = 'linear-gradient(to top, #10b981, #34d399)';
                    incomeBar.title = `Доход: ₽${this.formatNumber(item.income)}`;
                    
                    // Столбец расходов
                    const expensesBar = document.createElement('div');
                    expensesBar.className = 'chart-bar w-6 rounded-t';
                    expensesBar.style.height = `${expensesHeight}px`;
                    expensesBar.style.background = 'linear-gradient(to top, #ef4444, #f87171)';
                    expensesBar.title = `Расход: ₽${this.formatNumber(item.expenses)}`;
                    
                    // Подпись месяца
                    const label = document.createElement('div');
                    label.className = 'text-xs text-gray-600 mt-2';
                    label.textContent = item.month;
                    
                    column.appendChild(incomeBar);
                    column.appendChild(expensesBar);
                    column.appendChild(label);
                    container.appendChild(column);
                });

                // Легенда
                const legend = document.createElement('div');
                legend.className = 'flex justify-center gap-4 mt-4';
                legend.innerHTML = `
                    <div class="chart-legend">
                        <div class="legend-color" style="background: linear-gradient(to top, #10b981, #34d399);"></div>
                        <span>Доходы</span>
                    </div>
                    <div class="chart-legend">
                        <div class="legend-color" style="background: linear-gradient(to top, #ef4444, #f87171);"></div>
                        <span>Расходы</span>
                    </div>
                `;
                container.appendChild(legend);
            }

            renderExpensesPieChart() {
                const container = document.getElementById('expensesPieChart');
                const data = this.sampleData.expensesByCategory;
                
                container.innerHTML = '';
                
                // Создаем SVG для круговой диаграммы
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('width', '300');
                svg.setAttribute('height', '300');
                svg.setAttribute('viewBox', '0 0 300 300');
                
                const total = data.reduce((sum, item) => sum + item.amount, 0);
                let currentAngle = 0;
                
                data.forEach((item, index) => {
                    const percentage = (item.amount / total) * 100;
                    const angle = (percentage / 100) * 360;
                    
                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    const startAngle = currentAngle;
                    const endAngle = currentAngle + angle;
                    
                    const start = this.polarToCartesian(150, 150, 120, startAngle);
                    const end = this.polarToCartesian(150, 150, 120, endAngle);
                    const largeArcFlag = angle > 180 ? 1 : 0;
                    
                    const pathData = [
                        `M 150 150`,
                        `L ${start.x} ${start.y}`,
                        `A 120 120 0 ${largeArcFlag} 1 ${end.x} ${end.y}`,
                        'Z'
                    ].join(' ');
                    
                    path.setAttribute('d', pathData);
                    path.setAttribute('fill', item.color);
                    path.setAttribute('stroke', 'white');
                    path.setAttribute('stroke-width', '2');
                    path.style.cursor = 'pointer';
                    path.title = `${item.category}: ${percentage.toFixed(1)}% (₽${this.formatNumber(item.amount)})`;
                    
                    svg.appendChild(path);
                    currentAngle += angle;
                });
                
                container.appendChild(svg);
                
                // Легенда
                const legend = document.createElement('div');
                legend.className = 'grid grid-cols-2 gap-2 mt-4';
                
                data.forEach(item => {
                    const percentage = ((item.amount / total) * 100).toFixed(1);
                    const legendItem = document.createElement('div');
                    legendItem.className = 'chart-legend';
                    legendItem.innerHTML = `
                        <div class="legend-color" style="background-color: ${item.color};"></div>
                        <span class="text-sm">${item.category} (${percentage}%)</span>
                    `;
                    legend.appendChild(legendItem);
                });
                
                container.appendChild(legend);
            }

            polarToCartesian(centerX, centerY, radius, angleInDegrees) {
                const angleInRadians = (angleInDegrees - 90) * Math.PI / 180.0;
                return {
                    x: centerX + (radius * Math.cos(angleInRadians)),
                    y: centerY + (radius * Math.sin(angleInRadians))
                };
            }

            populateDataTable() {
                const tbody = document.getElementById('dataTable');
                tbody.innerHTML = '';
                
                // Демо-данные для таблицы
                const demoData = [
                    { date: '2024-01-15', category: 'Продажи', description: 'Продажа продукта А', income: 50000, expense: 0 },
                    { date: '2024-01-18', category: 'Зарплаты', description: 'Зарплата сотрудникам', income: 0, expense: 250000 },
                    { date: '2024-01-20', category: 'Маркетинг', description: 'Рекламная кампания', income: 0, expense: 50000 },
                    { date: '2024-02-05', category: 'Продажи', description: 'Продажа продукта Б', income: 75000, expense: 0 },
                    { date: '2024-02-10', category: 'Аренда', description: 'Аренда офиса', income: 0, expense: 150000 }
                ];
                
                demoData.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.date}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${item.category}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">${item.description}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">${item.income ? `₽${this.formatNumber(item.income)}` : '-'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">${item.expense ? `₽${this.formatNumber(item.expense)}` : '-'}</td>
                    `;
                    tbody.appendChild(row);
                });
            }

            switchTab(tabName) {
                // Обновляем активную вкладку
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('tab-active');
                    btn.classList.add('tab-inactive');
                });
                
                document.querySelector(`[data-tab="${tabName}"]`).classList.remove('tab-inactive');
                document.querySelector(`[data-tab="${tabName}"]`).classList.add('tab-active');
                
                // Показываем соответствующий контент
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                
                document.getElementById(`tab-${tabName}`).classList.remove('hidden');
            }
        }

        // Инициализация
        document.addEventListener('DOMContentLoaded', () => {
            window.dataAnalyzer = new DataAnalyzer();
        });
    </script>
</body>
</html>