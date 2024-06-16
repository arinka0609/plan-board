<?php
// Подключение к базе данных
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
// Получение уникального идентификатора пользователя из GET-запроса
$unique_id = $_GET['unique_id'];

// Запрос к базе данных для проверки статуса входа пользователя
$sql = "SELECT * FROM user_logins WHERE unique_id = '$unique_id' AND logout_time IS NULL";
$result = $conn->query($sql);

// Проверка наличия незавершенной сессии входа для пользователя
if ($result->num_rows > 0) {
    // Если есть незавершенная сессия входа, возвращаем статус "вошел"
    $response = array("logged_in" => true);
    echo json_encode($response);
} else {
    // Если незавершенной сессии входа нет, возвращаем статус "вышел"
    $response = array("logged_in" => false);
    echo json_encode($response);
}

// Закрытие соединения с базой данных
$conn->close();
?>