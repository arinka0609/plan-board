var app = new Vue({
    el: '#app-block5',
    data: {
      infoToDelete: null,
      isDeleteModalOpen: false,
    },
    methods: {
      openDeleteModal(infoId) {
        this.infoToDelete = infoId;
        this.isDeleteModalOpen = true;
    },
    closeDeleteModal() {
        window.location.reload();
        this.isDeleteModalOpen = false;
        this.infoToDelete = null;
    },
    deleteInfo() {
        axios.get('/src/deleteinfo.php', {
                params: {
                    info_id: this.infoToDelete
                }
            })
            .then(response => {
                console.log(response.data);
                window.location.reload();

              
            })
            .catch(error => {
                console.error('Ошибка при удалении новости:', error);
                this.closeDeleteModal();
            });
    },
    }
  });