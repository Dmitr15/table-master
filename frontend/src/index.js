import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';

// Импорт настроек для Day.js (опционально, для работы с датами)
import dayjs from 'dayjs';
import 'dayjs/locale/ru';

// Настройка локализации для русской локали
dayjs.locale('ru');

// Создание корневого элемента и рендеринг приложения
const root = ReactDOM.createRoot(document.getElementById('root'));

root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);

// Опционально: регистрация Service Worker для PWA (если нужно)
if ('serviceWorker' in navigator && process.env.NODE_ENV === 'production') {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then((registration) => {
        console.log('SW registered: ', registration);
      })
      .catch((registrationError) => {
        console.log('SW registration failed: ', registrationError);
      });
  });
}

// Обработка глобальных ошибок (опционально)
window.addEventListener('error', (event) => {
  console.error('Global error:', event.error);
});

// Отчет о веб-виталиках (опционально, для метрик производительности)
const reportWebVitals = (onPerfEntry) => {
  if (onPerfEntry && onPerfEntry instanceof Function) {
    import('web-vitals').then(({ getCLS, getFID, getFCP, getLCP, getTTFB }) => {
      getCLS(onPerfEntry);
      getFID(onPerfEntry);
      getFCP(onPerfEntry);
      getLCP(onPerfEntry);
      getTTFB(onPerfEntry);
    });
  }
};

reportWebVitals(console.log); // В продакшене можно отправлять в аналитику