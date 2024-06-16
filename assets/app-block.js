var blocksApp = new Vue({
  el: '#app-block',
  data: {
    blocks: [],
    blockToDelete: null,
    isDeleteModalOpen: false,
    deletedBlockId: null,
    blockToEdit: null,
    isEditModalOpen: false,
    editedBlock: {
      title: '',
      color: '#FFFFFF',
      color1: '#000000',
      searchQuery: ''
    },
    editOperationStatus: null,
    editOperationMessage: '',
    isUsersListVisible: false,
    users: [],
    selectedUsers: [],
    selectedUsersState: {} // объект для хранения состояния выбранных пользователей
  },
  mounted() {
    this.fetchBlocks();

  },
  computed: {
    filteredUsers() {
      if (!this.editedBlock.searchQuery) return this.users;
      return this.users.filter(user =>
        user.email.toLowerCase().includes(this.editedBlock.searchQuery.toLowerCase())
      );
    }
  },
  methods: {
    // Метод для выделения всех пользователей
    toggleSelectAllUsers() {
      // Проверяем, все ли пользователи уже выбраны
      const areAllUsersSelected = this.selectedUsers.length === this.filteredUsers.length;
  
      // Если все пользователи уже выбраны, сбрасываем их выбор
      if (areAllUsersSelected) {
          // Сбрасываем список выбранных пользователей
          this.selectedUsers = [];
          // Сбрасываем состояние выбора для всех пользователей
          for (const userId in this.selectedUsersState) {
              this.$set(this.selectedUsersState, userId, false);
          }
      } else {
          // Если не все пользователи выбраны, выбираем их все
          this.filteredUsers.forEach(user => {
              this.selectedUsers.push(user.id);
              this.$set(this.selectedUsersState, user.id, true); // сохраняем состояние выбора
          });
      }
  },
  deselectAllUsers() {
    // Очищаем список выбранных пользователей
    this.selectedUsers = [];
    // Сбрасываем состояние выбора для всех пользователей
    for (const userId in this.selectedUsersState) {
        this.$set(this.selectedUsersState, userId, false);
    }
},
    toggleUserSelection(userId) {
      const index = this.selectedUsers.indexOf(userId);
      if (index === -1) {
        this.selectedUsers.push(userId);
        this.$set(this.selectedUsersState, userId, true); // сохраняем состояние выбора
      } else {
        this.selectedUsers.splice(index, 1);
        this.$delete(this.selectedUsersState, userId); // удаляем состояние выбора
      }
    },
    fetchBlocks() {
      axios.get('/src/getBlock.php')
        .then(response => {
          this.blocks = response.data;
        })
        .catch(error => {
          console.error('Ошибка при получении данных о блоках:', error);
        });
    },
    openDeleteModal(blockId) {
      console.log('ID блока переданный в openDeleteModal:', blockId);
      this.blockToDelete = blockId;
      this.isDeleteModalOpen = true;
    },
    closeDeleteModal() {
      window.location.reload();
      this.isDeleteModalOpen = false;
      this.blockToDelete = null;
      this.fetchBlocks(); // Вызываем fetchBlocks только после успешного удаления блока
    },
    openEditModal(blockId) {
      console.log('ID блока переданный в openEditModal:', blockId);
      const blockToEdit = this.blocks.find(block => block.id === blockId);
      this.editedBlock.title = blockToEdit.title;
      this.isEditModalOpen = true;
      this.blockToEdit = blockId;
      
    },

    fetchBlockData(blockId) {
      axios.get('/src/getBlock.php', { params: { blockId: blockId } })
        .then(response => {
          const blockData = response.data;
          this.editedBlock.title = blockData.title;
          // Добавляем проверку на "undefined" и пустую строку для поля color
          this.editedBlock.color = blockData.color !== undefined && blockData.color !== '' ? blockData.color : '#FFFFFF';
          // Добавляем проверку на "undefined" и пустую строку для поля color1
          this.editedBlock.color1 = blockData.color1 !== undefined && blockData.color1 !== '' ? blockData.color1 : '#000000';
        })
        .catch(error => {
          console.error('Ошибка при загрузке данных блока:', error);
        });
    },
    submitEditForm() {
      if (!this.editedBlock.title || this.selectedUsers.length === 0) {
        // Проверка наличия заголовка и выбранных пользователей
        if (this.editedBlock.title && this.selectedUsers.length === 0) {
          // Если заголовок есть, но пользователи не выбраны, выводим сообщение об этом
          this.editOperationStatus = 'error';
          this.editOperationMessage = 'Минимум один пользователь обязателен';
        } else {
          // В случае отмены заполнения формы не выводим сообщения об ошибках
          this.closeEditModal();
          return;
        }
        setTimeout(() => {
          this.editOperationStatus = null;
          this.editOperationMessage = '';
        }, 2000); // скрываем блок через 2 секунды
        return;
      }
    
      if (this.editedBlock.title.trim().length > 75) {
        // Проверка длины заголовка
        this.editOperationStatus = 'error';
        this.editOperationMessage = 'Заголовок не может быть длиннее 75 символов.';
        setTimeout(() => {
          this.editOperationStatus = null;
          this.editOperationMessage = '';
        }, 2000); // скрываем блок через 2 секунды
        return;
      }
      const formData = new FormData();
      formData.append('block_id', this.blockToEdit);
      formData.append('title', this.editedBlock.title);
      formData.append('color', this.editedBlock.color);
      formData.append('color1', this.editedBlock.color1);
      this.selectedUsers.forEach(userId => formData.append('userIds[]', userId));

      axios.post('/src/editblock.php', formData)
        .then(response => {
          this.editOperationStatus = 'success';
          this.editOperationMessage = 'Блок успешно обновлен.';
          window.location.reload();
          this.closeEditModal();
        })
        .catch(error => {
          this.editOperationStatus = 'error';
          this.editOperationMessage = 'Ошибка при обновлении блока.';
          console.error('Ошибка при обновлении блока:', error);
          setTimeout(() => {
            this.editOperationStatus = null;
            this.editOperationMessage = '';
          }, 2000);
        });
    },
    cancelEdit() {
      this.closeEditModal();
    },
    closeEditModal() {
      window.location.reload();
      this.isEditModalOpen = false;
    },
    redirectToSpecificPage(blockId) {
      // Создаем объект FormData
      var formData = new FormData();
      formData.append('blockId', blockId); // Используем blockId, переданный в качестве параметра
  
      // Укажите URL, куда должен быть отправлен POST-запрос
      var url = '/src/set_block_id.php'; // Замените на свой URL
  
      // Отправляем данные на сервер с помощью Axios
      axios.post(url, formData)
          .then(response => {
              console.log(response.data); // Выводим ответ от сервера в консоль
  
              // Сохраняем blockId в sessionStorage
              sessionStorage.setItem('blockId', blockId);
  
              // Переходим на другую страницу
              window.location.href = '/doskadmin.php';
          })
          .catch(error => {
              console.error(error); // Выводим ошибку в консоль
          });
  },
    resetEditedBlock() {
      this.editedBlock = {
        title: '',
        color: '',
        color1: '',
        searchQuery: ''
      };
      this.editOperationStatus = null;
      this.editOperationMessage = '';
    },
    fetchUsers() {
      if (this.users.length === 0) {
        fetch('/src/getUsers.php')
          .then(response => response.json())
          .then(data => {
            console.log(data);
            this.users = data;
            this.toggleUsersListVisibility();
          })
          .catch(error => console.error('Ошибка:', error));
      } else {
        this.toggleUsersListVisibility();
      }
    },
    toggleUsersListVisibility() {
      this.isUsersListVisible = !this.isUsersListVisible;
    },
  },
});