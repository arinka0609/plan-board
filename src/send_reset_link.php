<?php
require_once 'helpers.php';

session_start();
require_once 'config.php'; // Ваш файл конфигурации для подключения к БД

// Проверка соединения
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}


ini_set("SMTP", "smtp.example.com");
ini_set("smtp_port", "25");
ini_set("sendmail_from", "arina.boykova.2018@gmail.com");
$email = $_POST['email'] ?? null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setValidationError('email', 'Неверный формат электронной почты');
    redirect('/forgot_password.php');
}

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user) {
    setValidationError('email', 'Пользователь с такой электронной почтой не найден');
    redirect('/forgot_password.php');
}

$token = bin2hex(random_bytes(16));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

$stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires)");
$stmt->execute(['email' => $email, 'token' => $token, 'expires' => $expires]);

$resetLink = "http://yourdomain.com/reset_password.php?token=$token";
mail($email, 'Восстановление пароля', "Для восстановления пароля перейдите по ссылке: $resetLink");

setMessage('success', 'Ссылка для восстановления пароля отправлена на вашу электронную почту');
redirect('/forgot_password.php');
?>