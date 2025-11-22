// Импорты
import './bootstrap';
import 'flowbite';

// Импорт сервисов
import conversionService from './services/conversionService';
import mergeService from './services/mergeService';
import splitService from './services/splitService';
import analyzeService from './services/analyzeService';
import notificationService from './services/notificationService';

// Основной класс приложения Table Master
class TableMasterApp {
    constructor() {
        this.services = {
            conversion: conversionService,
            merge: mergeService,
            split: splitService,
            analyze: analyzeService,
            notification: notificationService
        };
        this.init();
    }

    init() {
        this.initEventListeners();
        this.initComponents();
        this.initFileHandling();
        console.log('Table Master App initialized');
    }

    // Инициализация обработчиков событий
    initEventListeners() {
        // Глобальные обработчики
        document.addEventListener('DOMContentLoaded', () => {
            this.handleNavigation();
            this.initTooltips();
            this.initModals();
        });

        // Глобальный обработчик ошибок
        window.addEventListener('error', (event) => {
            console.error('Global error:', event.error);
            this.services.notification.error('Произошла непредвиденная ошибка');
        });
    }

    // Инициализация компонентов
    initComponents() {
        this.initFileUploaders();
        this.initPageSpecificHandlers();
    }

    // Инициализация обработчиков для конкретных страниц
    initPageSpecificHandlers() {
        const path = window.location.pathname;
        
        switch(path) {
            case '/converter':
                this.initConverterHandlers();
                break;
            case '/merger':
                this.initMergerHandlers();
                break;
            case '/splitter':
                this.initSplitterHandlers();
                break;
            case '/analyzer':
                this.initAnalyzerHandlers();
                break;
        }
    }

    // Обработчики для конвертера
    initConverterHandlers() {
        const form = document.getElementById('converterForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleConversion(e));
        }

        // Обработчик выбора формата для предпросмотра
        const formatSelect = document.getElementById('outputFormat');
        if (formatSelect) {
            formatSelect.addEventListener('change', () => this.updateConversionPreview());
        }
    }

    // Обработчики для слияния
    initMergerHandlers() {
        const form = document.getElementById('mergeForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleMerge(e));
        }

        // Обработчик изменения метода слияния
        const methodSelect = document.getElementById('mergeMethod');
        if (methodSelect) {
            methodSelect.addEventListener('change', (e) => this.toggleMergeSettings(e.target.value));
        }
    }

