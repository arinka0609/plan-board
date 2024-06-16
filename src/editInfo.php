<?php
session_start();
require_once 'config.php'; // Файл конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных из POST запроса
$id = isset($_POST['id']) ? $_POST['id'] : null;
$title = isset($_POST['title']) ? $_POST['title'] : null;
$description = isset($_POST['description']) ? $_POST['description'] : null;
$link = isset($_POST['link']) ? $_POST['link'] : null;
$date1 = isset($_POST['date1']) ? $_POST['date1'] : null;
$contentType = isset($_POST['contentType']) ? $_POST['contentType'] : null;

// Обработка в зависимости от типа контента
$fileValue = null;
if ($contentType === 'image') {
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $fileValue = uploadFile($_FILES['image'], 'uploads1');
    }
} elseif ($contentType === 'file') {
    if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
        $fileValue = uploadFile($_FILES['file'], 'uploads2');
    }
} elseif ($contentType === 'link') {
    if (!empty($link)) {
        $fileValue = $link;
    }
}

// Если есть файл, обновляем поле 'file' в базе данных
if ($fileValue !== null) {
    // Получаем путь к старому файлу
    $oldFilePathQuery = "SELECT file FROM info WHERE id=?";
    $stmt = $conn->prepare($oldFilePathQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($oldFilePath);
        $stmt->fetch();
        
        // Удаляем старый файл, если он существует
        if ($oldFilePath && file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }
    }
    $stmt->close();

    // Обновляем поле 'file' в базе данных
    $updateFileQuery = "UPDATE info SET file=? WHERE id=?";
    $stmt = $conn->prepare($updateFileQuery);
    $stmt->bind_param("si", $fileValue, $id);
    $stmt->execute();
    $stmt->close();
}

// Генерация SQL запроса для обновления остальных данных в базе данных
$sql = "UPDATE info SET title=?, description=?, date1=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $title, $description, $date1, $id); // Исправлено количество типов данных

// Выполнение запроса
if ($stmt->execute()) {
    echo "Информация успешно обновлена";
} else {
    echo "Ошибка при обновлении информации: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Функция для загрузки файла на сервер
function uploadFile(array $file, string $prefix = ''): string
{
    $uploadPath = __DIR__ . '/../' . $prefix; // Путь к директории загрузки

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = $prefix . '_' . time() . ".$ext";

    if (!move_uploaded_file($file['tmp_name'], "$uploadPath/$fileName")) {
        die('Ошибка при загрузке файла на сервер');
    }

    return "$prefix/$fileName";
}
?>