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
$title = $_POST['title'];
$description = $_POST['description'];
$date = date("Y-m-d"); // Получаем текущую дату

// Проверяем, было ли загружено изображение
if (isset($_FILES['image'])) {
    // Вызываем функцию uploadFile для перемещения загруженного изображения в нужное место
    $imagePath = uploadFile($_FILES['image']);
} else {
    // Если изображение не было загружено, устанавливаем его значение как null или пустую строку (в зависимости от требований к базе данных)
    $imagePath = ""; // или $imagePath = null;
}

// Генерация SQL запроса для добавления данных в базу данных
$sql = "INSERT INTO news (title, description, image, date) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $title, $description, $imagePath, $date);

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