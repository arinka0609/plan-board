<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (isset($_POST['info_id'])) {
    $info_id = $_POST['info_id'];

    // Выполните запрос к базе данных для получения списка пользователей, поставивших лайк
    $query = "SELECT users.name FROM likes1 INNER JOIN users ON likes1.user_id = users.id WHERE likes1.info_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $info_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Создаем массив для хранения имен пользователей
    $like_users = [];
    while ($row = $result->fetch_assoc()) {
        $like_users[] = $row['name'];
    }

    // Отправьте список пользователей в формате JSON
    echo json_encode($like_users);
} else {
    echo json_encode(['error' => 'No info_id provided']);
}
?>