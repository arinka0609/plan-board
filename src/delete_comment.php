<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение ID комментария из запроса
$data = json_decode(file_get_contents('php://input'), true);
$commentId = $data['commentId'];

// SQL запрос для удаления комментария
$sql = "DELETE FROM comments WHERE com_id = ?";

// Подготовка запроса
if ($stmt = $conn->prepare($sql)) {
    // Привязка параметров
    $stmt->bind_param("i", $commentId);

    // Выполнение запроса
    if ($stmt->execute()) {
        // Успешное удаление комментария
        echo json_encode(array('success' => true));
    } else {
        // Ошибка при выполнении запроса
        echo json_encode(array('success' => false, 'error' => 'Ошибка при выполнении запроса: ' . $stmt->error));
    }

    // Закрытие запроса
    $stmt->close();
} else {
    // Ошибка при подготовке запроса
    echo json_encode(array('success' => false, 'error' => 'Ошибка при подготовке запроса: ' . $conn->error));
}

// Закрытие подключения к базе данных
$conn->close();
?>