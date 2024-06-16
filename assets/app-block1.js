var blocksApp = new Vue({
  el: '#app-block1',
  data: {
    blocks: [],
    noBlocksMessage: '',
    notifications: {},
    isModalOpen: false,
    lastContent: null
  },
  computed: {
    hasNotifications() {
      return Object.values(this.notifications).some(hasNotification => hasNotification);
    }
  },
  mounted() {
    this.loadNotifications();
    this.fetchBlocks();
    setInterval(this.checkNotifications, 2000);
  },
  methods: {
    redirectToSpecificPage(blockId) {
      var formData = new FormData();
      formData.append('blockId', blockId);
      var url = '/src/set_block_id.php';
      axios.post(url, formData)
        .then(response => {
          console.log(response.data);
          sessionStorage.setItem('blockId', blockId);
          window.location.href = '/doskuser.php';
        })
        .catch(error => {
          console.error(error);
        });
    },
    fetchBlocks() {
      axios.get('/src/blockuser.php')
        .then(response => {
          this.blocks = response.data;
          if (this.blocks.length === 0) {
            this.noBlocksMessage = 'Нет доступных блоков';
          }
        })
        .catch(error => {
          console.error('Ошибка при получении данных о блоках:', error);
        });
    },
    checkNotifications() {
      axios.get('/src/check_notifications.php')
        .then(response => {
          const newNotifications = response.data;
          for (let blockId of newNotifications) {
            this.$set(this.notifications, blockId, true);
          }
          localStorage.setItem('notifications', JSON.stringify(this.notifications));
        })
        .catch(error => {
          console.error('Ошибка при проверке уведомлений:', error);
        });
    },
    openModal(blockId) {
      if (this.notifications[blockId] && !this.isModalOpen) {
        this.isModalOpen = true;
        this.$set(this.notifications, blockId, false);
        localStorage.setItem('notifications', JSON.stringify(this.notifications));
        this.fetchLastContent();
      }
    },
    closeModal() {
      this.isModalOpen = false;
    },
    loadNotifications() {
      const savedNotifications = localStorage.getItem('notifications');
      if (savedNotifications) {
        this.notifications = JSON.parse(savedNotifications);
      }
    },
    fetchLastContent() {
      axios.get('/src/get_last_content.php')
        .then(response => {
          console.log(response.data);  // Консольный вывод для отладки
          this.lastContent = response.data;
        })
        .catch(error => {
          console.error('Ошибка при получении последнего контента:', error);
        });
    }
  }
});