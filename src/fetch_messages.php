<?php
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$currentUserId = $_GET['outgoing_msg_id']; // Изменено имя параметра
$selectedUserId = $_GET['incoming_msg_id']; // Изменено имя параметра

$sql = "SELECT * FROM messages WHERE 
            ((outgoing_msg_id = '$currentUserId' AND incoming_msg_id = '$selectedUserId') OR 
            (outgoing_msg_id = '$selectedUserId' AND incoming_msg_id = '$currentUserId'))
        ORDER BY timestamp ASC"; // Сортировка по времени возрастанию
$result = $conn->query($sql);

$messages = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Декодируем зашифрованное сообщение и ключ шифрования из базы данных
        $encrypted_message_with_nonce = base64_decode($row['msg']);
        $encryption_key = base64_decode($row['encryption_key']);

        // Извлекаем nonce из зашифрованного сообщения
        $nonce = substr($encrypted_message_with_nonce, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted_message = substr($encrypted_message_with_nonce, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        // Расшифровываем сообщение
        $decrypted_message = sodium_crypto_secretbox_open($encrypted_message, $nonce, $encryption_key);
        if ($decrypted_message !== false) {
            // Если расшифровка прошла успешно, добавляем сообщение в массив
            $row['msg'] = $decrypted_message;
            $messages[] = $row;
        } else {
            // Если произошла ошибка при расшифровке, логируем ее или обрабатываем по вашему усмотрению
            error_log("Ошибка при расшифровке сообщения: " . $conn->error);
        }
    }
}

// Возвращаем массив сообщений в формате JSON
echo json_encode($messages);

$conn->close();
?>