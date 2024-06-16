<?php
// Включаем файл с функциями и проверкой авторизации
require_once __DIR__ . '/src/helpers.php';

// Проверяем авторизацию пользователя
checkAuth();

// Получаем текущего пользователя
$user = currentUser();

// Подключение к базе данных
require_once __DIR__ . '/src/config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="../assets/js/color-modes.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>Страница администратора</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/dashboard/">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/album/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   

<link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Custom styles for this template -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
   
   
  </head>
  <body>
  <div class="loader show">
  <div class="loader-inner">
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
    <div class="loader-line-wrap">
      <div class="loader-line"></div>
    </div>
  </div>
</div>
<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-25 text-white" href="#">PlanBoard</a>
  <ul class="navbar-nav flex-row d-md-none">
    <li class="nav-item text-nowrap">
      <button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <svg class="bi"><use xlink:href="#list"/></svg>
      </button>
    </li>
  </ul>
</header>


<input type="hidden" id="currentUserId" value="<?php echo $user['unique_id']; ?>">
<div class="container-fluid">
    <div class="row">
        <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary"  id="openModal">
            <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="sidebarMenuLabel">PlanBoard</h5>
                    <button type="button" id="sidebarMenuCloseButton" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" href="#" onclick="showSection('newsSection', 'Все доски')">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                                Просмотр досок
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" href="#" onclick="showSection('boardSection', 'Добавление блока доски')">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                                Добавить блок доски
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" href="#" onclick="showSection('contentSection', 'Добавление контента')">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                                Добавить контент
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" href="#" onclick="showSection('userSection', 'Управление пользователями')">
                            <i class="bi bi-person-fill"></i>
                                Управление пользователями
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" href="#" @click="openChatModal">
                                <i class="bi bi-chat"></i>
                                Сообщения
                            </a>
                        </li>
                    </ul>
                    <ul class="nav flex-column mb-auto">
                        <li class="nav-item">
                            <form id="logout-form" action="src/actions/logout.php" method="post" style="display: none;">
                                <button type="submit" id="logout-button"></button>
                            </form>
                            <a href="#" class="nav-link d-flex align-items-center gap-2" onclick="document.getElementById('logout-button').click(); return false;">
                                <svg class="bi"><use xlink:href="#door-closed"/></svg>
                                <span>Выйти</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php include 'header.php'; ?>
        </div>
        
        



  






    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <h1 class="h2" id="sectionTitle"></h1>
    <div id="newsSection" class="page-section">
        <form>
            <div class="album py-5 bg-body-tertiary">
                <div class="container">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="app-block">
                        <div v-for="(block, index) in blocks" :key="block.id" class="col">
                            <div class="col mb-5" v-if="blocks.length === 0">
                                <h2 style="color: Gray">Нет доступных досок...</h2>
                            </div>
                            <div class="card shadow-sm">
                                <div class="card-img-top-wrapper">
                                    <div class="card-img-top" :style="{ backgroundColor: block.color }">
                                        <span class="text">{{ block.title }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="redirectToSpecificPage(block.id)">Перейти</button>
                                        </div>
                                        <small class="text-body-secondary">{{ block.date }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"></div>

    <div id="boardSection" class="page-section">
    <div id="app">
        <form method="post" action="addblock.php" @submit.prevent="submitForm" class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <h6>Тема блока</h6>
                    <input id="title" type="text" class="form-control" name="title" v-model="productData.title" placeholder="Тема проекта" required>
                </div>
                <div class="form-group">
                    <h6>Цвет блока</h6>
                    <input id="color" type="color" list="colorList" v-model="productData.color">
                    <datalist id="colorList">
                        <option value="#ff0000" label="Красный"></option>
                        <option value="#008000" label="Зелёный"></option>
                        <option value="#0000ff" label="Синий"></option>
                    </datalist>
                </div>
                <div class="form-group">
                    <h6>Цвет текста</h6>
                    <input id="color1" type="color" list="colorList1" v-model="productData.color1">
                    <datalist id="colorList1">
                        <option value="#ff0000" label="Красный"></option>
                        <option value="#008000" label="Зелёный"></option>
                        <option value="#0000ff" label="Синий"></option>
                    </datalist>
                </div>
                <div class="form-group">
                    <h6>Выберите пользователей, которые увидят блок</h6>
                </div>
            </div>
            <div class="col-md-6">
                <div class="scroll">
                    <input type="text" v-model="productData.searchQuery" class="form-control" placeholder="Поиск...">
                    <div class="d-flex gap-2 py-3 justify-content-end">
                        <div class="button-check">
                            <button class="btn btn-primary rounded-pill px-3" type="button" @click.stop="toggleSelectAllUsers">Выбрать</button>
                            <button class="btn btn-secondary rounded-pill px-3" type="button" @click.stop="deselectAllUsers">Сбросить</button>
                        </div>
                    </div>
                    <div class="list-group" v-for="user in filteredUsers" :key="user.id">
                        <label class="list-group-item d-flex gap-2">
                            <input class="form-check-input flex-shrink-0" type="checkbox" :id="'user' + user.id" :value="user.id" @change="toggleUserSelection(user.id)" :checked="selectedUsersState[user.id]">
                            <span>
                                <label :for="'user' + user.id">{{ user.email }}</label>
                                <small class="d-block text-body-secondary">Этот пользователь будет видеть добавленную информацию</small>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xxl-4 my-5 mx-auto">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit">Добавить</button>
                </div>
            </div>
        </form>
        <div v-if="operationStatus === 'success'" class="notification success">
            {{ operationMessage }}
        </div>
        <div v-if="operationStatus === 'error'" class="notification error">
            {{ operationMessage }}
        </div>

        <!-- Модальное окно для редактирования блока -->
        <div class="modal fade" id="editBlockModal" tabindex="-1" aria-labelledby="editBlockModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBlockModalLabel">Редактировать блок</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeEditModal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" @submit.prevent="submitEditForm">
                            <div class="form-group">
                                <label for="edit-title">Тема проекта</label>
                                <input id="edit-title" type="text" class="form-control" name="title" v-model="editedBlock.title" placeholder="Тема" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-color">Цвет новостного блока</label>
                                <input id="edit-color" type="color" list="edit-colorList" v-model="editedBlock.color">
                                <datalist id="edit-colorList">
                                    <option value="#ff0000" label="Красный"></option>
                                    <option value="#008000" label="Зелёный"></option>
                                    <option value="#0000ff" label="Синий"></option>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="edit-color1">Цвет текста</label>
                                <input id="edit-color1" type="color" list="edit-colorList1" v-model="editedBlock.color1">
                                <datalist id="edit-colorList1">
                                    <option value="#ff0000" label="Красный"></option>
                                    <option value="#008000" label="Зелёный"></option>
                                    <option value="#0000ff" label="Синий"></option>
                                </datalist>
                            </div>
                            <div class="form-group">
                                <h6>Выберите пользователей, которые увидят блок</h6>
                                <div class="scroll" >
                                    <input type="text" v-model="productData.searchQuery" class="form-control" placeholder="Поиск...">
                                    <p></p>
                                    <div class="button-check">
                                        <button class="btn btn-primary rounded-pill px-3" type="button" @click.stop="toggleSelectAllUsers">Выбрать</button>
                                        <button class="btn btn-secondary rounded-pill px-3" type="button" @click.stop="deselectAllUsers">Сбросить</button>
                                    </div>
                                    <div v-for="user in filteredUsers" :key="user.id" class="checkbox">
                                        <input type="checkbox" :id="'edit-user' + user.id" :value="user.id" @change="toggleUserSelection(user.id)" :checked="selectedUsersState[user.id]">
                                        <label :for="'edit-user' + user.id">{{ user.email }}</label>
                                    </div>
                                </div>
                            </div>
                            <p></p>
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                            <button type="button" class="btn btn-secondary" @click="closeEditModal">Отмена</button>
                        </form>
                        <div v-if="editOperationStatus === 'success'" class="notification success">
                            {{ editOperationMessage }}
                        </div>
                        <div v-if="editOperationStatus === 'error'" class="notification error">
                            {{ editOperationMessage }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2>Данные о добавленных блоках</h2>
        <?php
        // Подключение к базе данных
        require_once __DIR__ . '/src/config.php';

        // Создание соединения
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Проверка соединения
        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        // Запрос к базе данных
        $sql = "SELECT id, title, date, color, color1 FROM block";
        $result = mysqli_query($conn, $sql); // Замените $connection на $conn

        // Проверка наличия данных
        if (mysqli_num_rows($result) > 0) {
            // Вывод данных в таблицу
            echo "<table class='table table-striped table-sm'>";
            echo "<thead><tr><th scope='col'>Id</th><th scope='col'>Title</th><th scope='col'>Date</th><th scope='col'>Color</th><th scope='col'>Color1</th><th scope='col'>Actions</th></tr></thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr><td>" . $row["id"] . "</td><td>" . $row["title"] . "</td><td>" . $row["date"] . "</td><td>" . $row["color"] . "</td><td>" . $row["color1"] . "</td>";
                echo "<td><button class='btn btn-danger btn-sm' onclick='deleteBlock(" . $row['id'] . ")'>Удалить</button> <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#editBlockModal' @click=\"openEditModal(" . $row['id'] . ")\">Редактировать</button></td></tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "0 результатов";
        }

        // Закрытие соединения с базой данных
        mysqli_close($conn);
        ?>
    </div>
</div>










  
<div id="contentSection" class="page-section" style="display: none;">
    <div id="app-block4">
        <form @submit.prevent="submitForm">
            <h3>Введите нужные данные</h3>
            <div class="row">
                <div class="col-md-6">
                    <h6>Выберите доску для добавления контента на нее</h6>
                    <select class="form-select" aria-label="Выберите доску" v-model="blockId" required>
                        <?php
                        // Подключение к базе данных
                        require_once __DIR__ . '/src/config.php';

                        // Создание соединения
                        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

                        // Проверка соединения
                        if ($conn->connect_error) {
                            die("Ошибка подключения: " . $conn->connect_error);
                        }

                        // Запрос к базе данных
                        $sql = "SELECT id, title FROM block";
                        $result = mysqli_query($conn, $sql);

                        // Вывод опций выпадающего списка
                        if (mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<option value=\"" . $row['id'] . "\">" . $row['title'] . "</option>";
                            }
                        }

                        // Закрытие соединения с базой данных
                        mysqli_close($conn);
                        ?>
                    </select>
                    <div class="form-group">
                        <h6>Напишите нужную тему</h6>
                        <input id="title" type="text" class="form-control" v-model="productData.title" placeholder="Тема" required>
                    </div>
                    <div class="form-group">
                        <h6>Выберите нужный вид контента</h6>
                        <div class="form_radio_btn">
                            <input type="radio" id="image_type" name="content_type" value="image" v-model="contentType">
                            <label for="image_type">Изображение</label><br>
                            <input type="radio" id="file_type" name="content_type" value="file" v-model="contentType">
                            <label for="file_type">Файл</label><br>
                            <input type="radio" id="link_type" name="content_type" value="link" v-model="contentType">
                            <label for="link_type">Ссылка</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <textarea id="description" class="form-control" v-model="productData.description" style="width: 100%; height: 150px;" placeholder="Напишите информацию о новости..." required></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div v-if="contentType === 'image'">
                            <label for="image">Изображение:</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*" @change="handleFileUpload">
                        </div>
                        <div v-if="contentType === 'file'">
                            <label for="file">Файл:</label>
                            <input type="file" id="file" name="file" class="form-control" accept=".doc,.docx,.pdf" @change="handleFileUpload">
                        </div>
                        <div v-if="contentType === 'link'">
                            <label for="link">Ссылка:</label>
                            <input type="url" id="link" name="file" class="form-control" v-model="productData.link" placeholder="https://example.com">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <h6>Выберите дату дедлайна</h6>
                <input type="date" id="start" name="trip-start" min="2018-01-01" max="2025-12-31" v-model="productData.date1">
            </div>
            <div class="col-lg-6 col-xxl-4 my-5 mx-auto">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit">Добавить</button>
                </div>
            </div>
            <div v-if="operationStatus === 'success'" class="notification success">
                {{ operationMessage }}
            </div>
            <div v-if="operationStatus === 'error'" class="notification error">
                {{ operationMessage }}
            </div>
        </form>
        <div class="modal fade" id="editContentModal" tabindex="-1" aria-labelledby="editContentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editContentModalLabel">Редактировать контент</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeEditModal"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitEditForm" action="/src/editInfo.php">
                            <div class="mb-3">
                                <label for="edit-title" class="form-label">Заголовок</label>
                                <input type="text" class="form-control" id="edit-title" v-model="editData.title">
                            </div>
                            <div class="mb-3">
                                <a :href="editData.file" id="edit-current-file" target="_blank">Просмотреть уже загруженный контент</a>
                                <label class="form-label">Тип контента</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="edit-image_type" value="image" v-model="editData.contentType">
                                    <label class="form-check-label" for="edit-image_type">Изображение</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="edit-file_type" value="file" v-model="editData.contentType">
                                    <label class="form-check-label" for="edit-file_type">Файл</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="edit-link_type" value="link" v-model="editData.contentType">
                                    <label class="form-check-label" for="edit-link_type">Ссылка</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-description" class="form-label">Описание</label>
                                <textarea class="form-control" id="edit-description" rows="3" v-model="editData.description"></textarea>
                            </div>
                            <div v-if="editData.contentType === 'link'" class="mb-3">
                                <label for="edit-link" class="form-label">Текущая ссылка</label>
                                <input type="url" class="form-control" id="edit-link" v-model="editData.link" placeholder="https://example.com">
                            </div>
                            <div v-if="editData.contentType === 'file'" class="mb-3">
                                <input type="file" class="form-control" id="edit-file" accept=".doc,.docx,.pdf" @change="handleFileUploadForFile">
                            </div>
                            <div v-if="editData.contentType === 'image'" class="mb-3">
                                <img :src="editData.imageSrc" id="edit-current-image" style="max-width: 100%;">
                                <input type="file" class="form-control" id="edit-image" name="image" accept="image/*" @change="handleImageUpload">
                            </div>
                            <div class="mb-3">
                                <label for="edit-date1" class="form-label">Дата дедлайна</label>
                                <input type="date" class="form-control" id="edit-date1" v-model="editData.date1">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="closeEditModal">Отмена</button>
                                <button type="submit" class="btn btn-primary" @click="submitEditForm">Сохранить</button>
                            </div>
                            <div v-if="operationStatus === 'success'" class="notification success">
                                {{ operationMessage }}
                            </div>
                            <div v-if="operationStatus === 'error'" class="notification error">
                                {{ operationMessage }}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        // Подключение к базе данных
        require_once __DIR__ . '/src/config.php';

        // Создание соединения
        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Проверка соединения
        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        // Запрос к базе данных
        $sql = "SELECT id, block_id, file, date, date1, title, description FROM info";
        $result = $conn->query($sql);

        ?>
        <h2>Данные о добавленном контенте</h2>
        <div class="table-responsive small">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">block Id</th>
                        <th scope="col">File</th>
                        <th scope="col">Date</th>
                        <th scope="col">Date1</th>
                        <th scope="col">Title</th>
                        <th scope="col">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['block_id']; ?></td>
                                <td><?php echo $row['file']; ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td><?php echo $row['date1']; ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <?php echo "<td><button class='btn btn-danger btn-sm' onclick='deleteCont(" . $row["id"] . ")'>Удалить</button> <button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#editModal' @click=\"openEditModal(" . $row['id'] . ")\">Редактировать</button></td></tr>";?>
                            </tr>
                            <?php
                        }

                    } else {
                        ?>
                        <tr>
                            <td colspan="8">Нет данных для отображения</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        // Закрытие соединения с базой данных
        $conn->close();
        ?>

    </div>
</div>


<div id="userSection" class="page-section" style="display: none;">
    <h2>Данные пользователей</h2>
    <div class="table-responsive small">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Подключение к базе данных
                require_once __DIR__ . '/src/config.php';

                // Создание соединения
                $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

                // Проверка соединения
                if ($conn->connect_error) {
                    die("Ошибка подключения: " . $conn->connect_error);
                }

                // Запрос к базе данных
                $sql = "SELECT id, name, email FROM users WHERE role NOT IN (1, 2)";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td>
                                <button class='btn btn-danger btn-sm' onclick='deleteUser(<?php echo $row["id"]; ?>)'>Удалить</button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="3">Нет данных для отображения</td>
                    </tr>
                    <?php
                }
                // Закрытие соединения с базой данных
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>








    <canvas class="my-4 w-100" id="myChart" width="900" height="380"></canvas>
</main>

  </div>
</div>
<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<?php include 'script.php'; ?>
<?php include 'symbol.php'; ?>
<?php include 'color.php'; ?>

</html>
