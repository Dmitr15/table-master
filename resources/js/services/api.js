import axios from 'axios';

// Базовый клиент API
const apiClient = axios.create({
    baseURL: '/api', // Все запросы будут идти к /api/*
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    timeout: 30000 // 30 секунд таймаут
});

// Интерсептор для обработки ошибок
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        console.error('API Error:', error);
        
        if (error.response?.status === 422) {
            // Ошибки валидации
            const errors = error.response.data.errors;
            const firstError = Object.values(errors)[0]?.[0];
            throw new Error(firstError || 'Ошибка валидации данных');
        } else if (error.response?.status === 500) {
            throw new Error('Внутренняя ошибка сервера');
        } else if (error.code === 'ECONNABORTED') {
            throw new Error('Превышено время ожидания ответа от сервера');
        } else {
            throw new Error(error.response?.data?.message || 'Произошла ошибка');
        }
    }
);

export default apiClient;