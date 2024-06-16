var app = new Vue({
    el: '#app-block4',
    data: {
        productData: {
            title: '',
            description: '',
            image: null,
            file: null,
            link: '',
            date1: '' // Добавляем поле date1
        },
        selectedBlockId: null,
        editData: {
            id: null,
            title: '',
            contentType: 'image',
            description: '',
            link: '',
            date1: ''
        },
        blocks: [], // Добавляем пустой массив для хранения данных о блоках
        blockId: null, // Инициализируем blockId
        contentType: 'image', // По умолчанию выбрано изображение
        editModal: null, // Добавляем переменную для модального окна
        operationMessage: '',
        operationStatus: null
    },
    mounted() {
        // Получаем DOM элемент модального окна
        this.editModal = new bootstrap.Modal(document.getElementById('editContentModal'));
        // Отправляем запрос на сервер для получения данных о блоках
        
    },
    methods: {
        
        closeEditModal() {
            // Reset the edit form
            this.resetForm();
        
            // Close the modal using jQuery
            $('#editContentModal').modal('hide');
        
            // Remove the modal backdrop manually
            $('.modal-backdrop').remove();
        },
        openEditModal: function(id) {
            axios.get('/src/getInfo.php?id=' + id)
                .then(response => {
                    // Обновляем данные в editData для отображения в модальном окне
                    this.editData = response.data;
                    // Устанавливаем id блока в объект editData
                    this.editData.id = id;
        
                    // Проверяем, есть ли изображение или файл в текущих данных
                    if (this.editData.contentType === 'image' || this.editData.contentType === 'file') {
                        // Очищаем изображение и файл в текущих данных
                        this.editData.image = null;
                        this.editData.file = null;
                    }
        
                    // Отображаем текущие данные в форме редактирования
                    if (this.editData.contentType === 'link') {
                        // Очищаем ссылку, если она уже есть
                        this.editData.link = '';
                    }
        
                    // Отображаем модальное окно
                    this.editModal.show(); // Открытие модального окна
                })
                .catch(error => {
                    console.error('Ошибка при загрузке данных для редактирования:', error);
                });
        },
        submitEditForm() {
            if (!this.editData.title) {
                this.showNotification('Заголовок не может быть пустым', 'error');
                return;
            }
            if (this.editData.link && !/^https?:\/\//i.test(this.editData.link)) {
    this.showNotification('Введите корректный URL, начиная с http:// или https://', 'error');
    return;
}
        
            // Проверка описания
            if (!this.editData.description) {
                this.showNotification('Описание не может быть пустым', 'error');
                return;
            }
        
            // Проверка даты дедлайна
            if (!this.editData.date1) {
                this.showNotification('Выберите дату дедлайна', 'error');
                return;
            }
            // Проверка типа контента
    if (this.editData.contentType === 'image') {
        // Проверка на изображение
        if (!this.editData.image) {
            this.showNotification('Выберите изображение', 'error');
            return;
        }
        // Проверка расширения изображения
        const fileExtension = this.editData.image.name.split('.').pop().toLowerCase();
        if (!['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            this.showNotification('Выберите изображение в формате JPG, JPEG, PNG или GIF', 'error');
            return;
        }
    } else if (this.editData.contentType === 'file') {
        // Проверка на файл
        if (!this.editData.file) {
            this.showNotification('Выберите файл', 'error');
            return;
        }
        // Проверка расширения файла
        const fileExtension = this.editData.file.name.split('.').pop().toLowerCase();
        if (!['doc', 'docx', 'pdf'].includes(fileExtension)) {
            this.showNotification('Выберите файл в формате DOC, DOCX или PDF', 'error');
            return;
        }
    } 
        
           
            let formData = new FormData();
            formData.append('id', this.editData.id);
            formData.append('title', this.editData.title);
            formData.append('description', this.editData.description);
            formData.append('date1', this.editData.date1);
            formData.append('contentType', this.editData.contentType); // Добавляем тип контента
            
            // Проверяем, какой тип контента выбран
            if (this.editData.contentType === 'file') {
                // Проверяем, был ли загружен новый файл
                if (this.editData.file) {
                    formData.append('file', this.editData.file); // Добавляем новый файл
                } else {
                    // Если новый файл не был загружен, удаляем файл из базы данных
                    formData.append('file', null);
                }
                // Удаляем ссылку из formData
                formData.append('link', null);
            } else if (this.editData.contentType === 'image') {
                // Проверяем, было ли загружено новое изображение
                if (this.editData.image) {
                    formData.append('image', this.editData.image); // Добавляем новое изображение
                } else {
                    // Если новое изображение не было загружено, удаляем изображение из базы данных
                    formData.append('image', null);
                }
                // Удаляем ссылку из formData
                formData.append('link', null);
            } else if (this.editData.contentType === 'link') {
                // Проверяем, была ли введена новая ссылка
                if (this.editData.link) {
                    formData.append('link', this.editData.link); // Добавляем новую ссылку
                } else {
                    // Если новая ссылка не была введена, удаляем ссылку из базы данных
                    formData.append('link', null);
                }
                // Удаляем файл и изображение из formData
                formData.append('file', null);
                formData.append('image', null);
            }
            
            axios.post('/src/editInfo.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                // Обработка успешного ответа
                this.showNotification('Данные успешно обновлены', 'success');
                // Закрываем модальное окно
                this.editModal.hide(); // Закрытие модального окна
                window.location.reload();
            })
            .catch(error => {
                // Обработка ошибки
                this.showNotification('Ошибка при обновлении данных: ' + error.message, 'error');
                console.error('Ошибка при обновлении данных:', error);
            });
        },
        showNotification(message, status) {
            this.operationMessage = message;
            this.operationStatus = status;
            setTimeout(() => {
                this.operationMessage = '';
                this.operationStatus = null;
            }, 1000);
        },
        resetForm() {
            this.productData.title = '';
            this.productData.description = '';
            this.productData.image = null;
            this.productData.file = null;
            this.productData.link = '';
            this.productData.date1 = ''; // Сбрасываем значение даты
        },
        submitForm() {
            // Проверяем тип контента и соответствующие ошибки
    if (this.contentType === 'image') {
         if (!this.productData.file && !this.productData.image && !this.productData.link) {
                this.showNotification('Выберите хотя бы один тип контента', 'error');
                return;
            }
        const fileExtension = this.productData.image.name.split('.').pop().toLowerCase();
        if (!['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            this.showNotification('Выберите изображение в формате JPG, JPEG, PNG или GIF', 'error');
            return;
        }
    } else if (this.contentType === 'file') {
        if (!this.productData.file) {
            this.showNotification('Выберите файл', 'error');
            return;
        }
        const fileExtension = this.productData.file.name.split('.').pop().toLowerCase();
        if (!['doc', 'docx', 'pdf'].includes(fileExtension)) {
            this.showNotification('Выберите файл в формате DOC, DOCX или PDF', 'error');
            return;
        }
    }
            if (this.productData.title.length > 45) {
                this.showNotification('Превышено максимальное количество символов в "Тема новости"(45)', 'error');
                return;
            }
            if (this.productData.description.length > 500) {
                this.showNotification('Превышено максимальное количество символов "О новости"(500)', 'error');
                return;
            }
            if (!this.productData.title || !this.productData.description) {
                this.showNotification('Заполните текстовые поля', 'error');
                return;
            }
            if (!this.blockId) {
                this.showNotification('Выберите доску для добавления контента', 'error');
                return;
            }
        
            // Проверяем, была ли выбрана дата дедлайна
            if (!this.productData.date1) {
                this.showNotification('Выберите дату дедлайна', 'error');
                return;
            }
           
            
        
            // Создаем объект FormData для отправки данных формы
            let formData = new FormData();
            formData.append('title', this.productData.title);
            formData.append('description', this.productData.description);
            formData.append('block_id', this.blockId); // Включаем blockId в данные формы
            formData.append('date1', this.productData.date1); // Включаем date1 в данные формы
            
            // Проверяем тип контента
            if (this.contentType === 'image') {
                formData.append('image', this.productData.image);
            } else if (this.contentType === 'file') {
                formData.append('file', this.productData.file);
            } else if (this.contentType === 'link') {
                formData.append('link', this.productData.link);
            }
    
            // Отправляем данные на сервер с помощью Axios
            axios.post('/src/addinfo.php', formData)
                .then(response => {
                    this.showNotification('Данные успешно добавлены', 'success');
                    console.log(response.data); // Выводим ответ от сервера в консоль
                    window.location.reload();
                    this.resetForm(); // Очищаем форму после успешной отправки
                })
                .catch(error => {
                    this.showNotification('Ошибка при добавлении данных: ' + error.message, 'error');
                    console.error(error); // Выводим ошибку в консоль
                    // Дополнительные действия в случае ошибки при отправке данных (если нужно)
                });
        },
        
        handleFileUpload(event) {
            const fieldName = event.target.name;
            if (fieldName === 'image') {
                this.productData.image = event.target.files[0];
            } else if (fieldName === 'file') {
                this.productData.file = event.target.files[0];
            }
        },
        handleImageUpload(event) {
            this.editData.image = event.target.files[0];
            // Обновляем src элемента img
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('edit-current-image').src = e.target.result;
            };
            reader.readAsDataURL(this.editData.image);
        },
        handleFileUploadForFile(event) {
            this.editData.file = event.target.files[0];
        },
        cancel() {
            this.resetForm(); // Очищаем форму при закрытии
            this.formOpen = false; // Закрываем форму
        }
    }
});

