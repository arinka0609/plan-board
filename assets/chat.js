new Vue({
    el: '#openModal',
    data: {
        isChatModalOpen: false,
        isChat1ModalOpen: false,
        isChat2ModalOpen: false,
        allUsersData: [],
        filteredUsersData: [],
        searchQuery: '',
        selectedUser: {
            name: '',
            avatar: '',
            email: '',
            status: '',
            unique_id: '',
            statusColor: '',
            initialName: '',
            initialEmail: '',
            initialAvatar: null
        },
        messages: [],
        currentUser: {
            name: '',
            avatar: '',
            email: '',
            status: '',
            unique_id: ''
        },
        message: '',
        isEditMode: false,
        editName: '',
        editEmail: '',
        editAvatar: null,
        avatarError: '', // Ошибка для поля выбора аватара
        nameError: '', // Ошибка для поля ввода имени
        emailError: '' // Ошибка для поля ввода почты
    },
    methods: {
        openChatModal() {
            this.isChatModalOpen = true;
            console.log('Модальное окно открыто');
            this.fetchLastMessage();
        },
        openChat2Modal() {
            this.selectedUser = { ...this.currentUser };
            this.isChat2ModalOpen = true;
            console.log('Модальное окно открыто');
            this.initialName = this.selectedUser.name;
            this.initialEmail = this.selectedUser.email;
            this.initialAvatar = this.selectedUser.avatar; // Сохранение исходного аватара
        },
        updateAvatar(event) {
            console.log('Файл аватара:', event.target.files[0]);
            this.editAvatar = event.target.files[0];
        },
        toggleEditMode() {
            this.isEditMode = !this.isEditMode;
            this.editName = this.selectedUser.name;
            this.editEmail = this.selectedUser.email;
            console.log('Режим редактирования включен. Имя:', this.editName, 'Email:', this.editEmail);
        },
        saveProfile() {
            if (
                this.editName === this.selectedUser.name &&
                this.editEmail === this.selectedUser.email &&
                this.editAvatar === null
            ) {
                // Если данные не были изменены, просто обновляем страницу
                window.location.reload();
                return;
            }
            // Проверка на пустоту данных
            if (!this.editName.trim()) {
                this.nameError = 'Имя не может быть пустым';
                return;
            } else {
                this.nameError = ''; // Очистка ошибки
            }
            if (!this.editEmail.trim()) {
                this.emailError = 'Почта не может быть пустой';
                return;
            } else {
                this.emailError = ''; // Очистка ошибки
            }
            
            // Проверка ввода нового имени
            if (/^\d+$/.test(this.editName)) {
                this.nameError = 'Имя не может состоять только из цифр';
            } else {
                this.nameError = ''; // Очистка ошибки
            }
            // Проверка ввода новой почты
            if (!/^[^@\s]+@(yandex\.ru|mail\.ru|inbox\.ru|bk\.ru|hotmail\.com|live\.com|xakep\.ru|furmail\.ru|gmail\.com)$/.test(this.editEmail)) {
                this.emailError = 'Неверный формат почты';
            } else {
                this.emailError = ''; // Очистка ошибки
            }
            
            // Проверка типа загружаемого аватара
            if (this.editAvatar && this.editAvatar.type && !this.editAvatar.type.startsWith('image/')) {
                this.avatarError = 'Загрузите изображение';
            } else {
                this.avatarError = ''; // Очистка ошибки
            }
            // Если есть ошибки валидации, прекратить сохранение
            if (this.avatarError || this.nameError || this.emailError) {
                return;
            }
            
            // Если нет ошибок и данные изменились, можно сохранить данные
            const formData = new FormData();
            formData.append('name', this.editName);
            formData.append('email', this.editEmail);
            if (this.editAvatar) {
                formData.append('avatar', this.editAvatar);
            }
        
            axios.post('/src/update_profile.php', formData)
                .then(response => {
                    console.log('Серверный ответ:', response.data);
                    if (response.data.success) {
                        this.selectedUser.name = this.editName;
                        this.selectedUser.email = this.editEmail;
                        if (response.data.avatar) {
                            this.selectedUser.avatar = response.data.avatar;
                        }
                        this.isEditMode = false;
                        window.location.reload();
                    } else {
                        console.error('Ошибка при обновлении профиля:', response.data.message);
                    }
                })
                .catch(error => {
                    console.error('Ошибка при обновлении профиля:', error);
                });
        },
        openChat1Modal(unique_id) {
            this.isChatModalOpen = false;
            this.isChat1ModalOpen = true;

            this.selectedUser = this.allUsersData.find(user => user.unique_id === unique_id);

            this.fetchMessages(this.currentUser.unique_id, this.selectedUser.unique_id);
            this.checkLoginStatus(unique_id);  // Check status when opening chat modal
        },
        closeChat2Modal() {
            this.isChat2ModalOpen = false;
            // Сбрасываем только измененные данные при закрытии модального окна
            this.editName = this.initialName;
            this.editAvatar = this.initialAvatar;
            this.editEmail = this.initialEmail;
            this.nameError = '';
            this.emailError = '';
            this.avatarError = '';
        },
        closeChat1Modal() {
            this.fetchLastMessage();
            this.isChat1ModalOpen = false;
            this.isChatModalOpen = true;
            console.log('Модальное окно закрыто');
        },
        clearMessageField() {
            this.message = ''; 
            document.getElementById('messageInput').value = '';
        },
        closeChatModal() {
            this.isChatModalOpen = false;
            console.log('Модальное окно закрыто');
            this.fetchLastMessage();
        },
        searchUsers() {
            let query = this.searchQuery.toLowerCase();
            this.filteredUsersData = this.allUsersData.filter(user => {
                return user.name.toLowerCase().includes(query);
            });
        },
        fetchLastMessage() {
            axios.get('/src/fetch_last_messages.php', {
                params: {
                    currentUserUniqueId: this.currentUser.unique_id,
                    targetUserUniqueId: this.selectedUser.unique_id
                }
            })
            .then(response => {
                const lastMessage = response.data;

                if (lastMessage) {
                    this.selectedUser.lastMessage = lastMessage.msg;
                } else {
                    this.selectedUser.lastMessage = 'Нет сообщений';
                }

                let user = this.allUsersData.find(user => user.unique_id === this.selectedUser.unique_id);
                if (user) {
                    if (lastMessage) {
                        user.lastMessage = lastMessage.msg;
                    } else {
                        user.lastMessage = 'Нет сообщений';
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка при получении последнего сообщения:', error);
            });
        },
        fetchUsersData() {
            let currentUserId = document.getElementById("currentUserId").value;
            axios.get("/src/users.php?currentUserId=" + currentUserId)
                .then(response => {
                    this.allUsersData = response.data;
                    this.filteredUsersData = this.allUsersData;

                    this.currentUser = {
                        unique_id: currentUserId,
                        name: document.getElementById("currentUserName").value,
                        avatar: document.getElementById("currentUserAvatar").value,
                        email: document.getElementById("currentUserEmail").value,
                        status: document.getElementById("currentUserStatus").value
                    };

                    this.filteredUsersData.forEach(user => {
                        console.log('Идентификатор текущего пользователя:', currentUserId);
                        console.log('Отправка запроса для пользователя с ID:', user.unique_id);
                        
                        axios.get('/src/fetch_last_messages.php', {
                            params: {
                                currentUserUniqueId: currentUserId,
                                targetUserUniqueId: user.unique_id
                            }
                        })
                        .then(response => {
                            const lastMessage = response.data;
                            if (lastMessage) {
                                user.lastMessage = lastMessage.msg;
                            } else {
                                user.lastMessage = 'Нет сообщений';
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка при получении последнего сообщения:', error);
                        });

                        this.checkLoginStatus(user.unique_id);
                    });
                })
                .catch(error => {
                    console.error('Ошибка при получении пользователей:', error);
                });
        },
        loadMessagesFromStorage() {
            const savedMessages = localStorage.getItem('messages');
            if (savedMessages) {
                this.messages = JSON.parse(savedMessages);
            }
        },
        checkLoginStatus(unique_id) {
            axios.get('/src/get_active_users.php', {
                params: {
                    unique_id: unique_id
                }
            })
            .then(response => {
                console.log('Ответ сервера по статусу:', response.data);
                const loginStatus = response.data;
                const user = this.allUsersData.find(user => user.unique_id === unique_id);

                if (user) {
                    const isLoggedIn = loginStatus.logged_in === true || loginStatus.logged_in === "true";
                    console.log('Пользователь:', unique_id, 'В сети:', isLoggedIn);

                    user.statusClass = isLoggedIn ? 'online' : 'offline';
                    user.status = isLoggedIn ? 'online' : 'offline';  // Update user status text

                    // If the checked user is the selected user, update the selectedUser status
                    if (this.selectedUser.unique_id === unique_id) {
                        this.selectedUser.status = user.status;
                    }

                    this.$forceUpdate();
                }
            })
            .catch(error => {
                console.error('Ошибка при проверке статуса входа:', error);
            });
        },
        sendMessage() {
            let formData = new FormData(document.querySelector('.typing-area'));
            
            axios.post('/src/send_message.php', formData)
                .then(response => {
                    const newMessage = {
                        outgoing_msg_id: this.currentUser.unique_id,
                        incoming_msg_id: this.selectedUser.unique_id,
                        msg: formData.get('message')
                    };
                    this.messages.push(newMessage);

                    localStorage.setItem('messages', JSON.stringify(this.messages));

                    this.clearMessageField();
                    this.fetchLastMessage();
                    
                })
                .catch(error => {
                    console.error('Ошибка при отправке сообщения:', error);
                });
        },
        fetchMessages() {
            console.log('Метод fetchMessages() вызван.');
            console.log('currentUserId:', this.currentUser.unique_id);
            console.log('selectedUserId:', this.selectedUser.unique_id);

            let outgoing_msg_id = this.currentUser.unique_id;
            let incoming_msg_id = this.selectedUser.unique_id;

            console.log('Исходящий идентификатор сообщений:', outgoing_msg_id);
            console.log('Входящий идентификатор сообщений:', incoming_msg_id);

            axios.get('/src/fetch_messages.php', {
                params: {
                    outgoing_msg_id: outgoing_msg_id,
                    incoming_msg_id: incoming_msg_id
                }
            })
            .then(response => {
                this.messages = response.data.filter(message => 
                    (message.outgoing_msg_id === outgoing_msg_id && message.incoming_msg_id === incoming_msg_id) || 
                    (message.incoming_msg_id === outgoing_msg_id && message.outgoing_msg_id === incoming_msg_id)
                );

                let storedMessages = JSON.parse(localStorage.getItem('messages'));
                if (storedMessages) {
                    this.messages.forEach(message => {
                        let index = storedMessages.findIndex(m => m.id === message.id);
                        if (index === -1) {
                            storedMessages.push(message);
                        }
                    });
                    localStorage.setItem('messages', JSON.stringify(storedMessages));
                }

            })
            .catch(error => {
                console.error('Ошибка при получении сообщений:', error);
            });
        },
        getMessageClass(message) {
            if (message.outgoing_msg_id === this.currentUser.unique_id) {
                return 'chat outgoing';
            } else {
                return 'chat incoming';
            }
        },
        pollUserStatus() {
            this.allUsersData.forEach(user => {
                this.checkLoginStatus(user.unique_id);
            });
        },
        pollLastMessage() {
            if (this.selectedUser && this.selectedUser.unique_id) {
                this.fetchLastMessage();
            }
        }
    },
    mounted() {
        this.fetchUsersData();
        this.loadMessagesFromStorage();
        console.log('Компонент смонтирован и данные пользователей загружены');

        // Start polling user status every 10 seconds
        this.statusPollingInterval = setInterval(this.pollUserStatus, 10000);

        // Start polling for the last message every second
        this.messagePollingInterval = setInterval(this.pollLastMessage, 1000);
    },
    beforeDestroy() {
        // Clear the intervals when the component is destroyed
        clearInterval(this.statusPollingInterval);
        clearInterval(this.messagePollingInterval);
    }
});