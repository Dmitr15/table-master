// // Импорты
// import './bootstrap';
// import 'flowbite';

// // Основной класс приложения Table Master
// class TableMasterApp {
//     constructor() {
//         this.init();
//     }

//     init() {
//         this.initEventListeners();
//         this.initComponents();
//         this.initFileHandling();
//         this.initNotifications();
//         console.log('Table Master App initialized');
//     }

//     // Инициализация обработчиков событий
//     initEventListeners() {
//         // Глобальные обработчики
//         document.addEventListener('DOMContentLoaded', () => {
//             this.handleNavigation();
//             this.initTooltips();
//             this.initModals();
//         });

//         // Обработчик для всех форм с data-ajax-form
//         document.addEventListener('submit', (e) => {
//             if (e.target.dataset.ajaxForm) {
//                 e.preventDefault();
//                 this.handleAjaxForm(e.target);
//             }
//         });
//     }

//     // Инициализация компонентов
//     initComponents() {
//         this.initFileUploaders();
//         this.initDataTables();
//         this.initCharts();
//     }

//     // Работа с файлами
//     initFileHandling() {
//         this.uploadedFiles = new Map();
//         this.initDragAndDrop();
//     }

//     // Drag & Drop функциональность
//     initDragAndDrop() {
//         const dropZones = document.querySelectorAll('[data-drop-zone]');
        
//         dropZones.forEach(zone => {
//             zone.addEventListener('dragover', (e) => {
//                 e.preventDefault();
//                 zone.classList.add('drag-over');
//             });

//             zone.addEventListener('dragleave', () => {
//                 zone.classList.remove('drag-over');
//             });

//             zone.addEventListener('drop', (e) => {
//                 e.preventDefault();
//                 zone.classList.remove('drag-over');
//                 this.handleDroppedFiles(e.dataTransfer.files, zone);
//             });
//         });
//     }

//     // Обработка перетащенных файлов
//     handleDroppedFiles(files, dropZone) {
//         Array.from(files).forEach(file => {
//             if (this.isValidFileType(file)) {
//                 this.processFile(file, dropZone);
//             } else {
//                 this.showNotification(`Неподдерживаемый формат файла: ${file.name}`, 'error');
//             }
//         });
//     }

//     // Валидация типа файла
//     isValidFileType(file) {
//         const validTypes = [
//             'application/vnd.ms-excel',
//             'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
//             'text/csv',
//             'application/json'
//         ];
        
//         return validTypes.includes(file.type) || 
//                file.name.endsWith('.xlsx') || 
//                file.name.endsWith('.xls') ||
//                file.name.endsWith('.csv') ||
//                file.name.endsWith('.json');
//     }

//     // Обработка файла
//     async processFile(file, targetElement) {
//         const fileId = Date.now().toString();
        
//         // Создаем объект файла
//         const fileObject = {
//             id: fileId,
//             name: file.name,
//             size: file.size,
//             type: file.type,
//             file: file,
//             preview: null
//         };

//         this.uploadedFiles.set(fileId, fileObject);
        
//         // Показываем превью
//         this.showFilePreview(fileObject, targetElement);
        
//         // Парсим Excel/CSV если нужно
//         if (file.type.includes('sheet') || file.name.endsWith('.csv')) {
//             await this.parseTableFile(fileObject);
//         }
//     }

//     // Парсинг табличных файлов
//     async parseTableFile(fileObject) {
//         try {
//             // Здесь будет интеграция с библиотекой для парсинга Excel/CSV
//             // Например, с помощью SheetJS или Papa Parse
//             console.log('Parsing file:', fileObject.name);
            
//             // Временная заглушка
//             this.showNotification(`Файл "${fileObject.name}" успешно загружен`, 'success');
            
//         } catch (error) {
//             console.error('Error parsing file:', error);
//             this.showNotification(`Ошибка при обработке файла: ${error.message}`, 'error');
//         }
//     }

//     // Инициализация загрузчиков файлов
//     initFileUploaders() {
//         const fileInputs = document.querySelectorAll('input[type="file"][data-file-upload]');
        
//         fileInputs.forEach(input => {
//             input.addEventListener('change', (e) => {
//                 Array.from(e.target.files).forEach(file => {
//                     this.processFile(file, e.target.closest('[data-file-container]'));
//                 });
//             });
//         });
//     }

//     // Показ превью файла
//     showFilePreview(fileObject, container) {
//         const previewHtml = `
//             <div class="file-item" data-file-id="${fileObject.id}">
//                 <div class="flex items-center space-x-3">
//                     <div class="file-icon">
//                         <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
//                         </svg>
//                     </div>
//                     <div class="file-info">
//                         <div class="file-name font-medium">${fileObject.name}</div>
//                         <div class="file-size text-sm text-gray-500">${this.formatFileSize(fileObject.size)}</div>
//                     </div>
//                 </div>
//                 <button type="button" class="file-remove text-red-600 hover:text-red-800" data-remove-file="${fileObject.id}">
//                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
//                     </svg>
//                 </button>
//             </div>
//         `;
        
//         if (container) {
//             container.insertAdjacentHTML('beforeend', previewHtml);
            
//             // Обработчик удаления файла
//             container.querySelector(`[data-remove-file="${fileObject.id}"]`).addEventListener('click', () => {
//                 this.removeFile(fileObject.id);
//             });
//         }
//     }

