<?php
session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных из POST запроса
$title = $_POST['title'];
$description = $_POST['description'];
$blockId = $_POST['block_id'];
$date1 = $_POST['date1'];

// Проверка наличия изображения
if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    $imagePath = uploadFile($_FILES['image'], 'uploads1');
    $fileValue = $imagePath;
} elseif (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
    $filePath = uploadFile($_FILES['file'], 'uploads2');
    $fileValue = $filePath;
} else {
    if (isset($_POST['link'])) {
        $fileValue = $_POST['link'];
    } else {
        $fileValue = ''; // или любое другое значение по умолчанию
    }
}

// Генерация SQL запроса для добавления данных в базу данных
$sql = "INSERT INTO info (block_id, file, title, description, date1, `date`) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $blockId, $fileValue, $title, $description, $date1); 

if ($stmt->execute()) {
    echo "Record added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$stmt->close();
$conn->close();

// Функция для загрузки файла на сервер
function uploadFile(array $file, string $prefix = ''): string
{
    $uploadPath = __DIR__ . '/../' . $prefix; // Путь к директории загрузки

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

    return "$prefix/$fileName";
}
?>