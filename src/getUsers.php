<?php

header('Content-Type: application/json');
require_once __DIR__ . '/config.php'; // Подключение файла конфигурации


try {
    $pdo = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE role = 3");
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}