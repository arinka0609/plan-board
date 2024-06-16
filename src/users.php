<?php
// Подключение к базе данных
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение ID текущего пользователя из параметра запроса
$currentUserId = $_GET['currentUserId'];

// SQL-запрос для получения данных о пользователях с исключением текущего пользователя
$sql = "SELECT id, name, avatar, unique_id FROM users WHERE unique_id != '$currentUserId' AND role != 1";

// Выполнение запроса к базе данных
$result = $conn->query($sql);

$users = array(); // Создаем массив для хранения данных о пользователях

if ($result) {
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            // Добавляем данные о пользователе в массив
            $users[] = array(
                'name' => $row['name'],
                'avatar' => $row['avatar'],
                'unique_id' => $row['unique_id'] // Добавляем поле unique_id
            );
        }
    }
    $result->close(); // Закрываем результат запроса
} else {
    // Обработка ошибки запроса
    die("Ошибка выполнения запроса: " . $conn->error);
}

$conn->close(); // Закрываем соединение с базой данных

// Отправляем данные о пользователях в формате JSON
echo json_encode($users);
?>