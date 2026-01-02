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

    // Проверяем незавершенные конвертации при загрузке страницы
    checkPendingConversions();

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

        // Сохраняем информацию о старте конвертации
        const conversionData = {
            fileId: fileId,
            conversionType: conversionType,
            status: 'processing',
            startedAt: new Date().toISOString(),
            buttonText: getButtonText(conversionType)
        };

        saveConversionToStorage(fileId, conversionData);

        // Отправляем AJAX запрос
        fetch(getConversionUrl(conversionType, fileId), fetchOptions)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                // Успешный старт - начинаем отслеживание
                console.log('Conversion started successfully:', data);
                checkConversionStatus(fileId, conversionInfo, button, conversionType, form);
            })
            .catch(error => {
                console.error('Error starting conversion:', error);
                if (button) {
                    button.disabled = false;
                    button.textContent = getButtonText(conversionType);
                }
                conversionInfo.innerHTML = '<span class="conversion-status status-failed">' +
                    (error.message || 'Operation failed') + '</span>';

                // Обновляем статус в хранилище
                updateConversionStatus(fileId, 'failed', error.message);

                setTimeout(() => {
                    conversionInfo.innerHTML = '';
                }, 5000);
            });
    }

    function checkConversionStatus(fileId, conversionInfo, button, conversionType, form) {
        let attempts = 0;
        const maxAttempts = 120;
        let lastStatus = '';

        const checkInterval = setInterval(() => {
            attempts++;

            fetch(`/convert/check/${fileId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }

                    lastStatus = data.status;

                    // Обновляем статус в хранилище
                    updateConversionStatus(fileId, data.status, null, data);

                    if (data.status === 'completed') {
                        clearInterval(checkInterval);

                        if (button) {
                            button.disabled = false;
                            button.textContent = getButtonText(conversionType);
                        }

                        // Для merge очищаем поле выбора файла
                        if (conversionType === 'merge' && form) {
                            form.reset();
                        }

                        let downloadUrl = data.file || data.output_file || data.output_path;
                        if (!downloadUrl) {
                            console.error('No download URL provided:', data);
                            conversionInfo.innerHTML = `
                                    <div class="file-status">
                                        <span class="conversion-status status-completed">Conversion completed!</span>
                                        <span class="conversion-status status-failed" style="margin-left:10px;">Download link missing</span>
                                    </div>
                                `;
                        } else {
                            // Автоматическое скачивание
                            const link = document.createElement('a');
                            link.href = downloadUrl;
                            link.download = '';
                            link.style.display = 'none';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            // Показываем сообщение
                            conversionInfo.innerHTML = `
                                    <div class="file-status">
                                        <span class="conversion-status status-completed">File downloaded successfully!</span>
                                    </div>
                                `;

                            // Сохраняем ссылку для повторного скачивания
                            updateConversionStatus(fileId, 'completed', null, { downloadUrl: downloadUrl });
                        }

                        setTimeout(() => {
                            conversionInfo.innerHTML = '';
                        }, 15000);

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

                    } else if (data.status === 'processing') {
                        // Показываем прогресс
                        if (attempts % 5 === 0) {
                            conversionInfo.innerHTML = `<span class="conversion-status status-processing">Processing...</span>`;
                        }
                    } else if (attempts >= maxAttempts) {
                        clearInterval(checkInterval);
                        if (button) {
                            button.disabled = false;
                            button.textContent = getButtonText(conversionType);
                        }
                        conversionInfo.innerHTML = `<span class="conversion-status status-failed">Operation timeout (last status: ${lastStatus})</span>`;

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
                    conversionInfo.innerHTML = `<span class="conversion-status status-failed">Status check failed: ${error.message}</span>`;

                    setTimeout(() => {
                        conversionInfo.innerHTML = '';
                    }, 5000);
                });
        }, 2000);
    }

    function checkPendingConversions() {
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith('conversion-')) {
                const fileId = key.replace('conversion-', '');
                try {
                    const data = JSON.parse(localStorage.getItem(key));

                    // Если конвертация еще в процессе, проверяем статус
                    if (data.status === 'processing') {
                        const conversionInfo = document.getElementById(`conversion-info-${fileId}`);
                        const form = document.querySelector(`.conversion-form[data-file-id="${fileId}"]`);
                        const button = form ? form.querySelector('.convert-btn') : null;

                        if (conversionInfo) {
                            conversionInfo.innerHTML = '<span class="conversion-status status-processing">Checking previous conversion...</span>';
                            checkConversionStatus(fileId, conversionInfo, button, data.conversionType, form);
                        }
                    }

                } catch (e) {
                    console.error('Error parsing localStorage data:', e);
                    localStorage.removeItem(key);
                }
            }
        }
    }

    function saveConversionToStorage(fileId, data) {
        localStorage.setItem(`conversion-${fileId}`, JSON.stringify(data));
    }

    function updateConversionStatus(fileId, status, error = null, additionalData = {}) {
        try {
            const key = `conversion-${fileId}`;
            const existing = localStorage.getItem(key);
            let data = existing ? JSON.parse(existing) : {};

            data.status = status;
            data.updatedAt = new Date().toISOString();

            if (error) data.error = error;
            if (additionalData.downloadUrl) data.downloadUrl = additionalData.downloadUrl;
            if (status === 'completed') data.completedAt = new Date().toISOString();

            localStorage.setItem(key, JSON.stringify(data));
        } catch (e) {
            console.error('Error updating conversion status:', e);
        }
    }

    // Вспомогательные функции
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
        return texts[conversionType] || 'Convert';
    }
});
