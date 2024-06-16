<?php
session_start();
require_once 'config.php'; // Файл конфигурации для подключения к БД

if (isset($_GET['block_id'])) {
    $block_id = intval($_GET['block_id']);

    // Подключение к базе данных
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Проверяем подключение к базе данных
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    // Удаляем записи из таблицы user_block по block_id
    $sql_delete_user_block = "DELETE FROM user_block WHERE block_id = $block_id";
    if (!$conn->query($sql_delete_user_block)) {
        die("Ошибка при удалении записей из user_block: " . $conn->error);
    }

    // Удаляем запись из таблицы block по полученному id
    $sql_delete_block = "DELETE FROM block WHERE id = $block_id";
    if (!$conn->query($sql_delete_block)) {
        die("Ошибка при удалении записи из block: " . $conn->error);
    }
    // Закрываем соединение с базой данных
    $conn->close();
    header("Location: /home.php");
} else {
    echo "Отсутствует обязательный параметр block_id";
}
?>