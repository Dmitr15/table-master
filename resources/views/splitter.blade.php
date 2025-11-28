<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Разделение - Table Master</title>

    <link rel="preconnect" href="https://fonts.bunny.net  ">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* === Базовые стили (без изменений, только добавим адаптив в конец) === */
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

    .split-methods {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .split-method {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .split-method:hover {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .split-method.active {
        border-color: #667eea;
        background: #f0f4ff;
        box-shadow: 0 4px 6px -1px rgba(102, 126, 234, 0.1);
    }

    .method-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: #e0e7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .split-method.active .method-icon {
        background: #667eea;
    }

    .split-method.active .method-icon svg {
        color: white;
    }

    .method-icon svg {
        width: 24px;
        height: 24px;
        color: #667eea;
    }

    .method-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .method-description {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .split-method.active .method-title {
        color: #667eea;
    }

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

        .split-methods {
            grid-template-columns: 1fr;
        }

        .info-cards {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .form-input,
        .form-select {
            font-size: 0.875rem;
            padding: 0.6rem 0.8rem;
        }

        .method-title {
            font-size: 1rem;
        }

        .method-description {
            font-size: 0.75rem;
        }
    }

    /* Маленькие планшеты и телефоны в альбомной ориентации */
    @media (min-width: 769px) and (max-width: 1024px) {
        .info-cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .split-methods {
            grid-template-columns: 1fr;
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

        .split-method {
            padding: 1rem 0.75rem;
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
                <a href="{{ route('splitter') }}" class="active">Разделение</a>
                <a href="{{ route('analyzer') }}">Анализ</a>
            </nav>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="main-content">
        <div class="container">
            <!-- Заголовок страницы -->
            <div class="page-header">
                <h1 class="page-title">Разделение файлов</h1>
                <p class="page-subtitle">Разделите большой файл на несколько частей по выбранному критерию</p>
            </div>

            <!-- Основная карточка разделения -->
            <div class="converter-card">
                <!-- Форма разделения -->
                <form id="splitterForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Область загрузки файла -->
                    <div class="form-group">
                        <label class="form-label">Файл для разделения</label>
                        <div id="fileDropZone" data-drop-zone class="file-upload-area">
                            <input type="file" id="fileInput" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                            <div id="uploadContent">
                                <div class="upload-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="upload-text">Перетащите файл сюда</div>
                                <div class="upload-subtext">или кликните для выбора</div>
                                <div class="upload-note">Поддерживаемые форматы: XLSX, XLS, CSV • Максимальный размер: 10MB</div>
                            </div>
                            <div id="filePreview" class="hidden" data-file-container>
                                <!-- Превью файла будет добавляться сюда -->
                            </div>
                        </div>
                    </div>

                    <!-- Методы разделения -->
                    <div id="splitMethods" class="settings-section hidden">
                        <h3 class="form-label">Выберите метод разделения</h3>
                        
                        <div class="split-methods">
                            <!-- По листам -->
                            <div class="split-method active" data-method="sheets">
                                <div class="method-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                                    </svg>
                                </div>
                                <div class="method-title">По листам</div>
                                <div class="method-description">Каждый лист в отдельный файл</div>
                            </div>

                            <!-- По количеству строк -->
                            <div class="split-method" data-method="rows">
                                <div class="method-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                    </svg>
                                </div>
                                <div class="method-title">По строкам</div>
                                <div class="method-description">Разделить на части по N строк</div>
                            </div>
                        </div>

                        <!-- Настройки разделения -->
                        <div id="splitSettings">
                            <!-- Настройки для разделения по листам -->
                            <div id="sheetsSettings">
                                <div class="form-group">
                                    <label class="form-label">Формат результата</label>
                                    <select id="outputFormatSheets" name="format" class="form-select">
                                        <option value="xlsx">Excel Workbook (XLSX)</option>
                                        <option value="xls">Excel (XLS)</option>
                                        <option value="zip">ZIP архив</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Настройки для разделения по строкам -->
                            <div id="rowsSettings" class="hidden">
                                <div class="form-group">
                                    <label for="rowsPerFile" class="form-label">Количество строк в каждой части</label>
                                    <input type="number" id="rowsPerFile" name="rows_per_file" value="100" min="1" class="form-input" placeholder="Введите количество строк">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Формат результата</label>
                                    <select id="outputFormatRows" name="format" class="form-select">
                                        <option value="xlsx">Excel Workbook (XLSX)</option>
                                        <option value="xls">Excel (XLS)</option>
                                        <option value="zip">ZIP архив</option>
                                    </select>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Примерное количество файлов: <span id="estimatedFiles">0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопка разделения -->
                        <div class="form-group" style="text-align: right; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                            <button type="submit" class="btn-primary">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                Начать разделение
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Прогресс разделения -->
                <div id="splitProgress" class="result-section hidden">
                    <div class="result-icon processing">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="result-title">Разделение в процессе...</h3>
                    <p class="result-text" id="splitProgressText">Разделяем файл на части</p>
                    <div class="progress-bar">
                        <div id="splitProgressFill" class="progress-fill"></div>
                    </div>
                </div>

                <!-- Результат разделения -->
                <div id="splitResult" class="result-section hidden">
                    <div class="result-icon success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="result-title">Разделение завершено!</h3>
                    <p class="result-text">Файл успешно разделен на части</p>

                    <div id="splitDownloadSection" class="hidden">
                        <a id="splitDownloadLink" class="btn-primary" href="#" download>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Скачать файл
                        </a>
                    </div>

                    <button id="newSplit" class="btn-primary" style="margin-top: 1rem;">
                        Разделить другой файл
                    </button>
                </div>
            </div>

            <!-- Информационные карточки -->
            <div class="info-cards">
                <div class="info-card feature">
                    <div class="info-icon-small">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                        </svg>
                    </div>
                    <h4 class="info-title">По листам</h4>
                    <p class="info-description">Каждый лист Excel в отдельный файл</p>
                </div>

                <div class="info-card">
                    <div class="info-icon-small">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                        </svg>
                    </div>
                    <h4 class="info-title">По строкам</h4>
                    <p class="info-description">Разделение на равные части</p>
                </div>

                <div class="info-card">
                    <div class="info-icon-small">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h4 class="info-title">Гибкая настройка</h4>
                    <p class="info-description">Различные методы разделения</p>
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

// Функция для добавления отладочной записи
window.addDebugLog = function(message, type = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = `[${timestamp}] ${type.toUpperCase()}: ${message}`;
    debugLog.push(logEntry);
    console.log(logEntry);
};

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('splitterForm');
    const fileInput = document.getElementById('fileInput');
    const fileDropZone = document.getElementById('fileDropZone');
    const uploadContent = document.getElementById('uploadContent');
    const filePreview = document.getElementById('filePreview');
    const splitMethods = document.getElementById('splitMethods');
    const splitResult = document.getElementById('splitResult');
    const splitProgress = document.getElementById('splitProgress');
    const splitDownloadSection = document.getElementById('splitDownloadSection');
    const splitDownloadLink = document.getElementById('splitDownloadLink');
    const newSplitBtn = document.getElementById('newSplit');
    const splitProgressFill = document.getElementById('splitProgressFill');
    const splitProgressText = document.getElementById('splitProgressText');
    const splitMethodElements = document.querySelectorAll('.split-method');
    const rowsPerFileInput = document.getElementById('rowsPerFile');
    const estimatedFilesSpan = document.getElementById('estimatedFiles');

    let currentFileId = null;
    let statusCheckInterval = null;
    let currentMethod = 'sheets';

    addDebugLog('Страница разделения загружена', 'info');

    // --- Обработка файла ---
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

        const allowedTypes = ['.xlsx', '.xls', '.csv'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(fileExtension)) {
            const errorMsg = `Неподдерживаемый формат файла: ${fileExtension}`;
            addDebugLog(errorMsg, 'error');
            showNotification('Ошибка: поддерживаются только XLSX, XLS, CSV файлы', 'error');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            const errorMsg = `Файл слишком большой: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
            addDebugLog(errorMsg, 'error');
            showNotification('Ошибка: файл слишком большой (макс. 10MB)', 'error');
            return;
        }

        showFilePreview(file);
        splitMethods.classList.remove('hidden');
        updateEstimatedFiles();
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

    // --- Функции очистки ---
    window.clearFileSelection = function() {
        addDebugLog('Очистка выбора файла', 'info');
        fileInput.value = '';
        uploadContent.classList.remove('hidden');
        filePreview.classList.add('hidden');
        splitMethods.classList.add('hidden');
    }

    // --- Выбор метода разделения ---
    splitMethodElements.forEach(method => {
        method.addEventListener('click', () => {
            splitMethodElements.forEach(m => m.classList.remove('active'));
            method.classList.add('active');
            currentMethod = method.dataset.method;
            toggleMethodSettings();
        });
    });

    function toggleMethodSettings() {
        const sheetsSettings = document.getElementById('sheetsSettings');
        const rowsSettings = document.getElementById('rowsSettings');

        if (currentMethod === 'sheets') {
            sheetsSettings.classList.remove('hidden');
            rowsSettings.classList.add('hidden');
        } else {
            sheetsSettings.classList.add('hidden');
            rowsSettings.classList.remove('hidden');
            updateEstimatedFiles();
        }
    }

    function updateEstimatedFiles() {
        // Демо-расчет: предполагаем, что в файле 1000 строк
        if (currentMethod === 'rows') {
            const rowsPerFile = parseInt(rowsPerFileInput.value) || 100;
            const estimatedFiles = Math.ceil(1000 / rowsPerFile);
            estimatedFilesSpan.textContent = estimatedFiles;
        } else {
            estimatedFilesSpan.textContent = '1';
        }
    }

    rowsPerFileInput.addEventListener('input', updateEstimatedFiles);

    // --- Обработка отправки формы ---
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        if (fileInput.files.length === 0) {
            addDebugLog('Файл не выбран', 'error');
            showNotification('Выберите файл для разделения', 'error');
            return;
        }

        try {
            submitButton.innerHTML = 'Отправка...';
            submitButton.disabled = true;

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('method', currentMethod);

            if (currentMethod === 'rows') {
                formData.append('rows_per_file', rowsPerFileInput.value);
                formData.append('format', document.getElementById('outputFormatRows').value);
            } else {
                formData.append('format', document.getElementById('outputFormatSheets').value);
            }

            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            addDebugLog(`Начало разделения файла методом: ${currentMethod}`, 'info');
            addDebugLog(`Отправка запроса на: /split-file`, 'info');

            const response = await fetch('/split-file', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            addDebugLog(`Ответ получен. Статус: ${response.status}`, 'info');

            const contentType = response.headers.get('content-type');
            addDebugLog(`Content-Type ответа: ${contentType}`, 'info');

            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                addDebugLog(`Некорректный Content-Type. Ответ: ${text.substring(0, 500)}`, 'error');
                throw new Error('Сервер вернул некорректный ответ (не JSON). Проверьте отладочную информацию.');
            }

            const result = await response.json();
            addDebugLog(`Ответ JSON: ${JSON.stringify(result)}`, 'info');

            if (result.success) {
                currentFileId = result.file_id;
                addDebugLog(`Разделение начато. File ID: ${currentFileId}`, 'success');

                splitMethods.classList.add('hidden');
                splitProgress.classList.remove('hidden');
                splitProgressFill.style.width = '30%';
                splitProgressText.textContent = 'Начинаем разделение файла...';

                startStatusChecking();
            } else {
                addDebugLog(`Ошибка разделения: ${result.message}`, 'error');
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Split error:', error);
            addDebugLog(`Ошибка: ${error.message}`, 'error');
            showNotification(error.message, 'error');
        } finally {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    });

    // --- Проверка статуса ---
    function startStatusChecking() {
        let attempts = 0;
        const maxAttempts = 60;

        addDebugLog('Запуск проверки статуса разделения', 'info');

        statusCheckInterval = setInterval(async () => {
            attempts++;

            if (attempts > maxAttempts) {
                clearInterval(statusCheckInterval);
                addDebugLog('Превышено максимальное количество попыток проверки статуса разделения', 'error');
                showNotification('Разделение занимает слишком много времени. Попробуйте позже.', 'error');
                splitProgress.classList.add('hidden');
                splitMethods.classList.remove('hidden');
                return;
            }

            addDebugLog(`Проверка статуса разделения #${attempts} для file_id: ${currentFileId}`, 'info');
            await checkSplitStatus();
        }, 2000);
    }

    async function checkSplitStatus() {
        if (!currentFileId) {
            addDebugLog('Нет currentFileId для проверки статуса разделения', 'error');
            return;
        }

        try {
            const response = await fetch(`/check-status/${currentFileId}`);
            addDebugLog(`Статус разделения проверен. Код: ${response.status}`, 'info');

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                addDebugLog(`Некорректный Content-Type при проверке статуса разделения: ${contentType}`, 'error');
                addDebugLog(`Ответ: ${text.substring(0, 200)}`, 'error');
                return;
            }

            const result = await response.json();
            addDebugLog(`Статус разделения ответ: ${JSON.stringify(result)}`, 'info');

            if (result.status === 'completed' && result.file) {
                clearInterval(statusCheckInterval);
                addDebugLog('Разделение завершено успешно', 'success');
                splitProgress.classList.add('hidden');
                splitResult.classList.remove('hidden');
                splitDownloadSection.classList.remove('hidden');
                splitDownloadLink.href = result.file;

                const fileName = 'split_files.' + (result.file.split('.').pop() || 'zip');
                splitDownloadLink.download = fileName;
                addDebugLog(`Ссылка для скачивания разделения: ${result.file}`, 'success');

                showNotification('Файл успешно разделен!', 'success');
            } else if (result.status === 'processing') {
                splitProgressFill.style.width = '70%';
                splitProgressText.textContent = 'Разделяем данные...';
                addDebugLog('Разделение в процессе...', 'info');
            } else if (result.status === 'failed') {
                clearInterval(statusCheckInterval);
                addDebugLog('Разделение завершилось ошибкой', 'error');
                throw new Error('Разделение не удалось');
            } else {
                splitProgressFill.style.width = '50%';
                splitProgressText.textContent = 'Файл в очереди на разделение...';
                addDebugLog('Статус разделения: ожидание обработки', 'info');
            }
        } catch (error) {
            clearInterval(statusCheckInterval);
            addDebugLog(`Ошибка при проверке статуса разделения: ${error.message}`, 'error');
            showNotification('Ошибка при проверке статуса: ' + error.message, 'error');
            splitProgress.classList.add('hidden');
            splitMethods.classList.remove('hidden');
        }
    }

    // --- Новое разделение ---
    newSplitBtn.addEventListener('click', () => {
        addDebugLog('Очистка для нового разделения', 'info');
        fileInput.value = '';
        uploadContent.classList.remove('hidden');
        filePreview.classList.add('hidden');
        splitMethods.classList.add('hidden');
        splitResult.classList.add('hidden');
        splitProgress.classList.add('hidden');
        splitDownloadSection.classList.add('hidden');

        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
            addDebugLog('Проверка статуса разделения остановлена', 'info');
        }

        currentFileId = null;
        splitProgressFill.style.width = '0%';
    });

    // --- Функция уведомлений ---
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

    // --- Глобальные функции для отладки через консоль ---
    window.getDebugLog = function() {
        return debugLog;
    };

    window.clearDebugLog = function() {
        debugLog = [];
    };
});
</script>