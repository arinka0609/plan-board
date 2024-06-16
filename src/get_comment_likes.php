
<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Создание соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$commentId = $data['comment_id'];

$sql = "SELECT users.name FROM likes
        JOIN users ON likes.user_id = users.id
        WHERE likes.comment_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $commentId);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>