<?php

session_start(); // Начало сессии

require_once __DIR__ . '/config.php'; // Подключение файла конфигурации


// Функция для перенаправления на другую страницу
function redirect(string $path)
{
    header("Location: $path");
    die();
}

// Установка сообщения об ошибке валидации
function setValidationError(string $fieldName, string $message): void
{
    $_SESSION['validation'][$fieldName] = $message;
}

// Проверка наличия ошибки валидации для поля
function hasValidationError(string $fieldName): bool
{
    return isset($_SESSION['validation'][$fieldName]);
}

// Возвращает атрибут для поля с ошибкой валидации
function validationErrorAttr(string $fieldName): string
{
    return isset($_SESSION['validation'][$fieldName]) ? 'aria-invalid="true"' : '';
}

// Получение сообщения об ошибке валидации и его очистка из сессии
function validationErrorMessage(string $fieldName): string
{
    $message = $_SESSION['validation'][$fieldName] ?? '';
    unset($_SESSION['validation'][$fieldName]);
    return $message;
}

// Сохранение предыдущего значения поля в сессии
function setOldValue(string $key, mixed $value): void
{
    $_SESSION['old'][$key] = $value;
}

// Получение предыдущего значения поля и его очистка из сессии
function old(string $key)
{
    $value = $_SESSION['old'][$key] ?? '';
    unset($_SESSION['old'][$key]);
    return $value;
}

// Загрузка файла на сервер
function uploadFile(array $file, string $prefix = ''): string
{
    $uploadPath = __DIR__ . '/../uploads'; // Путь к директории загрузки

    // Создание директории, если она не существует
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION); // Расширение файла
    $fileName = $prefix . '_' . time() . ".$ext"; // Составление имени файла

    // Перемещение загруженного файла
    if (!move_uploaded_file($file['tmp_name'], "$uploadPath/$fileName")) {
        die('Ошибка при загрузке файла на сервер');
    }

    return "uploads/$fileName";
}



// Установка сообщения в сессии
function setMessage(string $key, string $message): void
{
    $_SESSION['message'][$key] = $message;
}

// Проверка наличия сообщения
function hasMessage(string $key): bool
{
    return isset($_SESSION['message'][$key]);
}

// Получение сообщения и его очистка из сессии
function getMessage(string $key): string
{
    $message = $_SESSION['message'][$key] ?? '';
    unset($_SESSION['message'][$key]);
    return $message;
}

// Получение объекта PDO для работы с БД
function getPDO(): PDO
{
    try {
        return new \PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8;dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
    } catch (\PDOException $e) {
        die("Connection error: {$e->getMessage()}");
    }
}

// Поиск пользователя по email
function findUser(string $email): array|bool
{
    $pdo = getPDO();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}


// Получение текущего пользователя
function currentUser(): array|false
{
    $pdo = getPDO();

    if (!isset($_SESSION['user'])) {
        return false;
    }

    $userId = $_SESSION['user']['id'] ?? null;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

// Выход пользователя из системы
function logout(): void
{
    session_start();

    // Проверяем, был ли пользователь в сессии
    if (isset($_SESSION['user'])) {
        // Получаем данные пользователя из сессии
        $user_id = $_SESSION['user']['id'];
        $unique_id = $_SESSION['user']['unique_id'];
        $logout_time = date('Y-m-d H:i:s');

        // Подключение к базе данных
        require_once 'config.php';

        $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Проверяем успешность подключения
        if ($conn->connect_error) {
            die("Ошибка подключения: " . $conn->connect_error);
        }

        // Записываем время выхода пользователя в таблицу user_logins
        $query = "UPDATE user_logins SET logout_time = ? WHERE user_id = ? AND unique_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sis', $logout_time, $user_id, $unique_id);
        $stmt->execute();
        $stmt->close();

        // Закрываем соединение с базой данных
        $conn->close();
    }

    // Удаляем информацию о пользователе из сессии
    unset($_SESSION['user']);

    // Очищаем сессию
    session_destroy();

    // Перенаправляем пользователя на страницу входа
    redirect('/index.php');
}

// Проверка, авторизован ли пользователь
function checkAuth(): void
{
    if (!isset($_SESSION['user']['id'])) {
        redirect('/');
    }
}

// Определение функции checkGuest без возвращаемого значения
function checkGuest(): void
{
    // Проверка на существование 'id' пользователя в массиве $_SESSION['user']
    if (isset($_SESSION['user']['id'])) {
        // Если 'id' существует, то пользователь считается авторизованным,
        // и происходит перенаправление на страницу /home.php
        
    }
    // В случае если 'id' не установлен, функция не делает ничего,
    // позволяя скрипту продолжить выполнение.
}





