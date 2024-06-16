
 // ЗАГРУЗКА
window.addEventListener('load', function () {
    // Находим загрузочный блок
    var loader = document.querySelector('.loader');

    // Останавливаем анимацию загрузки
    loader.style.animationPlayState = 'paused';

    // Скрываем загрузочный блок после небольшой задержки
    setTimeout(function () {
        loader.style.display = 'none';
    }, 800); // Задержка в 500 миллисекунд
});


function closeSidebarMenu() {
    document.getElementById('sidebarMenuCloseButton').click();
}

function showSection(sectionId, sectionTitle) {
    // Сохраняем текущую секцию и ее заголовок в локальное хранилище
    localStorage.setItem('currentSection', sectionId);
    localStorage.setItem('currentSectionTitle', sectionTitle);

    // Скрываем все секции на странице
    document.querySelectorAll('.page-section').forEach(section => {
        section.style.display = 'none';
    });

    // Отображаем выбранную секцию
    var selectedSection = document.getElementById(sectionId);
    selectedSection.style.display = 'block';

    // Обновляем заголовок выбранной секции
    document.getElementById('sectionTitle').innerText = sectionTitle;

    // Закрываем шторку
    closeSidebarMenu();
}

// Функция для отображения секции при загрузке страницы
function showStoredSection() {
    const currentSection = localStorage.getItem('currentSection');
    const currentSectionTitle = localStorage.getItem('currentSectionTitle');

    if (currentSection && currentSectionTitle) {
        showSection(currentSection, currentSectionTitle);
    } else {
        // Если в локальном хранилище нет сохраненных данных, отображаем первую секцию
        showSection('newsSection', 'Добавление новости');
    }
}

// Проверяем локальное хранилище при загрузке страницы и отображаем соответствующую секцию
document.addEventListener('DOMContentLoaded', showStoredSection);

// Функция для удаления новости
function deleteRow(id) {
    if (confirm("Вы уверены, что хотите удалить эту запись?")) {
        fetch('/src/deleteNews.php?news_id=' + id, {
            method: 'GET',
        })
        .then(response => {
            if (response.ok) {
                // После успешного удаления новости обновляем данные о новостях
                window.location.reload();
            } else {
                throw new Error('Ошибка удаления записи');
            }
        })
        .catch(error => {
            console.error('Ошибка удаления записи:', error);
        });
    }
}
// Удаления блока
function deleteBlock(id) {
    if (confirm("Вы уверены, что хотите удалить эту запись?")) {
        fetch('/src/deleteblock.php?block_id=' + id, {
            method: 'GET',
        })
        .then(response => {
            if (response.ok) {
                // После успешного удаления новости обновляем данные о новостях
                window.location.reload();
            } else {
                throw new Error('Ошибка удаления записи');
            }
        })
        .catch(error => {
            console.error('Ошибка удаления записи:', error);
            window.location.reload();
        });
    }
}

