document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('videoPost');
    const videoInput = document.getElementById('video-up');
    const progressBar = document.querySelector('.progress-bar-inside1');
    const progressMessage = document.querySelector('.progress-bar-message');
    const nameFile = document.getElementById('nameFile');
    
    videoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            nameFile.textContent = file.name;
            validateVideoFile(file);
        }
    });

    function validateVideoFile(file) {
        const maxSize = 250000000;
        const validTypes = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];
        
        if (file.size > maxSize) {
            showError('O arquivo excede o tamanho máximo permitido (512MB)');
            videoInput.value = '';
            nameFile.textContent = '';
            return false;
        }
        
        if (!validTypes.includes(file.type)) {
            showError('Formato de vídeo não suportado. Use MP4, MOV ou AVI.');
            videoInput.value = '';
            nameFile.textContent = '';
            return false;
        }
        
        return true;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const file = videoInput.files[0];
        if (!file || !validateVideoFile(file)) {
            return;
        }

        const formData = new FormData(form);
        updateProgressBar(0, 'Iniciando upload...');
        
        try {
            await uploadVideo(formData);
        } catch (error) {
            console.error('Upload error:', error);
            showError(error.message || 'Erro ao processar o vídeo');
            updateProgressBar(0);
        }
    });

    async function uploadVideo(formData) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('Accept', 'application/json');
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 30);
                    updateProgressBar(percent, `Enviando... ${percent}%`);
                }
            });
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            
                            if (response.error) {
                                reject(new Error(response.error));
                                return;
                            }

                            if (response.status === 'completed') {
                                updateProgressBar(100, response.message || 'Upload concluído!');
                                setTimeout(() => window.location.href = '/', 1500);
                                resolve(response);
                                return;
                            }

                            if (response.progress) {
                                const progressPercent = Math.min(30 + response.progress, 100);
                                updateProgressBar(
                                    progressPercent,
                                    response.message || `Processando... ${progressPercent}%`
                                );
                                if (progressPercent < 100) {
                                    resolve(response);
                                }
                            }
                        } catch (e) {
                            console.error('Response parse error:', e);
                            console.log('Raw response:', xhr.responseText);
                            reject(new Error('Erro ao processar resposta do servidor'));
                        }
                    } else {
                        reject(new Error(xhr.statusText || 'Erro no servidor'));
                    }
                }
            };
            
            xhr.onerror = () => {
                console.error('XHR Error:', xhr.statusText);
                reject(new Error('Erro na conexão com o servidor'));
            };
            
            xhr.send(formData);
        });
    }

    function updateProgressBar(percent, message) {
        if (progressBar) {
            progressBar.style.transition = 'width 0.3s ease-out';
            progressBar.style.width = `${percent}%`;
        }
        if (progressMessage && message) {
            progressMessage.textContent = message;
        }
    }

    function showError(message) {
        console.error('Error:', message);
        updateProgressBar(0, `Erro: ${message}`);
    }
});