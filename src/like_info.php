<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (isset($_POST['like'])) {
    // Получаем данные из запроса
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $info_id = isset($_POST['info_id']) ? $_POST['info_id'] : '';

    // Проверяем, что пользователь авторизован
    if ($user_id && $info_id) {
        // Проверяем, не поставил ли пользователь уже лайк на этот блок информации
        $checkQuery = "SELECT * FROM likes1 WHERE user_id = '{$user_id}' AND info_id = '{$info_id}'";
        $checkResult = $conn->query($checkQuery);
        if ($checkResult->num_rows > 0) {
            // Если лайк уже поставлен, удаляем его
            $deleteQuery = "DELETE FROM likes1 WHERE user_id = '{$user_id}' AND info_id = '{$info_id}'";
            $conn->query($deleteQuery);

            // Уменьшаем количество лайков в таблице info
            $updateInfoQuery = "UPDATE info SET like_count = like_count - 1 WHERE id = '{$info_id}'";
            $conn->query($updateInfoQuery);
        } else {
            // Если лайка еще нет, добавляем его
            $insertQuery = "INSERT INTO likes1 (user_id, info_id) VALUES ('{$user_id}', '{$info_id}')";
            $conn->query($insertQuery);

            // Увеличиваем количество лайков в таблице info
            $updateInfoQuery = "UPDATE info SET like_count = like_count + 1 WHERE id = '{$info_id}'";
            $conn->query($updateInfoQuery);
        }

        // Подсчитываем количество лайков для блока информации
        $countQuery = "SELECT like_count FROM info WHERE id = '{$info_id}'";
        $countResult = $conn->query($countQuery);
        $like_count = $countResult->fetch_assoc()['like_count'];

        // Возвращаем количество лайков в виде JSON
        echo json_encode(['status' => 'success', 'like_count' => $like_count]);
        exit; // Прерываем выполнение скрипта после отправки JSON ответа
    } else {
        // Возвращаем сообщение об ошибке в виде JSON, если пользователь не авторизован или данные неполные
        echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, авторизуйтесь, чтобы поставить лайк.']);
        exit; // Прерываем выполнение скрипта после отправки JSON ответа
    }
}