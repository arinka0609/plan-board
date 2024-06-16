var app = new Vue({
  el: '#app',
  data: {
      formOpen: false,
      productData: {
          title: '',
          color: '#55595c',
          color1: '#FFFFFF',
          userIds: [],
          searchQuery: ''
      },
      editedBlock: {
          title: '',
          color: '#55595c',
          color1: '#FFFFFF',
          userIds: [],
          searchQuery: ''
      },
      editOperationStatus: null,
      editOperationMessage: '',
      users: [],
      blocks: [],
      selectedUsers: [],
      selectedUsersState: {},
      isUsersListVisible: false,
      operationStatus: null,
      operationMessage: ''
  },
  computed: {
      filteredUsers() {
          if (!this.productData.searchQuery) return this.users;
          return this.users.filter(user =>
              user.email.toLowerCase().includes(this.productData.searchQuery.toLowerCase())
          );
      }
  },
  created() {
    // Вызываем метод fetchUsers() при создании компонента Vue
    this.fetchUsers();
},
  methods: {
      toggleSelectAllUsers() {
          const areAllUsersSelected = this.selectedUsers.length === this.filteredUsers.length;
          if (areAllUsersSelected) {
              this.selectedUsers = [];
              for (const userId in this.selectedUsersState) {
                  this.$set(this.selectedUsersState, userId, false);
              }
          } else {
              this.filteredUsers.forEach(user => {
                  this.selectedUsers.push(user.id);
                  this.$set(this.selectedUsersState, user.id, true);
              });
          }
      },
      getBlock(blockId) {
          axios.get(`/src/getBlock1.php?id=${blockId}`)
              .then(response => {
                  const block = response.data;
                  if (block) {
                      this.editedBlock.title = block.title;
                      this.editedBlock.color = block.color;
                      this.editedBlock.color1 = block.color1;
                      $('#editBlockModal').modal('show');
                      this.blockToEdit = blockId;
                  } else {
                      console.error('Данные о блоке с ID', blockId, 'не найдены.');
                  }
              })
              .catch(error => {
                  console.error('Ошибка при получении данных о блоке:', error);
              });
      },
      openEditModal(blockId) {
        axios.get(`/src/getBlock1.php?id=${blockId}`)
            .then(response => {
                const block = response.data[0];
                if (block) {
                    this.editedBlock.title = block.title;
                    this.editedBlock.color = block.color;
                    this.editedBlock.color1 = block.color1;
                    this.editedBlock.userIds = block.user_id.split(',').map(id => parseInt(id.trim()));
    
                    // Обновляем selectedUsers и selectedUsersState на основе данных из БД
                    this.selectedUsers = [...this.editedBlock.userIds];
                    this.selectedUsersState = {};
                    this.users.forEach(user => {
                        this.$set(this.selectedUsersState, user.id, this.editedBlock.userIds.includes(user.id));
                    });
    
                    $('#editBlockModal').modal('show');
                    this.blockToEdit = blockId;
                } else {
                    console.error('Данные о блоке с ID', blockId, 'не найдены.');
                }
            })
            .catch(error => {
                console.error('Ошибка при получении данных о блоке:', error);
            });
    },
     submitEditForm() {
    if (!this.editedBlock.title || this.selectedUsers.length === 0) {
        if (this.editedBlock.title && this.selectedUsers.length === 0) {
            this.editOperationStatus = 'error';
            this.editOperationMessage = 'Минимум один пользователь обязателен';
        } else {
            this.closeEditModal();
            return;
        }
        setTimeout(() => {
            this.editOperationStatus = null;
            this.editOperationMessage = '';
        }, 2000);
        return;
    }

    if (this.editedBlock.title.trim().length > 100) {
        this.editOperationStatus = 'error';
        this.editOperationMessage = 'Заголовок не может быть длиннее 100 символов.';
        setTimeout(() => {
            this.editOperationStatus = null;
            this.editOperationMessage = '';
        }, 2000);
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
          this.resetForm();
          var editModalElement = document.getElementById('editBlockModal');
          var editModalInstance = bootstrap.Modal.getInstance(editModalElement);
          if (editModalInstance) {
              editModalInstance.hide();
          }
          document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
          document.body.classList.remove('modal-open');
          document.body.style.paddingRight = '';
      },
      deselectAllUsers() {
          this.selectedUsers = [];
          for (const userId in this.selectedUsersState) {
              this.$set(this.selectedUsersState, userId, false);
          }
      },
      toggleUserSelection(userId) {
        const index = this.selectedUsers.indexOf(userId);
        if (index === -1) {
            this.selectedUsers.push(userId);
            this.$set(this.selectedUsersState, userId, true);
        } else {
            this.selectedUsers.splice(index, 1);
            this.$set(this.selectedUsersState, userId, false);
        }
    },
      showNotification(message, status) {
          this.operationMessage = message;
          this.operationStatus = status;
          setTimeout(() => {
              this.operationMessage = '';
              this.operationStatus = null;
          }, 2000);
      },
      submitForm() {
          if (this.selectedUsers.length === 0) {
              this.showNotification('Заголовок и минимум один пользователь обязательны', 'error');
              return;
          }
          if (this.productData.title.length > 100) {
              this.showNotification('Превышено максимальное количество символов (100)', 'error');
              return;
          }
          if (!this.productData.title || this.selectedUsers.length === 0) {
              this.showNotification('Заголовок и минимум один пользователь обязательны', 'error');
              return;
          }
          const formData = new FormData();
          formData.append('title', this.productData.title);
          formData.append('color', this.productData.color);
          formData.append('color1', this.productData.color1); 
          this.selectedUsers.forEach(userId => formData.append('userIds[]', userId));

          axios.post('/src/addblock.php', formData)
              .then(response => {
                  this.showNotification('Данные успешно добавлены', 'success');
                  this.resetForm();
                  setTimeout(() => {
                      window.location.reload();
                  }, 1000);
              })
              .catch(error => {
                  this.showNotification('Ошибка при добавлении данных: ' + error.message, 'error');
              });
      },
      resetForm() {
          this.productData = {
              title: '',
              color: '#55595c',
              color1: '#FFFFFF',
              userIds: [],
              searchQuery: ''
          };
          this.editedBlock = {
              title: '',
              color: '#55595c',
              color1: '#FFFFFF',
              userIds: [],
              searchQuery: ''
          };
          this.selectedUsers = [];
          this.selectedUsersState = {};
          this.isUsersListVisible = false;
      },
      cancel() {
          this.formOpen = false;
          this.resetForm();
      },
      fetchUsers() {
        // Загрузить данные только если список пользователей пуст
        if (this.users.length === 0) {
            fetch('/src/getUsers.php')
                .then(response => response.json())
                .then(data => {
                    this.users = data;
                })
                .catch(error => console.error('Ошибка:', error));
        }
        // После загрузки данных список пользователей будет видимым
        this.isUsersListVisible = true;
    },
      toggleUsersListVisibility() {
          this.isUsersListVisible = !this.isUsersListVisible;
      },
  },
});