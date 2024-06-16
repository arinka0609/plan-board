<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Проверяем наличие параметра id в запросе
if (!isset($_GET['id'])) {
    // Если id не указан, возвращаем пустой JSON объект
    echo json_encode(array());
    exit; // Завершаем выполнение скрипта
}

// Получаем id блока из запроса
$id = $_GET['id'];

// Создаем соединение с базой данных
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Генерируем SQL запрос для получения данных о блоке по его id
$sql = "SELECT * FROM info WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Проверяем, есть ли результаты
if ($result->num_rows > 0) {
    // Преобразуем результат в ассоциативный массив
    $row = $result->fetch_assoc();
    
    // Добавляем путь к изображению к возвращаемым данным, если изображение существует
    if (!empty($row['image'])) {
        $row['imageSrc'] = 'uploads1/' . $row['image']; // Замените 'uploads1/' на путь к папке с вашими изображениями
    }
    
    // Отправляем данные в формате JSON
    echo json_encode($row);
} else {
    // Если данных нет, отправляем пустой JSON объект
    echo json_encode(array());
}

// Закрываем соединение с базой данных
$stmt->close();
$conn->close();
?>