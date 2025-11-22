import apiClient from './api';

class MergeService {
    /**
     * Слияние нескольких файлов
     */
    async mergeFiles(files, mergeMethod, options = {}) {
        const formData = new FormData();
        
        // Добавляем все файлы
        files.forEach((file, index) => {
            formData.append(`files[${index}]`, file);
        });
        
        formData.append('method', mergeMethod);
        
        // Дополнительные опции
        if (options.joinColumn) {
            formData.append('join_column', options.joinColumn);
        }
        if (options.includeHeaders !== undefined) {
            formData.append('include_headers', options.includeHeaders);
        }

        try {
            const response = await apiClient.post('/merge', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                responseType: 'blob'
            });

            return {
                success: true,
                blob: response.data,
                filename: this.getDownloadFilename(response) || 'merged_file.xlsx'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    getDownloadFilename(response) {
        const contentDisposition = response.headers['content-disposition'];
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename="(.+)"/);
            return filenameMatch ? filenameMatch[1] : null;
        }
        return null;
    }
}

export default new MergeService();