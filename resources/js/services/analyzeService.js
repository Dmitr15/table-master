import apiClient from './api';

class AnalyzeService {
    /**
     * Анализ данных из файла
     */
    async analyzeData(file, analysisType, options = {}) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('analysis_type', analysisType);
        
        // Дополнительные опции
        if (options.startDate) {
            formData.append('start_date', options.startDate);
        }
        if (options.endDate) {
            formData.append('end_date', options.endDate);
        }
        if (options.includeCharts !== undefined) {
            formData.append('include_charts', options.includeCharts);
        }

        try {
            const response = await apiClient.post('/analyze', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Получение предпросмотра данных
     */
    async getPreview(file, rows = 10) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('rows', rows.toString());

        try {
            const response = await apiClient.post('/preview', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }
}

export default new AnalyzeService();