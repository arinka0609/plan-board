var app = new Vue({
    el: '#app1',
    data: {
        productData: {
            title: '',
            description: '',
            image: null // Сначала устанавливаем image в null, а потом будем его обновлять
        },
        editFormData: {
            id: null,
            title: '',
            description: '',
            image: null // Добавляем поле для хранения изображения
        },
        editOperationStatus: null,
        editOperationMessage: '',
        formOpen: false,
        operationMessage: '', // Сообщение о результате операции
        operationStatus: null // Статус операции ('success' или 'error')
    },
    methods: {
        openEditModal(newsId) {
            // Получение данных новости из базы данных
            axios.get(`/src/getNews.php?id=${newsId}`)
                .then(response => {
                    // Заполнение данных формы редактирования
                    this.editFormData.id = newsId;
                    this.editFormData.title = response.data.title;
                    this.editFormData.description = response.data.description;
                    this.editFormData.image = response.data.image; // Добавление данных об изображении
        
                    // Отображение выбранного изображения в модальном окне
                    document.getElementById('editImagePreview').src = response.data.image;
        
                    // Открытие модального окна
                    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                    editModal.show();
                })
                .catch(error => {
                    console.error(error);
                });
        },
        closeEditModal() {
            this.resetEditForm(); // Сбрасываем данные формы редактирования
            var editModalElement = document.getElementById('editModal');
            var editModalInstance = bootstrap.Modal.getInstance(editModalElement);
            if (editModalInstance) {
                editModalInstance.hide(); // Закрываем модальное окно
            }
            // Удаляем затемненный фон вручную
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = ''; // Удаляем потенциальный сдвиг контента из-за полосы прокрутки
        },
        resetEditForm() {
            this.editFormData.id = null;
            this.editFormData.title = '';
            this.editFormData.description = '';
            this.editFormData.image = null;
            document.getElementById('editImagePreview').src = ''; // Сбрасываем превью изображения
        },
        submitEditForm() {
            if (!this.editFormData || !this.editFormData.title || !this.editFormData.description || !this.editFormData.image) {
                this.showEditError('Заполните все поля', 'error');
                return;
            }
        
            if (this.editFormData.title.length > 45) {
                this.showEditError('Превышено максимальное количество символов в "Тема новости"(45)', 'error');
                return;
            }
        
            if (this.editFormData.description.length > 500) {
                this.showEditError('Превышено максимальное количество символов "О новости"(500)', 'error');
                return;
            }
        
            let formData = new FormData();
            formData.append('id', this.editFormData.id);
            formData.append('title', this.editFormData.title);
            formData.append('description', this.editFormData.description);
            // Если необходимо, добавьте также обновление изображения
            if (this.editFormData.image) {
                formData.append('image', this.editFormData.image);
            }
            // Отправляем данные на сервер с помощью Axios
            axios.post('/src/editNews.php', formData)
                .then(response => {
                    console.log(response.data);
                    this.closeEditModal(); // Закрываем модальное окно после успешного редактирования
                    window.location.reload(); 
                })
                .catch(error => {
                    console.error(error);
                    // Дополнительные действия в случае ошибки
                });
        },
        handleEditImageUpload(event) {
            const file = event.target.files[0];
            // Проверяем тип файла
            if (!file.type.startsWith('image/')) {
                this.showEditError('Пожалуйста, выберите изображение', 'error');
                // Очищаем поле input файла
                event.target.value = '';
                return;
            }
            // Обновляем значение image при выборе файла
            this.editFormData.image = file;
        },
        showEditError(message) {
            this.editOperationStatus = 'error';
            this.editOperationMessage = message;
            setTimeout(() => {
                this.editOperationStatus = null;
                this.editOperationMessage = '';
            }, 2000);
        },
        showNotification(message, status) {
            this.operationMessage = message;
            this.operationStatus = status;
            setTimeout(() => {
                this.operationMessage = '';
                this.operationStatus = null;
            }, 3000);
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            // Проверяем тип файла
            if (!file.type.startsWith('image/')) {
                this.showNotification('Пожалуйста, выберите изображение', 'error');
                // Очищаем поле input файла
                event.target.value = '';
                return;
            }
            // Обновляем значение image при выборе файла
            this.productData.image = file;
        },
        resetForm() {
            this.productData.title = '';
            this.productData.description = '';
            this.productData.image = null;
        },
        submitForm() {
            if (this.productData.title.length > 45) {
                this.showNotification('Превышено максимальное количество символов в "Тема новости"(45)', 'error');
                return;
            }
            if (this.productData.description.length > 500) {
                this.showNotification('Превышено максимальное количество символов "О новости"(500)', 'error');
                return;
            }
            if (!this.productData.title || !this.productData.description || !this.productData.image) {
                this.showNotification('Заполните все поля', 'error');
                return;
            }

            // Создаем объект FormData для отправки данных формы
            let formData = new FormData();
            formData.append('title', this.productData.title);
            formData.append('description', this.productData.description);
            formData.append('image', this.productData.image);

            // Отправляем данные на сервер с помощью Axios
            axios.post('/src/addnews.php', formData)
                .then(response => {
                    this.showNotification('Данные успешно добавлены', 'success');
                    console.log(response.data); // Выводим ответ от сервера в консоль
                    this.resetForm(); // Очищаем форму после успешной отправки
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                })
                .catch(error => {
                    this.showNotification('Ошибка при добавлении данных: ' + error.message, 'error');
                    console.error(error); // Выводим ошибку в консоль
                    // Дополнительные действия в случае ошибки при отправке данных (если нужно)
                });
        },
        cancel() {
            this.resetForm(); // Очищаем форму при закрытии
            this.formOpen = false; // Закрываем форму
        }
    }
});