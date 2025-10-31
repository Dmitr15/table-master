<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.6;
        }

        .wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content {
            flex: 1 1 auto;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        a:hover {
            color: #3f4949;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 1rem;
            justify-content: center;
        }

        header {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #171718;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        nav li {
            margin: 0;
        }

        .btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #1f2937;
            font-size: 1rem;
        }

        .btn:hover {
            color: #4f46e5;
        }

        .main {
            display: flex;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        @media (min-width: 768px) {
            .main {
                grid-template-columns: 2fr 1fr;
            }
        }

        article {
            background: #fff;
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            margin-bottom: 14px;
            position: relative;
        }

        article h2 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        article p.meta {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        footer {
            background: #fff;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
            padding: 1rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .article__row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .row__btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .row__btn a,
        .row__btn button {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            color: #080808;
            transition: background 0.2s ease;
            background: none;
            font-family: inherit;
        }

        .view a:hover,
        .view button:hover {
            background-color: #648ce4;
        }

        .delete a:hover,
        .delete button:hover {
            background-color: #d47474;
        }

        .download a:hover,
        .download button:hover {
            background-color: #6fb19c;
        }

        .xlsxToXls a:hover,
        .xlsxToXls button:hover {
            background-color: #e0c97b;
        }

        .xlsToXlsx a:hover,
        .xlsToXlsx button:hover {
            background-color: #7bb8e0;
        }

        .excelToOds a:hover,
        .excelToOds button:hover {
            background-color: #a37be0;
        }

        .excelToCsv a:hover,
        .excelToCsv button:hover {
            background-color: #7be0b3;
        }

        .excelToHtml a:hover,
        .excelToHtml button:hover {
            background-color: #e07bb8;
        }

        /* Стили для форм */
        .delete-form {
            display: inline;
            margin: 0;
            padding: 0;
        }

        /* Стили для индикаторов загрузки и статуса */
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .conversion-info {
            margin-top: 0.5rem;
        }

        .conversion-status {
            font-size: 0.875rem;
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 500;
        }

        .status-processing {
            background-color: #fef3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-completed {
            background-color: #d1edff;
            color: #004085;
            border: 1px solid #b3d7ff;
        }

        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .action-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .convert-btn {
            position: relative;
            transition: all 0.3s ease;
        }

        .file-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .download-link {
            color: #2563eb;
            text-decoration: underline;
            font-weight: 500;
        }

        .download-link:hover {
            color: #1d4ed8;
        }

        /* Адаптивность для мобильных устройств */
        @media (max-width: 768px) {
            .article__row {
                flex-direction: column;
                align-items: flex-start;
            }

            .row__btn {
                justify-content: flex-start;
                width: 100%;
            }

            header .container {
                flex-direction: column;
                gap: 1rem;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        .merge input[type="file"] {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            cursor: pointer;
        }

        .merge input[type="file"]:hover {
            background: #f0f0f0;
        }

        .merge label {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.8rem;
            color: #666;
            cursor: pointer;
        }

        .download-form {
            margin-top: 10px;
            font-family: sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.6;
            justify-content: center;
            align-items: center;
            display: flex;
        }

        .download-form form {
            background: #fff;
            padding: 1.5rem 2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .upload label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .upload input[type="file"] {
            /* display: flex; */
            /* margin-bottom: 1rem; */
            justify-content: center;
            align-items: center;
        }

        .file-name {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 1rem;
        }

        .error {
            color: #c00;
            font-size: 0.9rem;
            margin-top: -0.5rem;
            margin-bottom: 1rem;
        }

        .download-form button {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            background: #007acc;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .download-form button:hover {
            background: #005f99;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="content">
            <header>
                <div class="container">
                    <h1><a href="#">Table Master</a></h1>
                    <nav>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">About</a></li>
                            <li><a href="#">Blog</a></li>
                            @auth
                                <span>Hi there, {{Auth::user()->name}}</span>
                                <form action="{{route('logout')}}" method="post" style="margin:0;">
                                    @csrf
                                    <button class="btn" onclick="event.preventDefault(); this.closest('form').submit();">Log
                                        Out</button>
                                </form>
                            @endauth
                        </ul>
                    </nav>
                </div>
            </header>

            <div class="container main">
                <main class="posts">
                    @foreach ($files as $file)
                        <article id="file-{{$file->id}}">
                            <h2>{{$file->original_name}}</h2>
                            <div class="article__row">
                                <div class="time_posted">
                                    <p class="meta">Loaded on {{$file->created_at}}</p>
                                    <!-- Контейнер для статуса конвертации -->
                                    <div class="conversion-info" id="conversion-info-{{$file->id}}"></div>
                                </div>
                                <div class="row__btn">
                                    <div class="view">
                                        <a href="{{route('files.show', $file->id)}}">View</a>
                                    </div>
                                    <div class="delete">
                                        <form action="{{route('files.destroy', $file->id)}}" method="post"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn"
                                                onclick="return confirm('Are you sure you want to delete this file?')">Delete</button>
                                        </form>
                                    </div>
                                    <div class="download">
                                        <form action="{{route('download', $file->id)}}" method="post">
                                            @csrf
                                            <button type="submit" class="action-btn">Download</button>
                                        </form>
                                    </div>
                                    <div class="xlsxToXls">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="xlsxToXls">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Convert to xls</button>
                                        </form>
                                    </div>
                                    <div class="xlsToXlsx">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="xlsToXlsx">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Convert to xlsx</button>
                                        </form>
                                    </div>
                                    <div class="excelToOds">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="excelToOds">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Convert to ods</button>
                                        </form>
                                    </div>
                                    <div class="excelToCsv">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="excelToCsv">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Convert to csv</button>
                                        </form>
                                    </div>
                                    <div class="excelToHtml">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="excelToHtml">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Convert to html</button>
                                        </form>
                                    </div>
                                    <div class="split">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="split">
                                            @csrf
                                            <button type="submit" class="action-btn convert-btn">Split</button>
                                        </form>
                                    </div>
                                    <div class="merge">
                                        <form class="conversion-form" data-file-id="{{$file->id}}"
                                            data-conversion-type="merge" data-use-formdata="true">
                                            @csrf
                                            <input type="file" name="merge_file" id="merge_file_{{ $file->id }}"
                                                accept=".xls,.xlsx" required>
                                            <label for="merge_file_{{ $file->id }}">Choose file for merging</label>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </main>
            </div>
            <div class="download-form">
                <form action="{{route('files.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="upload">
                        <label for="xls_file">Choose file</label>
                        <input type="file" name="xls_file" id="xls_file">
                        <p class="file-name" id="file-name">No file selected</p>
                        @error('body')
                            <p class="error">{{$message}}</p>
                        @enderror
                    </div>
                    <button type="submit">Upload</button>
                </form>
            </div>
        </div>
        <footer>
            © 2025 Table Master. All rights reserved.
        </footer>
    </div>

    <script>

        // document.addEventListener('DOMContentLoaded', function () {
        //     // Обработчик для всех форм конвертации
        //     document.querySelectorAll('.conversion-form').forEach(form => {
        //         form.addEventListener('submit', function (e) {
        //             e.preventDefault();

        //             const fileId = this.dataset.fileId;
        //             const conversionType = this.dataset.conversionType;
        //             const button = this.querySelector('.convert-btn');
        //             const conversionInfo = document.getElementById(`conversion-info-${fileId}`);

        //             // Показываем индикатор загрузки
        //             button.disabled = true;
        //             button.innerHTML = 'Converting... <span class="loading"></span>';

        //             // Показываем статус
        //             conversionInfo.innerHTML = '<span class="conversion-status status-processing">Conversion in progress...</span>';

        //             // Отправляем AJAX запрос
        //             fetch(getConversionUrl(conversionType, fileId), {
        //                 method: 'POST',
        //                 headers: {
        //                     'Content-Type': 'application/json',
        //                     'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
        //                     'Accept': 'application/json'
        //                 },
        //                 body: JSON.stringify({})
        //             })
        //                 .then(response => {
        //                     if (!response.ok) {
        //                         throw new Error('Network response was not ok');
        //                     }
        //                     return response.json();
        //                 })
        //                 .then(data => {
        //                     if (data.success) {
        //                         // Запускаем проверку статуса
        //                         checkConversionStatus(fileId, conversionInfo, button, conversionType);
        //                     } else {
        //                         throw new Error(data.message || 'Conversion failed');
        //                     }
        //                 })
        //                 .catch(error => {
        //                     console.error('Error:', error);
        //                     button.disabled = false;
        //                     button.textContent = getButtonText(conversionType);
        //                     conversionInfo.innerHTML = '<span class="conversion-status status-failed">Conversion failed: ' + error.message + '</span>';

        //                     // Через 5 секунд убираем сообщение об ошибке
        //                     setTimeout(() => {
        //                         conversionInfo.innerHTML = '';
        //                     }, 5000);
        //                 });
        //         });
        //     });

        //     // Функция для проверки статуса конвертации
        //     function checkConversionStatus(fileId, conversionInfo, button, conversionType) {
        //         let attempts = 0;
        //         const maxAttempts = 60; // Максимум 2 минуты (60 попыток * 2 секунды)

        //         const checkInterval = setInterval(() => {
        //             attempts++;

        //             fetch(`/convert/check/${fileId}`)
        //                 .then(response => {
        //                     if (!response.ok) {
        //                         throw new Error('Status check failed');
        //                     }
        //                     return response.json();
        //                 })
        //                 .then(data => {
        //                     if (data.status === 'completed') {
        //                         clearInterval(checkInterval);
        //                         button.disabled = false;
        //                         button.textContent = getButtonText(conversionType);
        //                         conversionInfo.innerHTML = `
        //                             <div class="file-status">
        //                                 <span class="conversion-status status-completed">Conversion completed!</span>
        //                                 <a href="${data.file}" class="download-link">Download converted file</a>
        //                             </div>
        //                         `;

        //                         // Через 10 секунд убираем сообщение об успехе
        //                         setTimeout(() => {
        //                             conversionInfo.innerHTML = '';
        //                         }, 10000);
        //                     } else if (data.status === 'failed') {
        //                         clearInterval(checkInterval);
        //                         button.disabled = false;
        //                         button.textContent = getButtonText(conversionType);
        //                         conversionInfo.innerHTML = '<span class="conversion-status status-failed">Conversion failed</span>';

        //                         setTimeout(() => {
        //                             conversionInfo.innerHTML = '';
        //                         }, 5000);
        //                     } else if (attempts >= maxAttempts) {
        //                         // Превышено максимальное время ожидания
        //                         clearInterval(checkInterval);
        //                         button.disabled = false;
        //                         button.textContent = getButtonText(conversionType);
        //                         conversionInfo.innerHTML = '<span class="conversion-status status-failed">Conversion timeout</span>';

        //                         setTimeout(() => {
        //                             conversionInfo.innerHTML = '';
        //                         }, 5000);
        //                     }
        //                     // Если статус 'pending' или 'processing', продолжаем ждать
        //                 })
        //                 .catch(error => {
        //                     console.error('Error checking status:', error);
        //                     clearInterval(checkInterval);
        //                     button.disabled = false;
        //                     button.textContent = getButtonText(conversionType);
        //                     conversionInfo.innerHTML = '<span class="conversion-status status-failed">Status check failed</span>';

        //                     setTimeout(() => {
        //                         conversionInfo.innerHTML = '';
        //                     }, 5000);
        //                 });
        //         }, 2000); // Проверяем каждые 2 секунды
        //     }

        //     // НОВАЯ ФУНКЦИЯ: Автоматическое скачивание
        //     function triggerAutoDownload(downloadUrl, fileId, conversionInfo) {
        //         // Создаем невидимый iframe для скачивания
        //         const iframe = document.createElement('iframe');
        //         iframe.style.display = 'none';
        //         iframe.src = downloadUrl;

        //         // Добавляем iframe на страницу
        //         document.body.appendChild(iframe);

        //         // Убираем iframe после загрузки
        //         iframe.onload = function () {
        //             setTimeout(() => {
        //                 document.body.removeChild(iframe);

        //                 // Обновляем сообщение после скачивания
        //                 conversionInfo.innerHTML = `
        //             <div class="file-status">
        //                 <span class="conversion-status status-completed">Download completed!</span>
        //                 <a href="${downloadUrl}" class="download-link">Download again</a>
        //             </div>
        //             `;

        //                 // Через 10 секунд убираем сообщение
        //                 setTimeout(() => {
        //                     conversionInfo.innerHTML = '';
        //                 }, 10000);
        //             }, 1000);
        //         };

        //         // Альтернативный метод через создание ссылки (на случай если iframe не работает)
        //         setTimeout(() => {
        //             const link = document.createElement('a');
        //             link.href = downloadUrl;
        //             link.download = '';
        //             link.style.display = 'none';
        //             document.body.appendChild(link);
        //             link.click();
        //             document.body.removeChild(link);
        //         }, 500);
        //     }

        //     // Дополнительная функция для универсального скачивания
        //     function universalDownload(url, filename) {
        //         // Пытаемся использовать современный API
        //         if (typeof window.showSaveFilePicker === 'function') {
        //             fetch(url)
        //                 .then(response => response.blob())
        //                 .then(blob => {
        //                     const fileHandle = window.showSaveFilePicker({
        //                         suggestedName: filename,
        //                         types: [{
        //                             description: 'Excel files',
        //                             accept: { 'application/vnd.ms-excel': ['.xls'] },
        //                         }],
        //                     });
        //                     return fileHandle.then(handle => {
        //                         const writable = handle.createWritable();
        //                         return writable.write(blob).then(() => writable.close());
        //                     });
        //                 })
        //                 .catch(err => {
        //                     console.log('Modern API failed, falling back to legacy method', err);
        //                     legacyDownload(url);
        //                 });
        //         } else {
        //             legacyDownload(url);
        //         }
        //     }

        //     function legacyDownload(url) {
        //         // Стандартный метод создания ссылки
        //         const link = document.createElement('a');
        //         link.href = url;
        //         link.download = '';
        //         link.target = '_blank';
        //         document.body.appendChild(link);
        //         link.click();
        //         document.body.removeChild(link);
        //     }

        //     // Вспомогательные функции
        //     function getConversionUrl(conversionType, fileId) {
        //         const routes = {
        //             'xlsxToXls': '/file/' + fileId + '/xlsxToXls',
        //             'xlsToXlsx': '/file/' + fileId + '/xlsToXlsx',
        //             'excelToOds': '/file/' + fileId + '/excelToOds',
        //             'excelToCsv': '/file/' + fileId + '/excelToCsv',
        //             'excelToHtml': '/file/' + fileId + '/excelToHtml',
        //             'split': '/file/' + fileId + '/split',
        //             'merge': '/file/' + fileId + '/merge',
        //         };
        //         return routes[conversionType];
        //     }

        //     function getDownloadFileName(conversionType) {
        //         const extensions = {
        //             'xlsxToXls': 'converted.xls',
        //             'xlsToXlsx': 'converted.xlsx',
        //             'excelToOds': 'converted.ods',
        //             'excelToCsv': 'converted.csv',
        //             'excelToHtml': 'converted.html',
        //             'split': 'split',
        //             'merge': 'merge',
        //         };
        //         return extensions[conversionType] || 'converted_file';
        //     }

        //     function getButtonText(conversionType) {
        //         const texts = {
        //             'xlsxToXls': 'Convert to xls',
        //             'xlsToXlsx': 'Convert to xlsx',
        //             'excelToOds': 'Convert to ods',
        //             'excelToCsv': 'Convert to csv',
        //             'excelToHtml': 'Convert to html',
        //             'split': 'split',
        //             'merge': 'merge',
        //         };
        //         return texts[conversionType];
        //     }
        // });
        document.addEventListener('DOMContentLoaded', function () {
            // Обработчик для всех форм конвертации (кроме merge)
            document.querySelectorAll('.conversion-form:not([data-conversion-type="merge"])').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    handleConversion(this);
                });
            });

            // Специальный обработчик для input файлов в merge формах
            document.querySelectorAll('.conversion-form[data-conversion-type="merge"] input[type="file"]').forEach(input => {
                input.addEventListener('change', function (e) {
                    if (this.files.length > 0) {
                        const form = this.closest('.conversion-form');
                        handleConversion(form);
                    }
                });
            });

            // Общая функция обработки конвертации
            function handleConversion(form) {
                const fileId = form.dataset.fileId;
                const conversionType = form.dataset.conversionType;
                const useFormData = form.dataset.useFormdata === 'true';
                const button = form.querySelector('.convert-btn');
                const conversionInfo = document.getElementById(`conversion-info-${fileId}`);

                // Для merge показываем статус сразу при выборе файла
                if (conversionType === 'merge') {
                    conversionInfo.innerHTML = '<span class="conversion-status status-processing">Preparing to merge...</span>';
                }

                // Подготовка данных для отправки
                let fetchOptions;

                if (useFormData) {
                    // Для операций с загрузкой файлов (merge)
                    const formData = new FormData(form);

                    fetchOptions = {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    };
                } else {
                    // Для обычных операций конвертации
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = 'Converting... <span class="loading"></span>';
                    }

                    conversionInfo.innerHTML = '<span class="conversion-status status-processing">Conversion in progress...</span>';

                    fetchOptions = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
                    };
                }

                // Отправляем AJAX запрос
                fetch(getConversionUrl(conversionType, fileId), fetchOptions)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Запускаем проверку статуса
                            checkConversionStatus(fileId, conversionInfo, button, conversionType, form);
                        } else {
                            throw new Error(data.message || 'Operation failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (button) {
                            button.disabled = false;
                            button.textContent = getButtonText(conversionType);
                        }
                        conversionInfo.innerHTML = '<span class="conversion-status status-failed">Operation failed: ' + error.message + '</span>';

                        // Через 5 секунд убираем сообщение об ошибке
                        setTimeout(() => {
                            conversionInfo.innerHTML = '';
                        }, 5000);
                    });
            }

            // Обновленная функция проверки статуса
            function checkConversionStatus(fileId, conversionInfo, button, conversionType, form) {
                let attempts = 0;
                const maxAttempts = 60;

                const checkInterval = setInterval(() => {
                    attempts++;

                    fetch(`/convert/check/${fileId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Status check failed');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'completed') {
                                clearInterval(checkInterval);
                                if (button) {
                                    button.disabled = false;
                                    button.textContent = getButtonText(conversionType);
                                }

                                // Для merge очищаем поле выбора файла после успешного завершения
                                if (conversionType === 'merge' && form) {
                                    form.reset();
                                }

                                conversionInfo.innerHTML = `
                            <div class="file-status">
                                <span class="conversion-status status-completed">Operation completed!</span>
                                <a href="${data.file}" class="download-link">Download ${conversionType === 'merge' ? 'merged' : 'converted'} file</a>
                            </div>
                        `;

                                // Через 10 секунд убираем сообщение об успехе
                                setTimeout(() => {
                                    conversionInfo.innerHTML = '';
                                }, 10000);
                            } else if (data.status === 'failed') {
                                clearInterval(checkInterval);
                                if (button) {
                                    button.disabled = false;
                                    button.textContent = getButtonText(conversionType);
                                }
                                conversionInfo.innerHTML = '<span class="conversion-status status-failed">Operation failed</span>';

                                setTimeout(() => {
                                    conversionInfo.innerHTML = '';
                                }, 5000);
                            } else if (attempts >= maxAttempts) {
                                clearInterval(checkInterval);
                                if (button) {
                                    button.disabled = false;
                                    button.textContent = getButtonText(conversionType);
                                }
                                conversionInfo.innerHTML = '<span class="conversion-status status-failed">Operation timeout</span>';

                                setTimeout(() => {
                                    conversionInfo.innerHTML = '';
                                }, 5000);
                            }
                        })
                        .catch(error => {
                            console.error('Error checking status:', error);
                            clearInterval(checkInterval);
                            if (button) {
                                button.disabled = false;
                                button.textContent = getButtonText(conversionType);
                            }
                            conversionInfo.innerHTML = '<span class="conversion-status status-failed">Status check failed</span>';

                            setTimeout(() => {
                                conversionInfo.innerHTML = '';
                            }, 5000);
                        });
                }, 2000);
            }

            // Вспомогательные функции (остаются без изменений)
            function getConversionUrl(conversionType, fileId) {
                const routes = {
                    'xlsxToXls': '/file/' + fileId + '/xlsxToXls',
                    'xlsToXlsx': '/file/' + fileId + '/xlsToXlsx',
                    'excelToOds': '/file/' + fileId + '/excelToOds',
                    'excelToCsv': '/file/' + fileId + '/excelToCsv',
                    'excelToHtml': '/file/' + fileId + '/excelToHtml',
                    'split': '/file/' + fileId + '/split',
                    'merge': '/file/' + fileId + '/merge',
                };
                return routes[conversionType];
            }

            function getButtonText(conversionType) {
                const texts = {
                    'xlsxToXls': 'Convert to xls',
                    'xlsToXlsx': 'Convert to xlsx',
                    'excelToOds': 'Convert to ods',
                    'excelToCsv': 'Convert to csv',
                    'excelToHtml': 'Convert to html',
                    'split': 'Split',
                    'merge': 'Merge',
                };
                return texts[conversionType];
            }
        });

    </script>
</body>

</html>