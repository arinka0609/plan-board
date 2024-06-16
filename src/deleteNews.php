<?php
session_start();
require_once 'config.php'; // Файл конфигурации для подключения к БД

if (isset($_GET['news_id'])) {
    $newsId = intval($_GET['news_id']);

    // Подключение к базе данных
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Проверяем подключение к базе данных
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    // Генерация SQL запроса для удаления новости
    $sql = "DELETE FROM news WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $newsId);

    // Выполнение SQL запроса
    if ($stmt->execute()) {
        // Удаление успешно, теперь получаем обновленные данные о новостях

        // Запрос к базе данных для получения обновленных данных о новостях
        $sql_select = "SELECT id, image, date, title, description FROM news";
        $result = mysqli_query($conn, $sql_select);

        // Подготовка массива для хранения данных о новостях
        $newsData = array();

        // Проверка наличия данных
        if (mysqli_num_rows($result) > 0) {
            // Формируем массив данных о новостях
            while($row = mysqli_fetch_assoc($result)) {
                $newsData[] = $row;
            }
        }

        // Закрытие соединения с базой данных
        mysqli_close($conn);

        // Возвращаем данные о новостях в формате JSON
        header('Content-Type: application/json');
        echo json_encode($newsData);
    } else {
        // В случае ошибки при удалении новости
        echo "Ошибка при удалении новости: " . $conn->error;
    }

    // Закрытие подготовленного выражения
    $stmt->close();
} else {
    // Если идентификатор новости не был передан
    echo "Ошибка при удалении новости: Идентификатор новости не был передан";
}
?>