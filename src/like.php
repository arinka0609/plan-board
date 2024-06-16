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
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : ''; // Получаем user_id из запроса
    $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : '';

    // Проверяем, что пользователь авторизован
    if ($user_id && $comment_id) {
        // Проверяем, не поставил ли пользователь уже лайк на этот комментарий
        $checkQuery = "SELECT * FROM likes WHERE user_id = '{$user_id}' AND comment_id = '{$comment_id}'";
        $checkResult = $conn->query($checkQuery);
        if ($checkResult->num_rows > 0) {
            // Если лайк уже поставлен, удаляем его
            $deleteQuery = "DELETE FROM likes WHERE user_id = '{$user_id}' AND comment_id = '{$comment_id}'";
            $conn->query($deleteQuery);

            // Уменьшаем количество лайков в таблице comments
            $updateCommentQuery = "UPDATE comments SET like_count = like_count - 1 WHERE com_id = '{$comment_id}'";
            $conn->query($updateCommentQuery);
        } else {
            // Если лайка еще нет, добавляем его
            $insertQuery = "INSERT INTO likes (user_id, comment_id) VALUES ('{$user_id}', '{$comment_id}')";
            $conn->query($insertQuery);

            // Увеличиваем количество лайков в таблице comments
            $updateCommentQuery = "UPDATE comments SET like_count = like_count + 1 WHERE com_id = '{$comment_id}'";
            $conn->query($updateCommentQuery);
        }

        // Подсчитываем количество лайков для комментария
        $countQuery = "SELECT like_count FROM comments WHERE com_id = '{$comment_id}'";
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
?>