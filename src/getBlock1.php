<?php
// Подключение к базе данных
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Проверяем, получен ли ID блока
if (isset($_GET['id'])) {
    $blockId = $_GET['id'];

    // SQL-запрос для получения данных о блоке по его ID
    $sql = "SELECT * FROM block WHERE id = $blockId";

    // Выполнение запроса
    $result = $conn->query($sql);

    // Проверка наличия данных
    if ($result->num_rows > 0) {
        // Получение данных о блоке
        $blockData = $result->fetch_assoc();

        // Отправка данных в формате JSON
        echo json_encode([$blockData]);
    } else {
        // Если блок не найден, возвращаем пустой массив
        echo json_encode([]);
    }
} else {
    // Если не получен ID блока, возвращаем сообщение об ошибке
    echo json_encode(['error' => 'Не получен ID блока']);
}

// Закрытие соединения с базой данных
$conn->close();
?>