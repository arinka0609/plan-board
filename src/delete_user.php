<?php
session_start();
require_once 'config.php'; // Файл конфигурации для подключения к БД
// Проверка, что запрос выполнен методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение ID пользователя из POST-запроса
    $userId = $_POST['id'];

    // Создание соединения
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Проверка соединения
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    // Подготовка SQL-запроса для удаления пользователя
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    // Выполнение запроса и проверка результата
    if ($stmt->execute()) {
        echo "Пользователь удален успешно.";
    } else {
        echo "Ошибка при удалении пользователя: " . $conn->error;
    }

    // Закрытие соединения и подготовленного выражения
    $stmt->close();
    $conn->close();
} else {
    echo "Неправильный метод запроса.";
}
?>