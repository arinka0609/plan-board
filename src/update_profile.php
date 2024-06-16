<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения: ' . $conn->connect_error]));
}

// Проверка наличия идентификатора пользователя в сессии
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID not found in session']);
    exit();
}

$user_id = $_SESSION['user']['id'];

// Проверка наличия данных в POST
if (!isset($_POST['name']) || !isset($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Name or email not provided']);
    exit();
}

$name = $_POST['name'];
$email = $_POST['email'];

$avatar = '';
$uploadPath = __DIR__ . '/../uploads'; // Путь к директории загрузки

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $fileName = 'avatar_' . time() . ".$ext"; // Формируем новое имя файла
    $filePath = $uploadPath . '/' . $fileName; // Полный путь к файлу
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath)) {
        $avatar = 'uploads/' . $fileName; // Сохраняем путь для хранения в БД
    }
}

// Обновление данных пользователя в базе данных
$sql = "UPDATE users SET name=?, email=?, avatar=? WHERE id=?";
$stmt = $conn->prepare($sql);

// Если аватар не обновляется, используем старое значение
if (empty($avatar)) {
    // Получаем текущее значение аватара
    $avatarQuery = "SELECT avatar FROM users WHERE id=?";
    $avatarStmt = $conn->prepare($avatarQuery);
    $avatarStmt->bind_param('i', $user_id);
    $avatarStmt->execute();
    $result = $avatarStmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $avatar = $row['avatar'];
    }
    $avatarStmt->close();
}

$stmt->bind_param('sssi', $name, $email, $avatar, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully', 'avatar' => $avatar]);
} else {
    echo json_encode(['success' => false, 'message' => 'Profile update failed']);
}

$stmt->close();
$conn->close();
?>