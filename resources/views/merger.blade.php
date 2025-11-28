<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Слияние - Table Master</title>

    <link rel="preconnect" href="https://fonts.bunny.net  ">
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
                <a href="{{ route('converter') }}">Конвертер</a>
                <a href="{{ route('merger') }}" class="active">Слияние</a>
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
                <h1 class="page-title">Слияние файлов</h1>
                <p class="page-subtitle">Объедините данные из двух табличных файлов в один</p>
            </div>

            <!-- Основная карточка слияния -->
            <div class="converter-card">
                <!-- Форма слияния -->
                <form id="mergerForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Область загрузки первого файла -->
                    <div class="form-group">
                        <label class="form-label">Первый файл</label>
                        <div id="fileDropZone1" data-drop-zone class="file-upload-area">
                            <input type="file" id="fileInput1" name="file1" accept=".xlsx,.xls,.csv" class="hidden" required>
                            <div id="uploadContent1">
                                <div class="upload-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="upload-text">Перетащите файл сюда</div>
                                <div class="upload-subtext">или кликните для выбора</div>
                                <div class="upload-note">Поддерживаемые форматы: XLSX, XLS, CSV • Максимальный размер: 10MB</div>
                            </div>
                            <div id="filePreview1" class="hidden" data-file-container>
                                <!-- Превью файла будет добавляться сюда -->
                            </div>
                        </div>
                    </div>

                    <!-- Область загрузки второго файла -->
                    <div class="form-group">
                        <label class="form-label">Второй файл</label>
                        <div id="fileDropZone2" data-drop-zone class="file-upload-area">
                            <input type="file" id="fileInput2" name="file2" accept=".xlsx,.xls,.csv" class="hidden" required>
                            <div id="uploadContent2">
                                <div class="upload-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="upload-text">Перетащите файл сюда</div>
                                <div class="upload-subtext">или кликните для выбора</div>
                                <div class="upload-note">Поддерживаемые форматы: XLSX, XLS, CSV • Максимальный размер: 10MB</div>
                            </div>
                            <div id="filePreview2" class="hidden" data-file-container>
                                <!-- Превью файла будет добавляться сюда -->
                            </div>
                        </div>
                    </div>

                    <!-- Настройки слияния -->
                    <div id="mergeSettings" class="settings-section hidden">
                        <!-- Выбор формата результата -->
                        <div class="form-group">
                            <label for="outputFormatMerge" class="form-label">Формат результата</label>
                            <select id="outputFormatMerge" name="format" class="form-select">
                                <option value="xlsx">Excel Workbook (XLSX)</option>
                                <option value="xls">Excel (XLS)</option>
                            </select>
                        </div>

                        <!-- Кнопка слияния -->
                        <div class="form-group" style="text-align: right; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
                            <button type="submit" class="btn-primary">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Начать слияние
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Прогресс слияния -->
                <div id="mergeProgress" class="result-section hidden">
                    <div class="result-icon processing">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="result-title">Слияние в процессе...</h3>
                    <p class="result-text" id="mergeProgressText">Объединяем данные из файлов</p>
                    <div class="progress-bar">
                        <div id="mergeProgressFill" class="progress-fill"></div>
                    </div>
                </div>

                <!-- Результат слияния -->
                <div id="mergeResult" class="result-section hidden">
                    <div class="result-icon success">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="result-title">Слияние завершено!</h3>
                    <p class="result-text">Файлы успешно объединены</p>

                    <div id="mergeDownloadSection" class="hidden">
                        <a id="mergeDownloadLink" class="btn-primary" href="#" download>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Скачать файл
                        </a>
                    </div>

                    <button id="newMerge" class="btn-primary" style="margin-top: 1rem;">
                        Объединить другие файлы
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
                    <p class="info-description">XLSX, XLS, CSV</p>
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

