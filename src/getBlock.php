<?php
// Подключение к базе данных
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// SQL-запрос для получения данных о блоках
$sql = "SELECT * FROM block";

// Выполнение запроса
$result = $conn->query($sql);

// Проверка наличия данных
if ($result->num_rows > 0) {
    // Создаем массив для хранения данных о блоках
    $blocks = array();
    // Читаем данные из результата запроса
    while($row = $result->fetch_assoc()) {
        // Добавляем данные о блоке в массив
        $blocks[] = $row;
    }
    // Изменяем порядок элементов в массиве на обратный
    $blocks = array_reverse($blocks);
    // Отправляем данные в формате JSON
    echo json_encode($blocks);
} else {
    // Если данных нет, отправляем пустой массив
    echo json_encode(array());
}

// Закрытие соединения с базой данных
$conn->close();
?>