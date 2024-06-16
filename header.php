<?php
    // Включаем файл с функциями и проверкой авторизации
    require_once __DIR__ . '/src/helpers.php';

    // Проверяем авторизацию пользователя
    checkAuth();

    // Получаем текущего пользователя
    $user = currentUser();

    // Устанавливаем информацию о пользователе в сессию
    $_SESSION['user']['id'] = $user['id'];
    $_SESSION['user']['role'] = $user['role'];
    $_SESSION['user']['unique_id'] = $user['unique_id'];
    // Определяем ссылку в зависимости от роли пользователя
    switch ($_SESSION['user']['role']) {
        case 1:
            $link = '/home.php';
            break;
        case 2:
            $link = '/homeruk.php';
            break;
        case 3:
            $link = '/homeuser.php';
            break;
        default:
            $link = '/'; // Перенаправляем на главную страницу или страницу ошибки
            break;
    }
?>

<input type="hidden" id="currentUserId" value="<?php echo $user['unique_id']; ?>">
<nav role="navigation" id="openModal">
    <div class="modal-container2" v-if="isChatModalOpen">
        <div class="wrapper">
            <section class="users">
                <header>
                    <div class="content">
                        <img src="<?php echo $user['avatar'] ?>" alt="<?php echo $user['name'] ?>">
                        <div class="details">
                            <span><?php echo $user['name'] ?></span>
                            <p><?php echo $user['status'] ?></p>
                        </div>
                    </div>
                    <a class="logout" @click="closeChatModal">Выйти</a>
                </header>
                <div class="search">
                    <span class="text">Поиск пользователей</span>
                    <input type="text" v-model="searchQuery" @input="searchUsers" placeholder="Имя для поиска...">
                    <button><i class="fa fa-search"></i></button>
                </div>
                <div class="users-list">
                    <a v-for="user in filteredUsersData" :key="user.name" @click="openChat1Modal(user.unique_id)">
                        <div class="content">
                            <img :src="user.avatar" :alt="user.name">
                            <div class="details">
                                <span>{{ user.name }}</span>
                                <p>{{ user.lastMessage }}</p>
                            </div>
                        </div>
                        <div class="status-dot">
                            <i :class="['fa', 'fa-circle', user.statusClass]"></i>
                        </div>
                    </a>
                </div>
            </section>
        </div>
    </div>

    <div class="modal-container3" v-if="isChat1ModalOpen">
        <div class="wrapper">
            <section class="chat-area">
                <header>
                    <a href="#" @click="closeChat1Modal" class="back-icon"><i class="fa fa-arrow-left"></i></a>
                    <img :src="selectedUser.avatar" :alt="selectedUser.name">
                    <div class="details">
                        <span>{{ selectedUser.name }}</span>
                        <p v-if="selectedUser.status === 'online'" class="online-status">Online</p>
        <p v-else class="offline-status">Offline</p>
                    </div>
                </header>
                <div class="chat-box">   
                    <div v-for="message in messages" :key="message.msg_id" :class="getMessageClass(message)">
                        <div class="details">
                            <p>{{ message.msg }}</p>
                        </div>
                    </div>
                </div>
                <form action="#" method="post" class="typing-area" autocomplete="off" @submit.prevent="sendMessage">
                    <input type="text" name="outgoing_id" :value="currentUser.unique_id" hidden>
                    <input type="text" name="incoming_id" v-model="selectedUser.unique_id" hidden>
                    <input type="text" name="message" class="input-field" placeholder="Напишите сообщение..." v-model="message">
                    <button type="submit" :disabled="!message"><i class="fas fa-paper-plane"></i></button>
                </form>
            </section>
        </div>
    </div>
    <div class="modal-container3" v-if="isChat2ModalOpen">
    <div class="container mt-4 mb-4 p-3 d-flex justify-content-center">
        <div class="card1 p-4">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeChat2Modal"></button>
            <div class="image d-flex flex-column justify-content-center align-items-center">
                <button class="btn btn-secondary">
                    <img :src="`${selectedUser.avatar}?t=${new Date().getTime()}`" :alt="selectedUser.name" height="100" width="100" />
                </button>
                <span class="name mt-3">{{ selectedUser.name }}</span>
                <span class="idd">{{ selectedUser.email }}</span>
                <div class="d-flex mt-2">
                    <button class="btn1 btn-dark" @click="toggleEditMode">Изменить данные</button>
                </div>
                <div v-if="isEditMode" class="edit-fields mt-3">
                    <!-- Отображение ошибок -->
          
                    <div v-if="avatarError" class="error">{{ avatarError }}</div>
                    <input type="file" @change="updateAvatar" class="form-control mb-2">
                    <div v-if="nameError" class="error">{{ nameError }}</div>
                    <input type="text" v-model="editName" class="form-control mb-2" placeholder="New Name">
                    <div v-if="emailError" class="error">{{ emailError }}</div>
                    <input type="email" v-model="editEmail" class="form-control mb-2" placeholder="New Email">
                    <button class="btn btn-success" @click="saveProfile">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
    <input type="hidden" id="currentUserId" value="<?php echo $_SESSION['user']['unique_id']; ?>">
    <input type="hidden" id="currentUserName" value="<?php echo $user['name']; ?>">
    <input type="hidden" id="currentUserAvatar" value="<?php echo $user['avatar']; ?>">
    <input type="hidden" id="currentUserEmail" value="<?php echo $user['email']; ?>">
    <input type="hidden" id="currentUserStatus" value="<?php echo $user['status']; ?>">
</nav>
    


