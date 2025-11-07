<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Разделение таблиц - Table Master</title>
    
    <style>
        /* Базовые стили (такие же как в merger) */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            color: #374151;
        }
        
        .min-h-screen { min-height: 100vh; }
        .bg-white { background-color: white; }
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-orange-50 { background-color: #fff7ed; }
        .bg-purple-50 { background-color: #faf5ff; }
        .bg-orange-100 { background-color: #fed7aa; }
        
        .text-white { color: white; }
        .text-gray-900 { color: #111827; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-orange-600 { color: #ea580c; }
        .text-purple-600 { color: #7c3aed; }
        
        .border { border-width: 1px; }
        .border-b { border-bottom-width: 1px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-300 { border-color: #d1d5db; }
        
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-xl { border-radius: 0.75rem; }
        
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
        .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
        
        .mx-auto { margin-left: auto; margin-right: auto; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        
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
        
        .space-x-4 > * + * { margin-left: 1rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        
        .cursor-pointer { cursor: pointer; }
        .overflow-hidden { overflow: hidden; }
        
        .max-w-4xl { max-width: 56rem; }
        .max-w-7xl { max-width: 80rem; }
        .w-full { width: 100%; }
        .w-8 { width: 2rem; }
        .w-10 { width: 2.5rem; }
        .w-12 { width: 3rem; }
        .h-8 { height: 2rem; }
        .h-10 { height: 2.5rem; }
        .h-12 { height: 3rem; }
        
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
            font-size: 0.875rem;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-warning {
            background-color: #ea580c;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 0.875rem;
        }
        
        .btn-warning:hover {
            background-color: #c2410c;
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
            font-size: 0.875rem;
        }
        
        .btn-secondary:hover {
            background-color: #d1d5db;
        }
        
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
        }
        
        .file-upload-area:hover {
            border-color: #9ca3af;
        }
        
        .split-option {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .split-option:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .split-option.active {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        @media (min-width: 768px) {
            .md\:flex { display: flex; }
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
    </style>
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
                        <a href="{{ route('splitter') }}" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm font-medium">Разделение</a>
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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Разделение таблиц</h1>
                <p class="text-lg text-gray-600">Разделите большую таблицу на несколько частей</p>
            </div>

            <!-- Основная карточка -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <!-- Загрузка файла -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Загрузите файл для разделения</h2>
                    <div id="fileDropZone" class="file-upload-area p-8 text-center cursor-pointer max-w-2xl mx-auto">
                        <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" class="hidden">
                        <div id="uploadContent">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-lg font-medium text-gray-600 mb-2">Перетащите файл для разделения</p>
                            <p class="text-sm text-gray-500">Поддерживаемые форматы: XLSX, XLS, CSV</p>
                        </div>
                        <div id="filePreview" class="hidden">
                            <div class="flex items-center justify-center space-x-4 p-4 bg-orange-50 rounded-lg">
                                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- Методы разделения -->
                <div id="splitMethods" class="hidden space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900">Выберите метод разделения</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- По количеству строк -->
                        <div class="split-option active" data-method="rows">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">По количеству строк</h4>
                                    <p class="text-sm text-gray-600">Разделить на части по N строк</p>
                                </div>
                            </div>
                        </div>

                        <!-- По столбцу -->
                        <div class="split-option" data-method="column">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">По значению столбца</h4>
                                    <p class="text-sm text-gray-600">Разделить по уникальным значениям</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Настройки разделения -->
                    <div id="splitSettings" class="space-y-4">
                        <!-- Настройки для разделения по строкам -->
                        <div id="rowsSettings">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Количество строк в каждой части</label>
                            <div class="flex items-center space-x-4">
                                <input type="number" id="rowsPerFile" value="100" min="1" class="w-32 p-2 border border-gray-300 rounded-lg">
                                <span class="text-sm text-gray-600">строк на файл</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Будет создано примерно <span id="estimatedFiles">0</span> файлов</p>
                        </div>

                        <!-- Настройки для разделения по столбцу -->
                        <div id="columnSettings" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Столбец для разделения</label>
                            <select id="splitColumn" class="w-full p-2 border border-gray-300 rounded-lg">
                                <option value="">Выберите столбец</option>
                                <option value="category">Категория</option>
                                <option value="department">Отдел</option>
                                <option value="region">Регион</option>
                            </select>
                        </div>
                    </div>

                    <!-- Кнопка разделения -->
                    <div class="flex justify-end">
                        <button id="splitBtn" class="btn-warning">
                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            Разделить таблицу
                        </button>
                    </div>
                </div>

                <!-- Результат -->
                <div id="splitResult" class="hidden">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">Таблица разделена!</h3>
                        <p class="text-gray-600">Создано <span id="filesCount">0</span> файлов</p>
                    </div>

                    <!-- Список созданных файлов -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Созданные файлы:</h4>
                        <div class="space-y-2" id="filesList">
                            <!-- Файлы будут добавлены здесь -->
                        </div>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button id="downloadAll" class="btn-primary">Скачать все (ZIP)</button>
                        <button id="newSplit" class="btn-secondary">Новое разделение</button>
                    </div>
                </div>
            </div>

            <!-- Информация -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-orange-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-orange-900">По строкам</h4>
                            <p class="text-sm text-orange-700">Разделение на равные части</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-purple-900">По столбцам</h4>
                            <p class="text-sm text-purple-700">Разделение по категориям</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-900">Гибкая настройка</h4>
                            <p class="text-sm text-blue-700">Различные методы разделения</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        class TableSplitter {
            constructor() {
                this.currentFile = null;
                this.currentMethod = 'rows';
                this.init();
            }

            init() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                const fileDropZone = document.getElementById('fileDropZone');
                const fileInput = document.getElementById('fileInput');
                const removeFileBtn = document.getElementById('removeFile');
                const splitOptions = document.querySelectorAll('.split-option');
                const splitBtn = document.getElementById('splitBtn');
                const newSplitBtn = document.getElementById('newSplit');

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

                // Выбор метода разделения
                splitOptions.forEach(option => {
                    option.addEventListener('click', () => {
                        splitOptions.forEach(opt => opt.classList.remove('active'));
                        option.classList.add('active');
                        this.currentMethod = option.dataset.method;
                        this.toggleMethodSettings();
                    });
                });

                // Кнопка разделения
                splitBtn.addEventListener('click', () => {
                    this.splitTable();
                });

                // Новое разделение
                newSplitBtn.addEventListener('click', () => {
                    this.resetSplitter();
                });

                // Расчет количества файлов
                document.getElementById('rowsPerFile').addEventListener('input', () => {
                    this.updateEstimatedFiles();
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
                document.getElementById('splitMethods').classList.remove('hidden');
                this.updateEstimatedFiles();
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

            toggleMethodSettings() {
                const rowsSettings = document.getElementById('rowsSettings');
                const columnSettings = document.getElementById('columnSettings');

                if (this.currentMethod === 'rows') {
                    rowsSettings.classList.remove('hidden');
                    columnSettings.classList.add('hidden');
                } else {
                    rowsSettings.classList.add('hidden');
                    columnSettings.classList.remove('hidden');
                }
            }

            updateEstimatedFiles() {
                // Демо-расчет: предполагаем, что в файле 1000 строк
                const rowsPerFile = parseInt(document.getElementById('rowsPerFile').value) || 100;
                const estimatedFiles = Math.ceil(1000 / rowsPerFile);
                document.getElementById('estimatedFiles').textContent = estimatedFiles;
            }

            splitTable() {
                const splitBtn = document.getElementById('splitBtn');
                const originalText = splitBtn.innerHTML;
                splitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';
                splitBtn.disabled = true;

                // Имитация процесса разделения
                setTimeout(() => {
                    document.getElementById('splitMethods').classList.add('hidden');
                    document.getElementById('splitResult').classList.remove('hidden');
                    this.generateFileList();
                    
                    splitBtn.innerHTML = originalText;
                    splitBtn.disabled = false;
                }, 2000);
            }

            generateFileList() {
                const filesList = document.getElementById('filesList');
                const filesCount = document.getElementById('filesCount');
                const estimatedFiles = document.getElementById('estimatedFiles').textContent;

                filesList.innerHTML = '';
                filesCount.textContent = estimatedFiles;

                for (let i = 1; i <= estimatedFiles; i++) {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'flex items-center justify-between p-2 bg-white rounded border';
                    fileItem.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm text-gray-900">part_${i}.xlsx</span>
                        </div>
                        <button class="text-blue-600 hover:text-blue-800 text-sm">Скачать</button>
                    `;
                    filesList.appendChild(fileItem);
                }
            }

            removeFile() {
                this.currentFile = null;
                document.getElementById('fileInput').value = '';
                document.getElementById('uploadContent').classList.remove('hidden');
                document.getElementById('filePreview').classList.add('hidden');
                document.getElementById('splitMethods').classList.add('hidden');
                document.getElementById('splitResult').classList.add('hidden');
            }

            resetSplitter() {
                this.removeFile();
                document.querySelectorAll('.split-option').forEach((opt, index) => {
                    if (index === 0) {
                        opt.classList.add('active');
                    } else {
                        opt.classList.remove('active');
                    }
                });
                this.currentMethod = 'rows';
                this.toggleMethodSettings();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            window.tableSplitter = new TableSplitter();
        });
    </script>
</body>
</html>