<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$response = [];

// Проверяем наличие обязательных данных
if (isset($_POST['title'], $_POST['color'], $_POST['color1'], $_POST['userIds'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $color = $conn->real_escape_string($_POST['color']);
    $color1 = $conn->real_escape_string($_POST['color1']); // Добавлено новое поле color1
    $userIds = $_POST['userIds']; // Массив идентификаторов пользователей
    $date = date("Y-m-d");

    // Добавляем запись в таблицу block
    $sql = "INSERT INTO block (title, date, color, color1, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt_block = $conn->prepare($sql);
    if (!$stmt_block) {
        $response['error'] = "Ошибка подготовки запроса: " . $conn->error;
    } else {
        // Собираем список пользователей через запятую
        $user_ids_str = implode(',', $userIds);
        $stmt_block->bind_param("sssss", $title, $date, $color, $color1, $user_ids_str); // Изменен тип параметров и добавлено поле color1
        if (!$stmt_block->execute()) {
            $response['error'] = "Ошибка выполнения запроса: " . $stmt_block->error;
        } else {
            // Получаем id добавленной записи
            $block_id = $stmt_block->insert_id;

            // Добавляем записи в таблицу user_block для каждого пользователя
            $sql_user_block = "INSERT INTO user_block (user_id, block_id) VALUES (?, ?)";
            $stmt_user_block = $conn->prepare($sql_user_block);
            if (!$stmt_user_block) {
                $response['error'] = "Ошибка подготовки запроса: " . $conn->error;
            } else {
                foreach ($userIds as $user_id) {
                    $user_id = intval($user_id); // Преобразуем к целому числу
                    $stmt_user_block->bind_param("ii", $user_id, $block_id);
                    if (!$stmt_user_block->execute()) {
                        $response['error'] = "Ошибка выполнения запроса: " . $stmt_user_block->error;
                        break; // Прерываем цикл в случае ошибки
                    }
                }
                $stmt_user_block->close();
                $response['message'] = "Новая запись создана успешно";
                $response['title'] = $title;
                $response['date'] = $date;
                $response['color'] = $color;
                $response['color1'] = $color1; // Добавлено поле color1
            }
        }
        $stmt_block->close();
    }
} else {
    $response['error'] = "Отсутствуют обязательные данные";
}

echo json_encode($response);

$conn->close();
?>