<?php

require_once __DIR__ . '/../helpers.php';

// Подключение к базе данных
require_once __DIR__ . '/../config.php'; // Предполагаем, что файл `db_connection.php` содержит код для подключения к базе данных
   // Подключение к базе данных
   $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

   // Проверяем подключение к базе данных
   if ($conn->connect_error) {
       die("Ошибка подключения: " . $conn->connect_error);
   }

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setOldValue('email', $email);
    setValidationError('email', 'Неверный формат электронной почты');
    setMessage('error', 'Ошибка валидации');
    redirect('/index1.php');
}

$user = findUser($email);

if (!$user) {
    setMessage('error', "Данные не найдены");
    redirect('/index1.php');
}

if (!password_verify($password, $user['password'])) {
    setMessage('error', 'Данные не найдены');
    redirect('/index1.php');
}

$_SESSION['user']['id'] = $user['id'];
$_SESSION['user']['role'] = $user['role'];
$_SESSION['user']['unique_id'] = $user['unique_id'];

// Запись информации о входе пользователя в таблицу `user_logins`
$user_id = $user['id'];
$unique_id = $user['unique_id'];
$login_time = date('Y-m-d H:i:s');

$query = "INSERT INTO user_logins (user_id, unique_id, login_time) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('iss', $user_id, $unique_id, $login_time);

if ($stmt->execute()) {
    // Успешная запись
    $stmt->close();
} else {
    // Обработка ошибки записи
    error_log('Ошибка записи информации о входе пользователя: ' . $stmt->error);
    setMessage('error', 'Ошибка записи информации о входе');
    redirect('/index1.php');
}

// Перенаправление на разные страницы в зависимости от роли пользователя
switch ($_SESSION['user']['role']) {
    case 1:
        redirect('/home1.php');
        break;
    case 2:
        redirect('/homeruk.php');
        break;
    case 3:
        redirect('/homeuser.php');
        break;
    default:
        redirect('/'); // Перенаправляем на главную страницу или страницу ошибки
        break;
}
?>