//     // Форматирование размера файла
//     formatFileSize(bytes) {
//         if (bytes === 0) return '0 Bytes';
//         const k = 1024;
//         const sizes = ['Bytes', 'KB', 'MB', 'GB'];
//         const i = Math.floor(Math.log(bytes) / Math.log(k));
//         return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
//     }

//     // Удаление файла
//     removeFile(fileId) {
//         this.uploadedFiles.delete(fileId);
//         const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
//         if (fileElement) {
//             fileElement.remove();
//         }
//     }

//     // Навигация
//     handleNavigation() {
//         // Активный пункт меню
//         const currentPath = window.location.pathname;
//         const navLinks = document.querySelectorAll('[data-nav-link]');
        
//         navLinks.forEach(link => {
//             if (link.getAttribute('href') === currentPath) {
//                 link.classList.add('active');
//             }
//         });
//     }

//     // Уведомления
//     initNotifications() {
//         this.notificationContainer = document.getElementById('notification-container');
//         if (!this.notificationContainer) {
//             this.notificationContainer = document.createElement('div');
//             this.notificationContainer.id = 'notification-container';
//             this.notificationContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
//             document.body.appendChild(this.notificationContainer);
//         }
//     }

//     showNotification(message, type = 'info', duration = 5000) {
//         const notification = document.createElement('div');
//         notification.className = `alert-${type} fade-in transform transition-all duration-300`;
//         notification.innerHTML = `
//             <div class="flex items-center justify-between">
//                 <span>${message}</span>
//                 <button type="button" class="ml-4 text-gray-500 hover:text-gray-700" onclick="this.parentElement.parentElement.remove()">
//                     &times;
//                 </button>
//             </div>
//         `;
        
//         this.notificationContainer.appendChild(notification);
        
//         // Автоматическое удаление
//         setTimeout(() => {
//             if (notification.parentElement) {
//                 notification.style.opacity = '0';
//                 notification.style.transform = 'translateX(100%)';
//                 setTimeout(() => notification.remove(), 300);
//             }
//         }, duration);
//     }

//     // Инициализация таблиц данных
//     initDataTables() {
//         // Интеграция с DataTables или кастомная реализация
//         const tables = document.querySelectorAll('[data-datatable]');
//         tables.forEach(table => {
//             this.enhanceTable(table);
//         });
//     }

//     // Улучшение таблиц
//     enhanceTable(table) {
//         // Добавляем функциональность сортировки, фильтрации
//         const headers = table.querySelectorAll('th[data-sortable]');
//         headers.forEach(header => {
//             header.style.cursor = 'pointer';
//             header.addEventListener('click', () => {
//                 this.sortTable(table, header.cellIndex);
//             });
//         });
//     }

//     // Сортировка таблицы
//     sortTable(table, columnIndex) {
//         // Реализация сортировки
//         console.log('Sorting table by column:', columnIndex);
//     }

//     // Инициализация графиков
//     initCharts() {
//         // Интеграция с Chart.js или другой библиотекой
//         const chartContainers = document.querySelectorAll('[data-chart]');
//         chartContainers.forEach(container => {
//             this.initChart(container);
//         });
//     }

//     initChart(container) {
//         // Заглушка для инициализации графиков
//         console.log('Initializing chart in:', container);
//     }

//     // Вспомогательные методы
//     initTooltips() {
//         // Инициализация tooltips
//         const elements = document.querySelectorAll('[data-tooltip]');
//         elements.forEach(el => {
//             // Базовая реализация tooltip
//         });
//     }

//     initModals() {
//         // Инициализация модальных окон
//         const modals = document.querySelectorAll('[data-modal]');
//         modals.forEach(modal => {
//             // Базовая реализация модальных окон
//         });
//     }

//     // AJAX обработка форм
//     async handleAjaxForm(form) {
//         const formData = new FormData(form);
//         const submitBtn = form.querySelector('[type="submit"]');
//         const originalText = submitBtn.textContent;
        
//         try {
//             submitBtn.disabled = true;
//             submitBtn.textContent = 'Обработка...';
            
//             const response = await fetch(form.action, {
//                 method: form.method,
//                 body: formData,
//                 headers: {
//                     'X-Requested-With': 'XMLHttpRequest',
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//                 }
//             });
            
//             const result = await response.json();
            
//             if (result.success) {
//                 this.showNotification(result.message || 'Успешно!', 'success');
//                 if (result.redirect) {
//                     window.location.href = result.redirect;
//                 }
//             } else {
//                 this.showNotification(result.message || 'Ошибка!', 'error');
//             }
            
//         } catch (error) {
//             this.showNotification('Ошибка сети', 'error');
//         } finally {
//             submitBtn.disabled = false;
//             submitBtn.textContent = originalText;
//         }
//     }
// }

// // Инициализация приложения
// document.addEventListener('DOMContentLoaded', () => {
//     window.TableMaster = new TableMasterApp();
// });

// // Глобальные вспомогательные функции
// window.formatFileSize = (bytes) => {
//     return window.TableMaster.formatFileSize(bytes);
// };

// window.showNotification = (message, type, duration) => {
//     return window.TableMaster.showNotification(message, type, duration);
// };

// // Экспорт для модулей
// export default TableMasterApp;