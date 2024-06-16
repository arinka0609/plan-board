var newsApp = new Vue({
    el: '#app-block3',
    data: {
        newsList: [],
        newsToDelete: null,
        isDeleteModalOpen: false,
        isEditModalOpen: false,
        editFormData: {
            id: null,
            title: '',
            description: '',
            image: null // Добавляем поле для хранения изображения
        },
        editOperationStatus: null,
        editOperationMessage: ''
    },
    mounted() {
        this.fetchNews();
    },
    methods: {
        fetchNews() {
            axios.get('/src/getNewsform.php')
                .then(response => {
                    this.newsList = response.data;
                })
                .catch(error => {
                    console.error('Ошибка при получении данных о новостях:', error);
                });
        },
        goToSlide(index) {
            // Используем jQuery для управления каруселью
            $('#myCarousel').carousel(index);

                // Получаем элемент слайдера
                var carouselElement = document.getElementById('myCarousel');

                // Прокручиваем страницу к элементу слайдера
                carouselElement.scrollIntoView({ behavior: 'smooth' });
        },
    
        openDeleteModal(newsId) {
            this.newsToDelete = newsId;
            this.isDeleteModalOpen = true;
        },
        closeDeleteModal() {
            window.location.reload();
            this.isDeleteModalOpen = false;
            this.newsToDelete = null;
        },
        deleteNews() {
            axios.get('/src/deleteNews.php', {
                    params: {
                        news_id: this.newsToDelete
                    }
                })
                .then(response => {
                    console.log(response.data);
                    window.location.reload();
                    this.fetchNews();
                    this.closeDeleteModal();
                })
                .catch(error => {
                    console.error('Ошибка при удалении новости:', error);
                    this.closeDeleteModal();
                });
        },
        openEditModal(newsId) {
            const newsItem = this.newsList.find(item => item.id === newsId);
            this.editFormData.id = newsId;
            this.editFormData.title = newsItem.title;
            this.editFormData.description = newsItem.description;
            this.editFormData.image = null; // Сбрасываем изображение
            this.isEditModalOpen = true;
        },
        closeEditModal() {
            window.location.reload();
            this.isEditModalOpen = false;
            this.editFormData.id = null;
            this.editFormData.title = '';
            this.editFormData.description = '';
            this.editFormData.image = null;
        },
        handleImageUpload(event) {
            this.editFormData.image = event.target.files[0];
        },
        showEditError(message) {
            this.editOperationStatus = 'error';
            this.editOperationMessage = message;
            setTimeout(() => {
                this.editOperationStatus = null;
                this.editOperationMessage = '';
            }, 2000);
        },
        resetEditOperation() {
            this.editOperationStatus = null;
            this.editOperationMessage = '';
        },
        submitEditForm() {
            // Проверка наличия данных
            if (!this.editFormData.title || !this.editFormData.description || !this.editFormData.image) {
                this.showEditError('Заполните все поля');
                return;
            }

            // Проверка превышения максимальной длины заголовка
            if (this.editFormData.title.length > 45) {
                this.showEditError('Превышено максимальное количество символов в "Тема новости" (45)');
                return;
            }

            // Проверка превышения максимальной длины описания
            if (this.editFormData.description.length > 500) {
                this.showEditError('Превышено максимальное количество символов в "О новости" (500)');
                return;
            }

            let formData = new FormData();
            formData.append('id', this.editFormData.id);
            formData.append('title', this.editFormData.title);
            formData.append('description', this.editFormData.description);
            if (this.editFormData.image) {
                formData.append('image', this.editFormData.image);
            }
            axios.post('/src/editNews.php', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    console.log(response.data);
                    window.location.reload();
                    this.fetchNews();
                    this.closeEditModal();
                })
                .catch(error => {
                    console.error('Ошибка при редактировании новости:', error);
                    this.closeEditModal();
                });
        }
        
    }
});