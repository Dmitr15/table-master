<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Конвертер - Table Master</title>
    
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
        .bg-blue-100 { background-color: #dbeafe; }
        .bg-green-100 { background-color: #dcfce7; }
        .bg-purple-100 { background-color: #e9d5ff; }
        .bg-blue-500 { background-color: #3b82f6; }
        .bg-green-500 { background-color: #10b981; }
        .bg-purple-500 { background-color: #8b5cf6; }
        .bg-gray-300 { background-color: #d1d5db; }
        
        .text-white { color: white; }
        .text-gray-900 { color: #111827; }
        .text-gray-700 { color: #374151; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-400 { color: #9ca3af; }
        .text-blue-600 { color: #2563eb; }
        .text-blue-700 { color: #1d4ed8; }
        .text-green-600 { color: #059669; }
        .text-purple-600 { color: #7c3aed; }
        .text-red-500 { color: #ef4444; }
        .text-red-600 { color: #dc2626; }
        
        .border { border-width: 1px; }
        .border-b { border-bottom-width: 1px; }
        .border-t { border-top-width: 1px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-300 { border-color: #d1d5db; }
        .border-blue-200 { border-color: #bfdbfe; }
        .border-green-200 { border-color: #bbf7d0; }
        .border-purple-200 { border-color: #ddd6fe; }
        
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-full { border-radius: 9999px; }
        
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        
        .p-2 { padding: 0.5rem; }
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .p-8 { padding: 2rem; }
        .px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
        .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
        
        .m-0 { margin: 0; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mt-2 { margin-top: 0.5rem; }
        
        .text-sm { font-size: 0.875rem; }
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
        
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        .space-x-3 > * + * { margin-left: 0.75rem; }
        .space-x-4 > * + * { margin-left: 1rem; }
        .space-x-8 > * + * { margin-left: 2rem; }
        .space-y-3 > * + * { margin-top: 0.75rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        
        .gap-6 { gap: 1.5rem; }
        
        .cursor-pointer { cursor: pointer; }
        .overflow-hidden { overflow: hidden; }
        .overflow-auto { overflow: auto; }
        
        .max-w-4xl { max-width: 56rem; }
        .max-w-7xl { max-width: 80rem; }
        .w-8 { width: 2rem; }
        .w-10 { width: 2.5rem; }
        .w-12 { width: 3rem; }
        .w-16 { width: 4rem; }
        .h-8 { height: 2rem; }
        .h-10 { height: 2.5rem; }
        .h-12 { height: 3rem; }
        .h-16 { height: 4rem; }
        
        .max-h-64 { max-height: 16rem; }
        
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
        
        .btn-success {
            background-color: #10b981;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-success:hover {
            background-color: #059669;
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
        
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
        }
        
        .file-upload-area:hover {
            border-color: #9ca3af;
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
        }
        
        @media (min-width: 1024px) {
            .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
        
        /* Специфичные стили для конвертера */
        .converter-steps {
            display: flex;
            justify-content: center;
            gap: 2rem;
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .step-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .step-number {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            font-size: 0.875rem;
        }
        
        .step-active .step-number {
            background-color: #3b82f6;
            color: white;
        }
        
        .step-inactive .step-number {
            background-color: #d1d5db;
            color: #6b7280;
        }
        
        .step-active .step-text {
            color: #2563eb;
            font-weight: 500;
        }
        
        .step-inactive .step-text {
            color: #6b7280;
            opacity: 0.5;
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
                        <a href="{{ route('converter') }}" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm font-medium">Конвертер</a>
                        <a href="{{ route('merger') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Слияние</a>
                        <a href="{{ route('splitter') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Разделение</a>
                        <a href="{{ route('analyzer') }}" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Анализ</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Заголовок -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Конвертер файлов</h1>
                <p class="text-lg text-gray-600">Конвертируйте табличные файлы между различными форматами</p>
            </div>

            <!-- Основная карточка конвертера -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Шаги конвертации -->
                <div class="converter-steps">
                    <div class="step-item step-active">
                        <div class="step-number">1</div>
                        <span class="step-text font-medium">Выбор файла</span>
                    </div>
                    <div class="step-item step-inactive">
                        <div class="step-number">2</div>
                        <span class="step-text">Настройки</span>
                    </div>
                    <div class="step-item step-inactive">
                        <div class="step-number">3</div>
                        <span class="step-text">Конвертация</span>
                    </div>
                </div>

                <!-- Форма конвертации -->
                <form id="converterForm" action="{{ route('converter.process') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    
                    <!-- Область загрузки файла -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Выберите файл для конвертации</label>
                        
                        <div id="fileDropZone" class="file-upload-area p-8 text-center cursor-pointer">
                            <input type="file" id="fileInput" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                            
                            <div id="uploadContent">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 mb-2">Перетащите файл сюда или кликните для выбора</p>
                                <p class="text-sm text-gray-500">Поддерживаемые форматы: XLSX, XLS, CSV</p>
                                <p class="text-xs text-gray-400 mt-2">Максимальный размер: 10MB</p>
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
                    </div>

                    <!-- Настройки конвертации -->
                    <div id="conversionSettings" class="hidden space-y-6">
                        <!-- Выбор формата -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="inputFormat" class="block text-sm font-medium text-gray-700 mb-2">Исходный формат</label>
                                <select id="inputFormat" class="form-select" disabled>
                                    <option value="">Определяется автоматически</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="outputFormat" class="block text-sm font-medium text-gray-700 mb-2">Целевой формат</label>
                                <select id="outputFormat" name="format" class="form-select" required>
                                    <option value="">Выберите формат</option>
                                    <option value="json">JSON</option>
                                    <option value="csv">CSV</option>
                                    <option value="xml">XML</option>
                                    <option value="tsv">TSV</option>
                                    <option value="pdf">PDF</option>
                                    <option value="html">HTML</option>
                                    <option value="xlsx">Excel (XLSX)</option>
                                    <option value="xls">Excel (XLS)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Кнопка конвертации -->
                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="btn-primary flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v2"></path>
                                </svg>
                                <span>Начать конвертацию</span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Результат конвертации -->
                <div id="conversionResult" class="hidden p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Конвертация завершена!</h3>
                        <p class="text-gray-600 mb-6">Файл успешно сконвертирован</p>
                        
                        <div class="flex justify-center space-x-4">
                            <a id="downloadLink" href="#" class="btn-success">Скачать файл</a>
                            <button type="button" id="convertAnother" class="btn-secondary">
                                Конвертировать другой файл
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Информационная панель -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-900">Поддерживаемые форматы</h4>
                            <p class="text-sm text-blue-700">XLSX, XLS, CSV, JSON, XML, PDF, HTML</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-green-900">Безопасность</h4>
                            <p class="text-sm text-green-700">Файлы не сохраняются на сервере</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-purple-900">Быстрая конвертация</h4>
                            <p class="text-sm text-purple-700">Процесс занимает несколько секунд</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript -->
    <script>
        class FileConverter {
            constructor() {
                this.currentFile = null;
                this.init();
            }

            init() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                const fileInput = document.getElementById('fileInput');
                const fileDropZone = document.getElementById('fileDropZone');
                const removeFileBtn = document.getElementById('removeFile');
                const convertAnotherBtn = document.getElementById('convertAnother');

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

                convertAnotherBtn.addEventListener('click', () => this.resetConverter());
            }

            handleFileSelect(file) {
                if (!file) return;

                // Проверка типа файла
                const isValidType = file.name.endsWith('.xlsx') || 
                    file.name.endsWith('.xls') ||
                    file.name.endsWith('.csv');

                if (!isValidType) {
                    alert('ОШИБКА: Неподдерживаемый формат файла');
                    return;
                }

                // Проверка размера файла
                if (file.size > 10 * 1024 * 1024) {
                    alert('ОШИБКА: Файл слишком большой. Максимальный размер: 10MB');
                    return;
                }

                this.currentFile = file;
                this.displayFileInfo(file);
                this.showConversionSettings();
            }

            displayFileInfo(file) {
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = this.formatFileSize(file.size);
                document.getElementById('uploadContent').classList.add('hidden');
                document.getElementById('filePreview').classList.remove('hidden');
                
                // Обновляем шаги
                document.querySelectorAll('.step-item')[1].classList.replace('step-inactive', 'step-active');
            }

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            showConversionSettings() {
                document.getElementById('conversionSettings').classList.remove('hidden');
            }

            removeFile() {
                this.currentFile = null;
                document.getElementById('fileInput').value = '';
                document.getElementById('uploadContent').classList.remove('hidden');
                document.getElementById('filePreview').classList.add('hidden');
                document.getElementById('conversionSettings').classList.add('hidden');
                
                // Сбрасываем шаги
                document.querySelectorAll('.step-item').forEach((step, index) => {
                    if (index > 0) {
                        step.classList.replace('step-active', 'step-inactive');
                    }
                });
            }

            resetConverter() {
                this.removeFile();
                document.getElementById('converterForm').classList.remove('hidden');
                document.getElementById('conversionResult').classList.add('hidden');
            }
        }

        // Инициализация
        document.addEventListener('DOMContentLoaded', () => {
            window.fileConverter = new FileConverter();
        });
    </script>
</body>
</html>