// Удаления контента
function deleteCont(id) {
    if (confirm("Вы уверены, что хотите удалить эту запись?")) {
        fetch('/src/deleteinfo.php?info_id=' + id, {
            method: 'GET',
        })
        .then(response => {
            if (response.ok) {
                // После успешного удаления новости обновляем данные о новостях
                window.location.reload();
            } else {
                throw new Error('Ошибка удаления записи');
            }
        })
        .catch(error => {
            console.error('Ошибка удаления записи:', error);
        });
    }
}
// Удаление пользователя
function deleteUser(userId) {
    if (confirm('Вы уверены, что хотите удалить этого пользователя?')) {
        // Создаем XMLHttpRequest объект
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/src/delete_user.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        // Определяем функцию для обработки ответа сервера
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('Пользователь удален успешно.');
                // Перезагружаем страницу или обновляем таблицу данных
                location.reload();
            } else {
                alert('Ошибка при удалении пользователя.');
            }
        };

        // Отправляем запрос с ID пользователя
        xhr.send('id=' + userId);
    }
}


 // КОММЕНТ
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.submit-comment').forEach(function(button) {
        button.addEventListener('click', function() {
            const commentInput = button.closest('.blog-card').querySelector('.comment-input'); // Получаем ссылку на элемент поля ввода
            const uniqueId = button.closest('.blog-card').querySelector('input[name="unique_id"]').value;
            const blockId = button.closest('.blog-card').id; // Получаем ID блока
            const currentDate = new Date().toISOString(); // Получаем текущую дату в формате ISO

            fetch('/src/comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `unique_id=${uniqueId}&comment=${commentInput.value}&info_id=${blockId}&date=${currentDate}` // Отправляем ID блока и дату
            })
            .then(response => {
                if (response.ok) {
                    commentInput.value = ''; // Очищаем поле ввода комментария после успешной отправки
                    window.location.reload();
                    return response.text();
                } else {
                    throw new Error('Ошибка при отправке комментария');
                }
            })
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});





     // ЛАЙК
     $(document).on('click', '.comment-icon.like', function() {
        var comment_id = $(this).data('comment-id');
        var user_id = $('[name="user_id"]').val(); // Получаем user_id
        console.log('User ID:', user_id); // Выводим значение user_id в консоль
        console.log('Comment ID:', comment_id); // Выводим значение comment_id в консоль
        $.ajax({
            type: 'POST',
            url: '/src/like.php', // замените на путь к вашему обработчику лайков
            data: {
                like: 1,
                user_id: user_id, // Передаем user_id
                comment_id: comment_id
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    // Обновляем количество лайков на странице
                    $('.comment-count[data-comment-id="' + comment_id + '"]').text(data.like_count);
                } else {
                    // Выводим сообщение об ошибке, если что-то пошло не так
                    alert(data.message);
                }
            }
        });
    });




    $(document).on('click', '.info-like-icon', function() {
        var info_id = $(this).data('info-id');
        var user_id = $('[name="user_id"]').val(); // Получаем user_id
        console.log('User ID:', user_id); // Выводим значение user_id в консоль
        console.log('Info ID:', info_id); // Выводим значение info_id в консоль
        $.ajax({
            type: 'POST',
            url: '/src/like_info.php', // Путь к вашему PHP скрипту для обработки лайков блоков информации
            data: {
                like: 1,
                user_id: user_id,
                info_id: info_id
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    // Обновляем количество лайков на странице
                    $('.info-like-count[data-info-id="' + info_id + '"]').text(data.like_count);
                } else {
                    // Выводим сообщение об ошибке, если что-то пошло не так
                    alert(data.message);
                }
            }
        });
    });



    $(document).on('click', '.info-like-count', function() {
        var info_id = $(this).data('info-id');
        var likeListModal = $('.info-like-list-modal'); // Поиск модального окна по классу напрямую
        
        $.ajax({
            type: 'POST',
            url: '/src/get_likes.php',
            data: { info_id: info_id },
            success: function(response) {
                try {
                    var users = JSON.parse(response);
                    if (Array.isArray(users)) {
                        var userList = users.join(', ');
                        likeListModal.find('#infoLikeList').html(userList.replace(/,/g, '<br>'));
                        likeListModal.toggle(); // Переключаем видимость модального окна
                    } else {
                        console.error('Ошибка: Полученный ответ не является массивом');
                    }
                } catch (error) {
                    console.error('Ошибка при обработке ответа: ' + error);
                }
            },
        });
    });
    
    // Закрытие модального окна при нажатии на крестик
    $(document).on('click', '.close', function() {
        $('.info-like-list-modal').hide();
    });


   


    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.comment-count').forEach(function(element) {
            element.addEventListener('click', function() {
                var commentId = this.getAttribute('data-comment-id');
    
                fetch('/src/get_comment_likes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ comment_id: commentId })
                })
                .then(response => response.json())
                .then(data => {
                    var likeListContainer = document.getElementById('likeList');
                    likeListContainer.innerHTML = ''; // Очистка предыдущего содержимого
    
                    data.forEach(user => {
                        var userElement = document.createElement('div');
                        userElement.textContent = user.name;
                        likeListContainer.appendChild(userElement);
                    });
    
                    var modal = document.getElementById('likeModal');
                    modal.style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
    
    function closeLikeModal() {
        var modal = document.getElementById('likeModal');
        modal.style.display = 'none';
    }
    
    // Закрытие модального окна при клике вне его
    window.onclick = function(event) {
        var modal = document.getElementById('likeModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }

// "Удалить" комментарий
document.addEventListener('DOMContentLoaded', function() {
    // Найти все кнопки "Удалить"
    document.querySelectorAll('.delete-comment').forEach(function(button) {
        // Добавить обработчик события для каждой кнопки
        button.addEventListener('click', function() {
            // Получить ID комментария из атрибута data-comment-id
            var commentId = this.getAttribute('data-comment-id');
            
            // Вывести ID комментария в консоль
            console.log('ID комментария для удаления:', commentId);
            
            // Отобразить модальное окно для подтверждения удаления
            var confirmDelete = confirm('Вы уверены, что хотите удалить этот комментарий?');
            
            // Если пользователь подтверждает удаление, выполнить удаление комментария
            if (confirmDelete) {
                // Отправить запрос на сервер для удаления комментария с помощью fetch или AJAX
                // Ваш код для отправки запроса удаления комментария с ID commentId
                
                // Пример с использованием fetch
                fetch('/src/delete_comment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ commentId: commentId })
                })
                .then(response => {
                    if (response.ok) {
                        // Если комментарий успешно удален, можно выполнить дополнительные действия, например, обновить интерфейс
                        console.log('Комментарий успешно удален');
                        window.location.reload();
                    } else {
                        console.error('Ошибка удаления комментария');
                    }
                })
                .catch(error => console.error('Ошибка:', error));
            }
        });
    });
});
















