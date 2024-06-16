<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Функция для шифрования текста с использованием библиотеки Sodium
function encryptMessage($message, $key) {
    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox($message, $nonce, $key);
    return base64_encode($nonce . $ciphertext);
}

// Функция для расшифрования текста с использованием библиотеки Sodium
function decryptMessage($encryptedMessage, $key) {
    $decoded = base64_decode($encryptedMessage);
    $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
    return sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
}

// Обработка данных формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $comment = isset($_POST["comment"]) ? $_POST["comment"] : '';
    $unique_id = isset($_POST["unique_id"]) ? $_POST["unique_id"] : '';
    $info_id = isset($_POST["info_id"]) ? $_POST["info_id"] : ''; // Получаем info_id блока
    $date = date("Y-m-d H:i:s"); // Генерация текущей даты и времени в формате YYYY-MM-DD HH:MM:SS

    // Генерация ключа для шифрования
    $key = sodium_crypto_secretbox_keygen();

    if ($comment && $unique_id && $info_id && $date) { // Проверяем, что все данные присутствуют
        // Шифруем комментарий
        $encryptedComment = encryptMessage($comment, $key);

        // Подготовка и выполнение SQL-запроса для вставки комментария в базу данных
        $stmt = $conn->prepare("INSERT INTO comments (us_id, com, com_key, info_id, date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $unique_id, $encryptedComment, base64_encode($key), $info_id, $date);

        if ($stmt->execute()) {
            // Возвращаем сообщение об успешном добавлении комментария в виде JSON
            echo json_encode(['status' => 'success', 'message' => 'Комментарий успешно добавлен.']);
        } else {
            // Возвращаем сообщение об ошибке в виде JSON
            echo json_encode(['status' => 'error', 'message' => 'Ошибка при добавлении комментария: ' . $conn->error]);
        }

        // Закрытие подготовленного запроса
        $stmt->close();
    } else {
        // Возвращаем сообщение о недостаточности данных для добавления комментария в виде JSON
        echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните все поля.']);
    }
}