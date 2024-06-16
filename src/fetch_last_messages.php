<?php
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$currentUserId = $_GET['currentUserUniqueId']; // Изменено имя параметра
$selectedUserId = $_GET['targetUserUniqueId']; // Изменено имя параметра

// Запрос для получения последнего сообщения между пользователями
$sql = "SELECT * FROM messages WHERE 
            ((outgoing_msg_id = '$currentUserId' AND incoming_msg_id = '$selectedUserId') OR 
            (outgoing_msg_id = '$selectedUserId' AND incoming_msg_id = '$currentUserId'))
            ORDER BY msg_id DESC LIMIT 1"; // Выбираем только одну запись, отсортированную по убыванию msg_id

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Декодируем зашифрованное сообщение и ключ шифрования из базы данных
    $encrypted_message_with_nonce = base64_decode($row['msg']);
    $encryption_key = base64_decode($row['encryption_key']);

    // Извлекаем nonce из зашифрованного сообщения
    $nonce = substr($encrypted_message_with_nonce, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $encrypted_message = substr($encrypted_message_with_nonce, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

    // Расшифровываем сообщение
    $decrypted_message = sodium_crypto_secretbox_open($encrypted_message, $nonce, $encryption_key);
    if ($decrypted_message !== false) {
        // Если расшифровка прошла успешно, возвращаем расшифрованное сообщение в формате JSON
        echo json_encode(array("msg" => $decrypted_message));
    } else {
        // Если произошла ошибка при расшифровке, возвращаем ошибку
        echo json_encode(array("error" => "Ошибка при расшифровке сообщения"));
    }
} else {
    // Если сообщение не найдено, возвращаем пустой результат
    echo json_encode(null);
}

$conn->close();
?>