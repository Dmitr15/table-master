import apiClient from './api';

class SplitService {
    /**
     * Разделение файла
     */
    async splitFile(file, splitMethod, options = {}) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('method', splitMethod);
        
        // Опции в зависимости от метода
        if (splitMethod === 'by_rows') {
            formData.append('rows_per_file', options.rowsPerFile);
        } else if (splitMethod === 'by_column') {
            formData.append('split_column', options.splitColumn);
        }
        
        if (options.includeHeaders !== undefined) {
            formData.append('include_headers', options.includeHeaders);
        }

        try {
            const response = await apiClient.post('/split', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                responseType: 'blob'
            });

            return {
                success: true,
                blob: response.data,
                filename: this.getDownloadFilename(response) || 'split_files.zip'
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

export default new SplitService();