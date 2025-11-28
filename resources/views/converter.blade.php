<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Конвертер - Table Master</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
    body {
        font-family: 'Figtree', -apple-system, BlinkMacSystemFont, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8fafc;
        min-height: 100vh;
    }

    /* Хедер */
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

    /* Основной контент */
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

    /* Карточка конвертера */
    .converter-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-bottom: 2rem;
    }

    /* Область загрузки файла */
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

    /* Настройки */
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

    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: white;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Кнопки */
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

    /* Результат */
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

    /* Прогресс бар */
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

    /* Информационные карточки */
    .info-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .info-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .info-card.feature {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .info-card.feature .info-title {
        color: white;
    }

    .info-card.feature .info-description {
        color: rgba(255, 255, 255, 0.9);
    }

    .info-icon-small {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .info-card:not(.feature) .info-icon-small {
        background: #e0e7ff;
    }

    .info-card:not(.feature) .info-icon-small svg {
        color: #667eea;
    }

    .info-card.feature .info-icon-small {
        background: rgba(255, 255, 255, 0.2);
    }

    .info-card.feature .info-icon-small svg {
        color: white;
    }

    .info-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .info-description {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .hidden {
        display: none;
    }

    /* Отладочная информация */
    .debug-info {
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        font-family: monospace;
        font-size: 0.875rem;
        color: #374151;
        max-height: 200px;
        overflow-y: auto;
    }

    .debug-toggle {
        background: #6b7280;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.875rem;
        margin-top: 1rem;
    }

    .debug-toggle:hover {
        background: #4b5563;
    }

    /* Анимации */
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
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

        .info-cards {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

    /* Маленькие планшеты и телефоны в альбомной ориентации */
    @media (min-width: 769px) and (max-width: 1024px) {
        .info-cards {
            grid-template-columns: repeat(2, 1fr);
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

        .debug-toggle {
            width: 100%;
            text-align: center;
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
                <a href="{{ route('converter') }}" class="active">Конвертер</a>
                <a href="{{ route('merger') }}">Слияние</a>
                <a href="{{ route('splitter') }}">Разделение</a>
                <a href="{{ route('analyzer') }}">Анализ</a>
            </nav>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="main-content">
        <div class="container">
            <!-- Заголовок страницы -->
            <div class="page-header">
                <h1 class="page-title">Конвертер файлов</h1>
                <p class="page-subtitle">Конвертируйте табличные файлы между различными форматами быстро и безопасно</p>
            </div>

            <!-- Основная карточка конвертера -->
            <div class="converter-card">
                <!-- Форма конвертации -->
                <form id="converterForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Область загрузки файла -->
                    <div class="form-group">
                        <div id="fileDropZone" data-drop-zone class="file-upload-area">
                            <input type="file" id="fileInput" data-file-upload accept=".xlsx,.xls,.csv,.ods" class="hidden" required name="xls_file">
                            
                            <div id="uploadContent">
                                <div class="upload-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="upload-text">Перетащите файл сюда</div>
                                <div class="upload-subtext">или кликните для выбора</div>
                                <div class="upload-note">Поддерживаемые форматы: XLSX, XLS, CSV, ODS • Максимальный размер: 50MB</div>
                            </div>
                            
                            <div id="filePreview" class="hidden" data-file-container>
                                <!-- Преью файла будет добавляться сюда через JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Настройки конвертации -->
                    <div id="conversionSettings" class="settings-section hidden">
                        <!-- Выбор формата -->
                        <div class="form-group">
                            <label for="outputFormat" class="form-label">Целевой формат</label>
                            <select id="outputFormat" name="format" class="form-select" required>
                                <option value="">Выберите формат для конвертации</option>
                                <option value="xlsxToXls">XLSX → XLS</option>
                                <option value="xlsToXlsx">XLS → XLSX</option>
                                <option value="excelToOds">Excel → ODS</option>
                                <option value="excelToCsv">Excel → CSV</option>
                                <option value="excelToHtml">Excel → HTML</option>
                            </select>
                        </div>

                        <!-- Кнопка конвертации -->
                        <div class="form-group" style="text-align: right; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                            <button type="submit" class="btn-primary">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v2"></path>
                                </svg>
                                Начать конвертацию
                            </button>
                        </div>

                        <!-- Отладочная информация -->
                        <button type="button" class="debug-toggle" onclick="toggleDebugInfo()">
                            Показать отладочную информацию
                        </button>
                        <div id="debugInfo" class="debug-info hidden">
                            <div><strong>Отладочная информация:</strong></div>
                            <div id="debugContent">Здесь будет отображаться отладочная информация...</div>
                        </div>
                    </div>
                </form>

                <!-- Прогресс конвертации -->
                <div id="conversionProgress" class="result-section hidden">
                    <div class="result-icon processing">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="result-title">Конвертация в процессе...</h3>
                    <p class="result-text" id="progressText">Обрабатываем ваш файл</p>
                    <div class="progress-bar">
                        <div id="progressFill" class="progress-fill"></div>
                    </div>
                </div>

                <!-- Результат конвертации -->
                <div id="conversionResult" class="result-section hidden">
                    <div class="result-icon success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="result-title">Конвертация завершена!</h3>
                    <p class="result-text">Файл успешно сконвертирован в выбранный формат</p>
                    
                    <div id="downloadSection" class="hidden">
                        <a id="downloadLink" class="btn-primary" href="#" download>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Скачать файл
                        </a>
                    </div>
                    
                    <button id="newConversion" class="btn-primary" style="margin-top: 1rem;">
                        Конвертировать другой файл
                    </button>
                </div>
            </div>

            <!-- Информационные карточки -->
            <div class="info-cards">
                <div class="info-card feature">
                    <div class="info-icon-small">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="info-title">Поддерживаемые форматы</h4>
                    <p class="info-description">XLSX, XLS, CSV, ODS, HTML</p>
                </div>
                
                <div class="info-card">
                    <div class="info-icon-small">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h4 class="info-title">Безопасность</h4>
                    <p class="info-description">Файлы защищены авторизацией</p>
                </div>
                
                <div class="info-card">
                    <div class="info-icon-small">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h4 class="info-title">Высокая скорость</h4>
                    <p class="info-description">Быстрая обработка файлов</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Контейнер для уведомлений -->
    <div id="notification-container"></div>
</body>
</html>

<script>
// Глобальные переменные для отладки
let debugLog = [];

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('converterForm');
    const fileInput = document.getElementById('fileInput');
    const fileDropZone = document.getElementById('fileDropZone');
    const uploadContent = document.getElementById('uploadContent');
    const filePreview = document.getElementById('filePreview');
    const conversionSettings = document.getElementById('conversionSettings');
    const conversionResult = document.getElementById('conversionResult');
    const conversionProgress = document.getElementById('conversionProgress');
    const downloadSection = document.getElementById('downloadSection');
    const downloadLink = document.getElementById('downloadLink');
    const newConversionBtn = document.getElementById('newConversion');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');

    let currentFileId = null;
    let statusCheckInterval = null;

    // Функция для добавления отладочной записи
    window.addDebugLog = function(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = `[${timestamp}] ${type.toUpperCase()}: ${message}`;
        debugLog.push(logEntry);

        // Обновляем отображаемую информацию
        const debugContent = document.getElementById('debugContent');
        if (debugContent) {
            debugContent.innerHTML = debugLog.map(entry => {
                const color = type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : '#6b7280';
                return `<div style="color: ${color}">${entry}</div>`;
            }).join('');
            debugContent.scrollTop = debugContent.scrollHeight;
        }

        // Также выводим в консоль
        console.log(logEntry);
    };

    addDebugLog('Страница загружена', 'info');

    // Обработка drag & drop
    fileDropZone.addEventListener('click', () => fileInput.click());

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
            handleFileSelection(files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });

    function handleFileSelection(file) {
        addDebugLog(`Файл выбран: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`, 'info');

        // Валидация файла
        const allowedTypes = ['.xlsx', '.xls', '.csv', '.ods'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(fileExtension)) {
            const errorMsg = `Неподдерживаемый формат: ${fileExtension}`;
            addDebugLog(errorMsg, 'error');
            showNotification('Ошибка: поддерживаются только XLSX, XLS, CSV, ODS файлы', 'error');
            return;
        }

        if (file.size > 50 * 1024 * 1024) {
            const errorMsg = `Файл слишком большой: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
            addDebugLog(errorMsg, 'error');
            showNotification('Ошибка: файл слишком большой (макс. 50MB)', 'error');
            return;
        }

        // Показываем превью файла
        showFilePreview(file);

        // Показываем настройки конвертации
        conversionSettings.classList.remove('hidden');
        addDebugLog('Настройки конвертации показаны', 'info');
    }

    function showFilePreview(file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);

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
                <button type="button" onclick="clearFileSelection()" style="margin-left: auto; background: none; border: none; color: #6b7280; cursor: pointer;">
                    <svg fill="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                    </svg>
                </button>
            </div>
        `;

        uploadContent.classList.add('hidden');
        filePreview.classList.remove('hidden');
    }

    window.clearFileSelection = function() {
        addDebugLog('Очистка выбора файла', 'info');
        fileInput.value = '';
        uploadContent.classList.remove('hidden');
        filePreview.classList.add('hidden');
        conversionSettings.classList.add('hidden');
        conversionResult.classList.add('hidden');
        conversionProgress.classList.add('hidden');
        downloadSection.classList.add('hidden');

        // Останавливаем проверку статуса
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
            addDebugLog('Проверка статуса остановлена', 'info');
        }

        currentFileId = null;
        progressFill.style.width = '0%';
    }

    // Обработка отправки формы
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        const format = document.getElementById('outputFormat').value;

        if (!format) {
            addDebugLog('Формат не выбран', 'error');
            showNotification('Выберите формат для конвертации', 'error');
            return;
        }

        try {
            submitButton.innerHTML = 'Отправка...';
            submitButton.disabled = true;

            const formData = new FormData();
            formData.append('xls_file', fileInput.files[0]); // Исправлено на xls_file
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            addDebugLog(`Начало конвертации в формат: ${format}`, 'info');

            // Сначала загружаем файл, чтобы получить его ID
            addDebugLog('Сначала загружаем файл...', 'info');
            
            const uploadResponse = await fetch('{{ route("files.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            addDebugLog(`Ответ загрузки получен. Статус: ${uploadResponse.status}`, 'info');

            // Получаем текст ответа для отладки
            const responseText = await uploadResponse.text();
            addDebugLog(`Текст ответа загрузки: ${responseText.substring(0, 500)}`, 'info');

            if (!uploadResponse.ok) {
                // Пробуем распарсить как JSON для получения деталей ошибки
                try {
                    const errorResult = JSON.parse(responseText);
                    addDebugLog(`Ошибка загрузки: ${JSON.stringify(errorResult)}`, 'error');
                    throw new Error(errorResult.message || 'Ошибка при загрузке файла');
                } catch (parseError) {
                    // Если не JSON, показываем текст ошибки
                    addDebugLog(`Не удалось распарсить ответ: ${parseError.message}`, 'error');
                    throw new Error(`HTTP error! status: ${uploadResponse.status}. Response: ${responseText.substring(0, 200)}`);
                }
            }

            // Парсим успешный ответ
            const uploadResult = JSON.parse(responseText);
            addDebugLog(`Результат загрузки: ${JSON.stringify(uploadResult)}`, 'info');

            if (!uploadResult.success) {
                throw new Error(uploadResult.message || 'Ошибка при загрузке файла');
            }

            // Получаем ID загруженного файла
            currentFileId = uploadResult.id || uploadResult.file_id;
            addDebugLog(`Файл загружен. File ID: ${currentFileId}`, 'success');

            // Теперь запускаем конвертацию
            const routeMap = {
                'xlsxToXls': '{{ route("xlsxToXls", ["id" => "FILE_ID"]) }}',
                'xlsToXlsx': '{{ route("xlsToXlsx", ["id" => "FILE_ID"]) }}',
                'excelToOds': '{{ route("excelToOds", ["id" => "FILE_ID"]) }}',
                'excelToCsv': '{{ route("excelToCsv", ["id" => "FILE_ID"]) }}',
                'excelToHtml': '{{ route("excelToHtml", ["id" => "FILE_ID"]) }}'
            };

            const convertRoute = routeMap[format].replace('FILE_ID', currentFileId);
            addDebugLog(`Запуск конвертации по маршруту: ${convertRoute}`, 'info');

            const convertResponse = await fetch(convertRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            addDebugLog(`Ответ конвертации получен. Статус: ${convertResponse.status}`, 'info');

            const convertResult = await convertResponse.json();
            addDebugLog(`Результат конвертации: ${JSON.stringify(convertResult)}`, 'info');

            if (convertResult.success) {
                addDebugLog(`Конвертация начата успешно`, 'success');

                // Показываем прогресс
                conversionSettings.classList.add('hidden');
                conversionProgress.classList.remove('hidden');
                progressFill.style.width = '30%';
                progressText.textContent = 'Начинаем обработку файла...';

                // Запускаем проверку статуса
                startStatusChecking();
            } else {
                addDebugLog(`Ошибка конвертации: ${convertResult.message}`, 'error');
                throw new Error(convertResult.message);
            }
        } catch (error) {
            console.error('Conversion error:', error);
            addDebugLog(`Ошибка: ${error.message}`, 'error');
            showNotification(error.message, 'error');
        } finally {
            submitButton.innerHTML = 'Начать конвертацию';
            submitButton.disabled = false;
        }
    });

    // Запуск проверки статуса
    function startStatusChecking() {
        let attempts = 0;
        const maxAttempts = 60;

        addDebugLog('Запуск проверки статуса', 'info');

        statusCheckInterval = setInterval(async () => {
            attempts++;

            if (attempts > maxAttempts) {
                clearInterval(statusCheckInterval);
                addDebugLog('Превышено максимальное количество попыток проверки статуса', 'error');
                showNotification('Конвертация занимает слишком много времени. Попробуйте позже.', 'error');
                conversionProgress.classList.add('hidden');
                conversionSettings.classList.remove('hidden');
                return;
            }

            addDebugLog(`Проверка статуса #${attempts} для file_id: ${currentFileId}`, 'info');
            await checkConversionStatus();
        }, 2000);
    }

    // Проверка статуса конвертации
    async function checkConversionStatus() {
        if (!currentFileId) {
            addDebugLog('Нет currentFileId для проверки статуса', 'error');
            return;
        }

        try {
            const timestamp = new Date().getTime();
            const response = await fetch(`/convert/check/${currentFileId}?t=${timestamp}`, {
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });

            addDebugLog(`Статус проверен. Код: ${response.status}`, 'info');

            const result = await response.json();
            addDebugLog(`Статус ответ: ${JSON.stringify(result)}`, 'info');

            if (result.status === 'completed' && result.file) {
                clearInterval(statusCheckInterval);
                addDebugLog('✅ Конвертация завершена успешно', 'success');
                
                conversionProgress.classList.add('hidden');
                conversionResult.classList.remove('hidden');
                downloadSection.classList.remove('hidden');
                downloadLink.href = result.file;
                
                // Устанавливаем имя файла для скачивания
                const fileName = fileInput.files[0].name.replace(/\.[^/.]+$/, "") + '_converted.' + getFileExtension(document.getElementById('outputFormat').value);
                downloadLink.download = fileName;
                addDebugLog(`📥 Ссылка для скачивания: ${result.file}`, 'success');
                
                showNotification('Файл успешно сконвертирован!', 'success');
                
            } else if (result.status === 'completed') {
                clearInterval(statusCheckInterval);
                addDebugLog('⚠️ Конвертация завершена, но нет ссылки для скачивания', 'warning');
                showNotification('Конвертация завершена, но файл недоступен для скачивания', 'warning');
                
            } else if (result.status === 'processing') {
                progressFill.style.width = '70%';
                progressText.textContent = 'Обрабатываем данные...';
                addDebugLog('🔄 Конвертация в процессе...', 'info');
                
            } else if (result.status === 'failed') {
                clearInterval(statusCheckInterval);
                addDebugLog('❌ Конвертация завершилась ошибкой', 'error');
                throw new Error('Конвертация не удалась');
                
            } else if (result.status === 'error') {
                clearInterval(statusCheckInterval);
                addDebugLog(`❌ Ошибка конвертации: ${result.message}`, 'error');
                throw new Error(result.message);
                
            } else {
                progressFill.style.width = '50%';
                progressText.textContent = 'Файл в очереди на обработку...';
                addDebugLog('⏳ Статус: ожидание обработки', 'info');
            }
        } catch (error) {
            clearInterval(statusCheckInterval);
            addDebugLog(`❌ Ошибка при проверке статуса: ${error.message}`, 'error');
            showNotification('Ошибка при проверке статуса: ' + error.message, 'error');
            conversionProgress.classList.add('hidden');
            conversionSettings.classList.remove('hidden');
        }
    }

    // Вспомогательная функция для получения расширения файла
    function getFileExtension(format) {
        const extensionMap = {
            'xlsxToXls': 'xls',
            'xlsToXlsx': 'xlsx',
            'excelToOds': 'ods',
            'excelToCsv': 'csv',
            'excelToHtml': 'html'
        };
        return extensionMap[format] || 'file';
    }

    // Новая конвертация
    newConversionBtn.addEventListener('click', clearFileSelection);

    // Функция уведомлений
    function showNotification(message, type = 'info') {
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

    // Функция для переключения отладочной информации
    window.toggleDebugInfo = function() {
        const debugInfo = document.getElementById('debugInfo');
        const debugToggle = document.querySelector('.debug-toggle');

        if (debugInfo.classList.contains('hidden')) {
            debugInfo.classList.remove('hidden');
            debugToggle.textContent = 'Скрыть отладочную информацию';
        } else {
            debugInfo.classList.add('hidden');
            debugToggle.textContent = 'Показать отладочную информацию';
        }
    };
});

// Глобальные функции для отладки через консоль
window.getDebugLog = function() {
    return debugLog;
};

window.clearDebugLog = function() {
    debugLog = [];
    const debugContent = document.getElementById('debugContent');
    if (debugContent) {
        debugContent.innerHTML = 'Лог очищен';
    }
};
</script>