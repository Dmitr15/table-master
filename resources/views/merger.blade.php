<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Слияние таблиц - Table Master</title>
    
    <style>
        /* Базовые стили (такие же как в analyzer) */
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
        .bg-green-50 { background-color: #f0fdf4; }
        .bg-blue-50 { background-color: #eff6ff; }
        .bg-green-100 { background-color: #dcfce7; }
        
        .text-white { color: white; }
        .text-gray-900 { color: #111827; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-green-600 { color: #059669; }
        .text-blue-600 { color: #2563eb; }
        .text-red-500 { color: #ef4444; }
        
        .border { border-width: 1px; }
        .border-b { border-bottom-width: 1px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-300 { border-color: #d1d5db; }
        
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-full { border-radius: 9999px; }
        
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
        
        .btn-success {
            background-color: #10b981;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 0.875rem;
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
        
        .file-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        
        .merge-preview {
            background: #1f2937;
            color: #d1d5db;
            border-radius: 0.5rem;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            max-height: 200px;
            overflow: auto;
        }
        
        @media (min-width: 768px) {
            .md\:flex { display: flex; }
            .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
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
                        <a href="{{ route('merger') }}" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm font-medium">Слияние</a>
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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Слияние таблиц</h1>
                <p class="text-lg text-gray-600">Объедините несколько таблиц в один файл</p>
            </div>

            <!-- Основная карточка -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <!-- Загрузка файлов -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Загрузите файлы для слияния</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Первый файл -->
                        <div id="file1DropZone" class="file-upload-area p-6 text-center cursor-pointer">
                            <input type="file" id="file1Input" accept=".xlsx,.xls,.csv" class="hidden">
                            <div id="file1UploadContent">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="font-medium text-gray-600 mb-2">Файл 1</p>
                                <p class="text-sm text-gray-500">Перетащите первый файл</p>
                            </div>
                            <div id="file1Preview" class="hidden">
                                <div class="file-item flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <p id="file1Name" class="font-medium text-gray-900 text-sm"></p>
                                            <p id="file1Size" class="text-xs text-gray-500"></p>
                                        </div>
                                    </div>
                                    <button type="button" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Второй файл -->
                        <div id="file2DropZone" class="file-upload-area p-6 text-center cursor-pointer">
                            <input type="file" id="file2Input" accept=".xlsx,.xls,.csv" class="hidden">
                            <div id="file2UploadContent">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="font-medium text-gray-600 mb-2">Файл 2</p>
                                <p class="text-sm text-gray-500">Перетащите второй файл</p>
                            </div>
                            <div id="file2Preview" class="hidden">
                                <div class="file-item flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <p id="file2Name" class="font-medium text-gray-900 text-sm"></p>
                                            <p id="file2Size" class="text-xs text-gray-500"></p>
                                        </div>
                                    </div>
                                    <button type="button" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка добавления файлов -->
                    <div class="text-center mt-4">
                        <button id="addFileBtn" class="btn-secondary text-sm">
                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Добавить еще файл
                        </button>
                    </div>
                </div>

                <!-- Настройки слияния -->
                <div id="mergeSettings" class="hidden space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900">Настройки слияния</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Метод слияния</label>
                            <select id="mergeMethod" class="w-full p-2 border border-gray-300 rounded-lg">
                                <option value="vertical">Вертикально (добавление строк)</option>
                                <option value="horizontal">Горизонтально (добавление столбцов)</option>
                                <option value="join">Объединение по ключевому столбцу</option>
                            </select>
                        </div>
                        
                        <div id="joinColumnSection" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ключевой столбец</label>
                            <select id="joinColumn" class="w-full p-2 border border-gray-300 rounded-lg">
                                <option value="">Выберите столбец</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">Предпросмотр слияния</h4>
                        <div class="merge-preview text-sm">
                            // Загрузите файлы для предпросмотра
                            // Столбец1 | Столбец2 | Столбец3
                            // Значение1 | Значение2 | Значение3
                            // Значение4 | Значение5 | Значение6
                        </div>
                    </div>

                    <!-- Кнопка слияния -->
                    <div class="flex justify-end">
                        <button id="mergeBtn" class="btn-success">
                            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Объединить таблицы
                        </button>
                    </div>
                </div>

                <!-- Результат -->
                <div id="mergeResult" class="hidden text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Таблицы объединены!</h3>
                    <p class="text-gray-600 mb-6">Файл успешно создан</p>
                    <div class="flex justify-center space-x-4">
                        <a id="downloadResult" href="#" class="btn-success">Скачать результат</a>
                        <button id="newMerge" class="btn-secondary">Новое слияние</button>
                    </div>
                </div>
            </div>

            <!-- Информация -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-green-900">Вертикальное слияние</h4>
                            <p class="text-sm text-green-700">Объединение таблиц по строкам</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-900">Горизонтальное слияние</h4>
                            <p class="text-sm text-blue-700">Объединение таблиц по столбцам</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        class TableMerger {
            constructor() {
                this.files = [];
                this.init();
            }

            init() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                // Файл 1
                document.getElementById('file1DropZone').addEventListener('click', () => {
                    document.getElementById('file1Input').click();
                });
                document.getElementById('file1Input').addEventListener('change', (e) => {
                    this.handleFileSelect(e.target.files[0], 1);
                });

                // Файл 2
                document.getElementById('file2DropZone').addEventListener('click', () => {
                    document.getElementById('file2Input').click();
                });
                document.getElementById('file2Input').addEventListener('change', (e) => {
                    this.handleFileSelect(e.target.files[0], 2);
                });

                // Добавление файлов
                document.getElementById('addFileBtn').addEventListener('click', () => {
                    this.addFileField();
                });

                // Настройки слияния
                document.getElementById('mergeMethod').addEventListener('change', (e) => {
                    this.toggleJoinColumn(e.target.value === 'join');
                });

                // Кнопка слияния
                document.getElementById('mergeBtn').addEventListener('click', () => {
                    this.mergeTables();
                });

                // Новое слияние
                document.getElementById('newMerge').addEventListener('click', () => {
                    this.resetMerger();
                });
            }

            handleFileSelect(file, fileNumber) {
                if (!file) return;

                const isValidType = file.name.endsWith('.xlsx') || 
                    file.name.endsWith('.xls') ||
                    file.name.endsWith('.csv');

                if (!isValidType) {
                    alert('ОШИБКА: Неподдерживаемый формат файла');
                    return;
                }

                this.displayFileInfo(file, fileNumber);
                
                if (this.files.length >= 2) {
                    document.getElementById('mergeSettings').classList.remove('hidden');
                }
            }

            displayFileInfo(file, fileNumber) {
                const fileName = document.getElementById(`file${fileNumber}Name`);
                const fileSize = document.getElementById(`file${fileNumber}Size`);
                const uploadContent = document.getElementById(`file${fileNumber}UploadContent`);
                const filePreview = document.getElementById(`file${fileNumber}Preview`);

                fileName.textContent = file.name;
                fileSize.textContent = this.formatFileSize(file.size);
                uploadContent.classList.add('hidden');
                filePreview.classList.remove('hidden');

                this.files.push({ file, number: fileNumber });
            }

            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            addFileField() {
                // В реальном приложении здесь была бы логика добавления новых полей для файлов
                alert('В демо-версии доступно слияние только двух файлов');
            }

            toggleJoinColumn(show) {
                const section = document.getElementById('joinColumnSection');
                if (show) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
                }
            }

            mergeTables() {
                const mergeBtn = document.getElementById('mergeBtn');
                const originalText = mergeBtn.innerHTML;
                mergeBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';
                mergeBtn.disabled = true;

                // Имитация процесса слияния
                setTimeout(() => {
                    document.getElementById('mergeSettings').classList.add('hidden');
                    document.getElementById('mergeResult').classList.remove('hidden');
                    
                    mergeBtn.innerHTML = originalText;
                    mergeBtn.disabled = false;
                }, 2000);
            }

            resetMerger() {
                this.files = [];
                
                // Сбрасываем все превью файлов
                [1, 2].forEach(num => {
                    document.getElementById(`file${num}UploadContent`).classList.remove('hidden');
                    document.getElementById(`file${num}Preview`).classList.add('hidden');
                    document.getElementById(`file${num}Input`).value = '';
                });
                
                document.getElementById('mergeSettings').classList.add('hidden');
                document.getElementById('mergeResult').classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            window.tableMerger = new TableMerger();
        });
    </script>
</body>
</html>