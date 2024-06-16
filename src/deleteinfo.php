<?php
session_start();
require_once 'config.php'; // Подключение файла конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных из GET запроса
if (isset($_GET['info_id'])) {
    $newsId = $_GET['info_id'];

    // Генерация SQL запроса для удаления данных из базы данных
    $sql = "DELETE FROM info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $newsId);

    if ($stmt->execute()) {
        echo "Запись успешно удалена";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
} else {
    echo "ID записи для удаления не указан";
}

$conn->close();
?>