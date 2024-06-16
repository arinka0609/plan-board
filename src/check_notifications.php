<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Запрос к таблице info
$sql = "SELECT block_id FROM info WHERE `date` >= NOW() - INTERVAL 2 SECOND";

$result = $conn->query($sql);

$newNotifications = array();
// Получаем новые уведомления и добавляем в массив
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $newNotifications[] = $row['block_id'];
  }
}

// Возвращаем массив новых уведомлений в формате JSON
echo json_encode($newNotifications);

$conn->close();
?>