// Функция для добавления отладочной записи
window.addDebugLog = function(message, type = 'info') {
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = `[${timestamp}] ${type.toUpperCase()}: ${message}`;
    debugLog.push(logEntry);
    console.log(logEntry);
};

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('mergerForm');
    const fileInput1 = document.getElementById('fileInput1');
    const fileInput2 = document.getElementById('fileInput2');
    const fileDropZone1 = document.getElementById('fileDropZone1');
    const fileDropZone2 = document.getElementById('fileDropZone2');
    const uploadContent1 = document.getElementById('uploadContent1');
    const uploadContent2 = document.getElementById('uploadContent2');
    const filePreview1 = document.getElementById('filePreview1');
    const filePreview2 = document.getElementById('filePreview2');
    const mergeSettings = document.getElementById('mergeSettings');
    const mergeResult = document.getElementById('mergeResult');
    const mergeProgress = document.getElementById('mergeProgress');
    const mergeDownloadSection = document.getElementById('mergeDownloadSection');
    const mergeDownloadLink = document.getElementById('mergeDownloadLink');
    const newMergeBtn = document.getElementById('newMerge');
    const mergeProgressFill = document.getElementById('mergeProgressFill');
    const mergeProgressText = document.getElementById('mergeProgressText');

    let currentFileId = null;
    let statusCheckInterval = null;
    let uploadedFile1Id = null;
    let uploadedFile2Id = null;

    addDebugLog('Страница слияния загружена', 'info');

    // --- Обработка первого файла ---
    fileDropZone1.addEventListener('click', () => fileInput1.click());
    fileDropZone1.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileDropZone1.style.borderColor = '#667eea';
        fileDropZone1.style.background = '#f0f4ff';
    });
    fileDropZone1.addEventListener('dragleave', () => {
        fileDropZone1.style.borderColor = '#d1d5db';
        fileDropZone1.style.background = '#f9fafb';
    });
    fileDropZone1.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelection(files[0], fileInput1, uploadContent1, filePreview1, '1');
        }
    });
    fileInput1.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0], fileInput1, uploadContent1, filePreview1, '1');
        }
    });

    // --- Обработка второго файла ---
    fileDropZone2.addEventListener('click', () => fileInput2.click());
    fileDropZone2.addEventListener('dragover', (e) => {
        e.preventDefault();
        fileDropZone2.style.borderColor = '#667eea';
        fileDropZone2.style.background = '#f0f4ff';
    });
    fileDropZone2.addEventListener('dragleave', () => {
        fileDropZone2.style.borderColor = '#d1d5db';
        fileDropZone2.style.background = '#f9fafb';
    });
    fileDropZone2.addEventListener('drop', (e) => {
        e.preventDefault();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelection(files[0], fileInput2, uploadContent2, filePreview2, '2');
        }
    });
    fileInput2.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0], fileInput2, uploadContent2, filePreview2, '2');
        }
    });

    function handleFileSelection(file, input, content, preview, num) {
        addDebugLog(`Файл ${num} выбран: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`, 'info');

        const allowedTypes = ['.xlsx', '.xls', '.csv'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(fileExtension)) {
            const errorMsg = `Неподдерживаемый формат для файла ${num}: ${fileExtension}`;
            addDebugLog(errorMsg, 'error');
            showNotification(`Ошибка: поддерживаются только XLSX, XLS, CSV файлы (файл ${num})`, 'error');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            const errorMsg = `Файл ${num} слишком большой: ${(file.size / 1024 / 1024).toFixed(2)} MB`;
            addDebugLog(errorMsg, 'error');
            showNotification(`Ошибка: файл ${num} слишком большой (макс. 10MB)`, 'error');
            return;
        }

        showFilePreview(file, preview, content, num);

        // Показываем настройки слияния, если оба файла выбраны
        if (fileInput1.files.length > 0 && fileInput2.files.length > 0) {
            mergeSettings.classList.remove('hidden');
            addDebugLog('Настройки слияния показаны', 'info');
        }
    }

    function showFilePreview(file, preview, content, num) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        preview.innerHTML = `
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
                <button type="button" onclick="clearFileSelection${num}()" style="margin-left: auto; background: none; border: none; color: #6b7280; cursor: pointer;">
                    <svg fill="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                    </svg>
                </button>
            </div>
        `;
        content.classList.add('hidden');
        preview.classList.remove('hidden');
    }

    // --- Функции очистки ---
    window.clearFileSelection1 = function() {
        addDebugLog('Очистка выбора файла 1', 'info');
        fileInput1.value = '';
        uploadContent1.classList.remove('hidden');
        filePreview1.classList.add('hidden');
        uploadedFile1Id = null;
        if (fileInput2.files.length === 0) {
            mergeSettings.classList.add('hidden');
        }
    }

    window.clearFileSelection2 = function() {
        addDebugLog('Очистка выбора файла 2', 'info');
        fileInput2.value = '';
        uploadContent2.classList.remove('hidden');
        filePreview2.classList.add('hidden');
        uploadedFile2Id = null;
        if (fileInput1.files.length === 0) {
            mergeSettings.classList.add('hidden');
        }
    }

    // --- Обработка отправки формы ---
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;

        if (fileInput1.files.length === 0 || fileInput2.files.length === 0) {
            addDebugLog('Не выбраны оба файла', 'error');
            showNotification('Выберите оба файла для слияния', 'error');
            return;
        }

        try {
            submitButton.innerHTML = 'Отправка...';
            submitButton.disabled = true;

            const formData = new FormData();
            formData.append('file1', fileInput1.files[0]);
            formData.append('file2', fileInput2.files[0]);
            const format = document.getElementById('outputFormatMerge').value;
            formData.append('format', format);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            addDebugLog(`Начало слияния файлов`, 'info');
            addDebugLog(`Отправка запроса на: /merge-files`, 'info');

            const response = await fetch('/merge-files', {
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
                addDebugLog(`Слияние начато. File ID: ${currentFileId}`, 'success');

                mergeSettings.classList.add('hidden');
                mergeProgress.classList.remove('hidden');
                mergeProgressFill.style.width = '30%';
                mergeProgressText.textContent = 'Начинаем объединение файлов...';

                startStatusChecking();
            } else {
                addDebugLog(`Ошибка слияния: ${result.message}`, 'error');
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Merge error:', error);
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

        addDebugLog('Запуск проверки статуса слияния', 'info');

        statusCheckInterval = setInterval(async () => {
            attempts++;

            if (attempts > maxAttempts) {
                clearInterval(statusCheckInterval);
                addDebugLog('Превышено максимальное количество попыток проверки статуса слияния', 'error');
                showNotification('Слияние занимает слишком много времени. Попробуйте позже.', 'error');
                mergeProgress.classList.add('hidden');
                mergeSettings.classList.remove('hidden');
                return;
            }

            addDebugLog(`Проверка статуса слияния #${attempts} для file_id: ${currentFileId}`, 'info');
            await checkMergeStatus();
        }, 2000);
    }

    async function checkMergeStatus() {
        if (!currentFileId) {
            addDebugLog('Нет currentFileId для проверки статуса слияния', 'error');
            return;
        }

        try {
            const response = await fetch(`/check-status/${currentFileId}`);
            addDebugLog(`Статус слияния проверен. Код: ${response.status}`, 'info');

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                addDebugLog(`Некорректный Content-Type при проверке статуса слияния: ${contentType}`, 'error');
                addDebugLog(`Ответ: ${text.substring(0, 200)}`, 'error');
                return;
            }

            const result = await response.json();
            addDebugLog(`Статус слияния ответ: ${JSON.stringify(result)}`, 'info');

            if (result.status === 'completed' && result.file) {
                clearInterval(statusCheckInterval);
                addDebugLog('Слияние завершено успешно', 'success');
                mergeProgress.classList.add('hidden');
                mergeResult.classList.remove('hidden');
                mergeDownloadSection.classList.remove('hidden');
                mergeDownloadLink.href = result.file;

                const fileName = 'merged_file.' + (result.file.split('.').pop() || 'xlsx');
                mergeDownloadLink.download = fileName;
                addDebugLog(`Ссылка для скачивания слияния: ${result.file}`, 'success');

                showNotification('Файлы успешно объединены!', 'success');
            } else if (result.status === 'processing') {
                mergeProgressFill.style.width = '70%';
                mergeProgressText.textContent = 'Объединяем данные...';
                addDebugLog('Слияние в процессе...', 'info');
            } else if (result.status === 'failed') {
                clearInterval(statusCheckInterval);
                addDebugLog('Слияние завершилось ошибкой', 'error');
                throw new Error('Слияние не удалось');
            } else {
                mergeProgressFill.style.width = '50%';
                mergeProgressText.textContent = 'Файл в очереди на объединение...';
                addDebugLog('Статус слияния: ожидание обработки', 'info');
            }
        } catch (error) {
            clearInterval(statusCheckInterval);
            addDebugLog(`Ошибка при проверке статуса слияния: ${error.message}`, 'error');
            showNotification('Ошибка при проверке статуса: ' + error.message, 'error');
            mergeProgress.classList.add('hidden');
            mergeSettings.classList.remove('hidden');
        }
    }

    // --- Новое слияние ---
    newMergeBtn.addEventListener('click', () => {
        addDebugLog('Очистка для нового слияния', 'info');
        fileInput1.value = '';
        fileInput2.value = '';
        uploadContent1.classList.remove('hidden');
        uploadContent2.classList.remove('hidden');
        filePreview1.classList.add('hidden');
        filePreview2.classList.add('hidden');
        mergeSettings.classList.add('hidden');
        mergeResult.classList.add('hidden');
        mergeProgress.classList.add('hidden');
        mergeDownloadSection.classList.add('hidden');

        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            statusCheckInterval = null;
            addDebugLog('Проверка статуса слияния остановлена', 'info');
        }

        currentFileId = null;
        uploadedFile1Id = null;
        uploadedFile2Id = null;
        mergeProgressFill.style.width = '0%';
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