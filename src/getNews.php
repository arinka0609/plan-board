<?php
// Подключение к базе данных
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// SQL-запрос для получения данных о новости по id
$sql = "SELECT * FROM news WHERE id = ?";

// Подготовленный запрос
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['id']); // Передача параметра id из запроса

// Выполнение запроса
$stmt->execute();

// Получение результата
$result = $stmt->get_result();

// Проверка наличия данных
if ($result->num_rows > 0) {
    // Получение данных о новости
    $news = $result->fetch_assoc();
    // Возвращение данных о новости в формате JSON
    echo json_encode($news);
} else {
    // Если данных нет, возвращаем пустой массив
    echo json_encode(array());
}

// Закрытие соединения с базой данных
$conn->close();