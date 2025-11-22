import apiClient from './api';

class ConversionService {
    /**
     * Конвертация файла
     */
    async convertFile(file, targetFormat, options = {}) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('format', targetFormat);
        
        // Добавляем дополнительные опции
        if (options.includeHeaders !== undefined) {
            formData.append('include_headers', options.includeHeaders);
        }
        if (options.prettyPrint !== undefined) {
            formData.append('pretty_print', options.prettyPrint);
        }
        if (options.sheetName) {
            formData.append('sheet_name', options.sheetName);
        }

        try {
            const response = await apiClient.post('/convert', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                responseType: 'blob' // Для скачивания файла
            });

            return {
                success: true,
                blob: response.data,
                filename: this.getDownloadFilename(response, file.name, targetFormat)
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Получение имени файла для скачивания из headers
     */
    getDownloadFilename(response, originalName, format) {
        const contentDisposition = response.headers['content-disposition'];
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename="(.+)"/);
            if (filenameMatch) {
                return filenameMatch[1];
            }
        }
        
        // Fallback: генерируем имя сами
        const nameWithoutExt = originalName.replace(/\.[^/.]+$/, "");
        return `${nameWithoutExt}.${format}`;
    }

    /**
     * Получение списка поддерживаемых форматов
     */
    async getSupportedFormats() {
        try {
            const response = await apiClient.get('/formats');
            return {
                success: true,
                formats: response.data.formats || []
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                formats: ['json', 'csv', 'xml', 'xlsx', 'xls'] // fallback
            };
        }
    }
}

export default new ConversionService();