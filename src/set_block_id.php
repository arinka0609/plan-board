<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Проверка наличия POST запроса и ключа "blockId"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blockId'])) {
    // Получаем blockId из POST запроса
    $blockId = $_POST['blockId'];

    // Устанавливаем значение blockId в сессию
    $_SESSION['blockId'] = $blockId;

    // Здесь вы можете выполнить любые операции с полученным blockId, например, сохранить его в базу данных

    // Возвращаем успешный ответ клиенту
    echo "Block ID успешно установлен в сессию: $blockId";
} else {
    // Выводим сообщение об ошибке, если нет POST-запроса или отсутствует ключ "blockId"
    echo "Ошибка: неверный запрос.";
    exit;
}
?>