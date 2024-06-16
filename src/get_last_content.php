<?php
// Подключение к базе данных
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$sql = "SELECT date, title FROM info ORDER BY date DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['date' => '', 'title' => 'Нет данных']);
}

$conn->close();
?>