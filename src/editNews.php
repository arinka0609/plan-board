<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$response = [];

// Получение данных из POST запроса
$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];

// Проверяем, было ли загружено новое изображение
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    // Проверяем тип загруженного файла
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($_FILES['image']['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        $response['success'] = false;
        $response['message'] = "Только файлы JPG, PNG и GIF допустимы для загрузки.";
        echo json_encode($response);
        exit;
    }
    
    // Вызываем функцию uploadFile для перемещения загруженного изображения в нужное место
    $imagePath = uploadFile($_FILES['image']);
    // Генерация SQL запроса для обновления данных в базе данных с учетом нового изображения
    $sql = "UPDATE news SET title = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $imagePath, $id);
} else {
    // Генерация SQL запроса для обновления данных в базе данных без изменения изображения
    $sql = "UPDATE news SET title = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $description, $id);
}

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Record updated successfully";
    echo json_encode($response);
} else {
    $response['success'] = false;
    $response['message'] = "Error updating record: " . $conn->error;
    echo json_encode($response);
}

$stmt->close();
$conn->close();

// Функция для загрузки файла на сервер
function uploadFile(array $file, string $prefix = ''): string
{
    $uploadPath = __DIR__ . '/../uploads1'; // Путь к директории загрузки

    // Создание директории, если она не существует
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION); // Расширение файла
    $fileName = $prefix . '_' . time() . ".$ext"; // Составление имени файла

    // Перемещение загруженного файла
    if (!move_uploaded_file($file['tmp_name'], "$uploadPath/$fileName")) {
        die('Ошибка при загрузке файла на сервер');
    }

    return "uploads1/$fileName";
}
?>