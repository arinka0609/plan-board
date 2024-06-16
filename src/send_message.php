<?php
// Подключение к базе данных
require_once 'config.php';

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных из формы
$incoming_msg_id = $_POST['incoming_id'];
$outgoing_msg_id = $_POST['outgoing_id'];
$message = $_POST['message'];

// Генерация ключа для шифрования
$encryption_key = sodium_crypto_secretbox_keygen();

// Генерация случайного nonce
$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

// Шифрование сообщения
$encrypted_message = sodium_crypto_secretbox($message, $nonce, $encryption_key);

// Преобразование nonce и зашифрованного сообщения в строку для сохранения в базе данных
$encrypted_message_with_nonce = base64_encode($nonce . $encrypted_message);

// Подготовка SQL запроса для вставки сообщения в базу данных
$sql = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, encryption_key, timestamp) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $incoming_msg_id, $outgoing_msg_id, $encrypted_message_with_nonce, base64_encode($encryption_key));

// Выполнение запроса
if ($stmt->execute()) {
    // Возвращаем данные нового сообщения
    $new_message_id = $stmt->insert_id;
    $sql = "SELECT * FROM messages WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $new_message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $new_message = $result->fetch_assoc();
    echo json_encode($new_message);
} else {
    echo "Ошибка при выполнении запроса: " . $conn->error;
}

// Закрытие запроса и соединения с базой данных
$stmt->close();
$conn->close();
?>