    // Обработчики для разделения
    initSplitterHandlers() {
        const form = document.getElementById('splitForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSplit(e));
        }

        // Обработчик изменения метода разделения
        const methodOptions = document.querySelectorAll('.split-option');
        methodOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                const method = e.currentTarget.dataset.method;
                this.toggleSplitSettings(method);
            });
        });
    }

    // Обработчики для анализа
    initAnalyzerHandlers() {
        const analyzeBtn = document.getElementById('analyzeBtn');
        if (analyzeBtn) {
            analyzeBtn.addEventListener('click', () => this.handleAnalysis());
        }

        // Обработчик переключения вкладок
        const tabButtons = document.querySelectorAll('.tab-btn');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tabName = e.currentTarget.dataset.tab;
                this.switchAnalysisTab(tabName);
            });
        });
    }

    // === КОНВЕРТЕР ===
    async handleConversion(e) {
        e.preventDefault();
        
        const form = e.target;
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        
        if (!file) {
            this.services.notification.error('Пожалуйста, выберите файл');
            return;
        }

        const format = document.getElementById('outputFormat').value;
        if (!format) {
            this.services.notification.error('Пожалуйста, выберите формат');
            return;
        }

        const includeHeaders = document.querySelector('input[name="include_headers"]')?.checked ?? true;
        const prettyPrint = document.querySelector('input[name="pretty_print"]')?.checked ?? false;

        const convertBtn = form.querySelector('button[type="submit"]');
        const originalText = convertBtn.innerHTML;

        try {
            convertBtn.disabled = true;
            convertBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';

            const result = await this.services.conversion.convertFile(file, format, {
                includeHeaders,
                prettyPrint
            });

            if (result.success) {
                this.downloadFile(result.blob, result.filename);
                this.showConversionResult();
                this.services.notification.success('Файл успешно сконвертирован!');
            } else {
                this.services.notification.error(result.error);
            }

        } catch (error) {
            this.services.notification.error('Ошибка при конвертации: ' + error.message);
        } finally {
            convertBtn.disabled = false;
            convertBtn.innerHTML = originalText;
        }
    }

    // === СЛИЯНИЕ ===
    async handleMerge(e) {
        e.preventDefault();
        
        const fileInputs = [document.getElementById('file1Input'), document.getElementById('file2Input')];
        const files = fileInputs.map(input => input.files[0]).filter(Boolean);
        
        if (files.length < 2) {
            this.services.notification.error('Пожалуйста, выберите как минимум 2 файла');
            return;
        }

        const method = document.getElementById('mergeMethod').value;
        const joinColumn = document.getElementById('joinColumn')?.value;
        const includeHeaders = document.querySelector('input[name="include_headers"]')?.checked ?? true;

        const mergeBtn = e.target.querySelector('button[type="submit"]');
        const originalText = mergeBtn.innerHTML;

        try {
            mergeBtn.disabled = true;
            mergeBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';

            const result = await this.services.merge.mergeFiles(files, method, {
                joinColumn,
                includeHeaders
            });

            if (result.success) {
                this.downloadFile(result.blob, result.filename);
                this.showMergeResult();
                this.services.notification.success('Файлы успешно объединены!');
            } else {
                this.services.notification.error(result.error);
            }

        } catch (error) {
            this.services.notification.error('Ошибка при слиянии: ' + error.message);
        } finally {
            mergeBtn.disabled = false;
            mergeBtn.innerHTML = originalText;
        }
    }

    // === РАЗДЕЛЕНИЕ ===
    async handleSplit(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        
        if (!file) {
            this.services.notification.error('Пожалуйста, выберите файл');
            return;
        }

        const method = this.currentSplitMethod || 'rows';
        const rowsPerFile = document.getElementById('rowsPerFile')?.value;
        const splitColumn = document.getElementById('splitColumn')?.value;
        const includeHeaders = document.querySelector('input[name="include_headers"]')?.checked ?? true;

        const splitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = splitBtn.innerHTML;

        try {
            splitBtn.disabled = true;
            splitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';

            const result = await this.services.split.splitFile(file, method, {
                rowsPerFile,
                splitColumn,
                includeHeaders
            });

            if (result.success) {
                this.downloadFile(result.blob, result.filename);
                this.showSplitResult();
                this.services.notification.success('Файл успешно разделен!');
            } else {
                this.services.notification.error(result.error);
            }

        } catch (error) {
            this.services.notification.error('Ошибка при разделении: ' + error.message);
        } finally {
            splitBtn.disabled = false;
            splitBtn.innerHTML = originalText;
        }
    }

    // === АНАЛИЗ ===
    async handleAnalysis() {
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        
        if (!file) {
            this.services.notification.error('Пожалуйста, выберите файл');
            return;
        }

        const analysisType = document.getElementById('reportType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const showTrends = document.getElementById('showTrends')?.checked ?? true;

        const analyzeBtn = document.getElementById('analyzeBtn');
        const originalText = analyzeBtn.innerHTML;

        try {
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';

            const result = await this.services.analyze.analyzeData(file, analysisType, {
                startDate,
                endDate,
                includeCharts: showTrends
            });

            if (result.success) {
                this.displayAnalysisResults(result.data);
                this.services.notification.success('Анализ данных завершен!');
            } else {
                this.services.notification.error(result.error);
            }

        } catch (error) {
            this.services.notification.error('Ошибка при анализе: ' + error.message);
        } finally {
            analyzeBtn.disabled = false;
            analyzeBtn.innerHTML = originalText;
        }
    }

    // Вспомогательные методы
    downloadFile(blob, filename) {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    showConversionResult() {
        const form = document.getElementById('converterForm');
        const result = document.getElementById('conversionResult');
        if (form && result) {
            form.classList.add('hidden');
            result.classList.remove('hidden');
        }
    }

    showMergeResult() {
        const form = document.getElementById('mergeForm');
        const result = document.getElementById('mergeResult');
        if (form && result) {
            form.classList.add('hidden');
            result.classList.remove('hidden');
        }
    }

    showSplitResult() {
        const form = document.getElementById('splitForm');
        const result = document.getElementById('splitResult');
        if (form && result) {
            form.classList.add('hidden');
            result.classList.remove('hidden');
        }
    }

    displayAnalysisResults(data) {
        document.getElementById('analysisResults').classList.remove('hidden');
        // Здесь будет логика отображения результатов анализа
        this.updateMetrics(data.metrics);
        this.renderCharts(data.charts);
        this.populateDataTable(data.tableData);
    }

    updateMetrics(metrics) {
        if (metrics) {
            document.getElementById('totalIncome').textContent = `₽ ${this.formatNumber(metrics.totalIncome)}`;
            document.getElementById('totalExpenses').textContent = `₽ ${this.formatNumber(metrics.totalExpenses)}`;
            document.getElementById('netProfit').textContent = `₽ ${this.formatNumber(metrics.netProfit)}`;
        }
    }

    formatNumber(num) {
        return num?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') || '0';
    }

    // ... остальные методы (initFileHandling, initFileUploaders, handleNavigation и т.д.) остаются без изменений
    initFileHandling() {
        this.uploadedFiles = new Map();
        this.initDragAndDrop();
    }

    initDragAndDrop() {
        const dropZones = document.querySelectorAll('[data-drop-zone]');
        
        dropZones.forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('drag-over');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('drag-over');
            });

            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('drag-over');
                this.handleDroppedFiles(e.dataTransfer.files, zone);
            });
        });
    }

    handleDroppedFiles(files, dropZone) {
        Array.from(files).forEach(file => {
            if (this.isValidFileType(file)) {
                this.processFile(file, dropZone);
            } else {
                this.services.notification.error(`Неподдерживаемый формат файла: ${file.name}`);
            }
        });
    }

    isValidFileType(file) {
        const validTypes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
            'application/json'
        ];
        
        return validTypes.includes(file.type) || 
               file.name.endsWith('.xlsx') || 
               file.name.endsWith('.xls') ||
               file.name.endsWith('.csv') ||
               file.name.endsWith('.json');
    }

    processFile(file, targetElement) {
        const fileId = Date.now().toString();
        const fileObject = {
            id: fileId,
            name: file.name,
            size: file.size,
            type: file.type,
            file: file
        };

        this.uploadedFiles.set(fileId, fileObject);
        this.showFilePreview(fileObject, targetElement);
        this.services.notification.success(`Файл "${file.name}" успешно загружен`);
    }

    showFilePreview(fileObject, container) {
        const previewHtml = `
            <div class="file-item" data-file-id="${fileObject.id}">
                <div class="flex items-center space-x-3">
                    <div class="file-icon">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="file-info">
                        <div class="file-name font-medium">${fileObject.name}</div>
                        <div class="file-size text-sm text-gray-500">${this.formatFileSize(fileObject.size)}</div>
                    </div>
                </div>
                <button type="button" class="file-remove text-red-600 hover:text-red-800" data-remove-file="${fileObject.id}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        if (container) {
            container.insertAdjacentHTML('beforeend', previewHtml);
            
            container.querySelector(`[data-remove-file="${fileObject.id}"]`).addEventListener('click', () => {
                this.removeFile(fileObject.id);
            });
        }
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    removeFile(fileId) {
        this.uploadedFiles.delete(fileId);
        const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
        if (fileElement) {
            fileElement.remove();
        }
    }

    handleNavigation() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('[data-nav-link]');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    }

    initFileUploaders() {
        const fileInputs = document.querySelectorAll('input[type="file"][data-file-upload]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                Array.from(e.target.files).forEach(file => {
                    this.processFile(file, e.target.closest('[data-file-container]'));
                });
            });
        });
    }

    initTooltips() {
        // Базовая реализация tooltip
    }

    initModals() {
        // Базовая реализация модальных окон
    }

    // Заглушки для методов, которые будут реализованы позже
    updateConversionPreview() {
        console.log('Updating conversion preview...');
    }

    toggleMergeSettings(method) {
        const joinSection = document.getElementById('joinColumnSection');
        if (joinSection) {
            joinSection.classList.toggle('hidden', method !== 'join');
        }
    }

    toggleSplitSettings(method) {
        this.currentSplitMethod = method;
        const rowsSettings = document.getElementById('rowsSettings');
        const columnSettings = document.getElementById('columnSettings');
        
        if (rowsSettings && columnSettings) {
            rowsSettings.classList.toggle('hidden', method !== 'rows');
            columnSettings.classList.toggle('hidden', method !== 'column');
        }
    }

    switchAnalysisTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('tab-active');
            btn.classList.add('tab-inactive');
        });
        
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        document.querySelector(`[data-tab="${tabName}"]`).classList.remove('tab-inactive');
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('tab-active');
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    }

    renderCharts(chartData) {
        // Заглушка для рендеринга графиков
        console.log('Rendering charts:', chartData);
    }

    populateDataTable(tableData) {
        // Заглушка для заполнения таблицы
        console.log('Populating data table:', tableData);
    }
}

// Инициализация приложения
document.addEventListener('DOMContentLoaded', () => {
    window.TableMaster = new TableMasterApp();
});

// Глобальные вспомогательные функции
window.formatFileSize = (bytes) => {
    return window.TableMaster.formatFileSize(bytes);
};

export default TableMasterApp;