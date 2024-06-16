<?php

require_once __DIR__ . '/../helpers.php';

// Выносим данные из $_POST в отдельные переменные
$avatarPath = null;
$name = $_POST['name'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;
$passwordConfirmation = $_POST['password_confirmation'] ?? null;
$avatar = $_FILES['avatar'] ?? null;

// Проверка на уникальность имени
$pdo = getPDO();
$stmtName = $pdo->prepare("SELECT COUNT(*) FROM users WHERE name = :name");
$stmtName->execute(['name' => $name]);
$userNameExists = $stmtName->fetchColumn() > 0;

// Проверка на уникальность email
$stmtEmail = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
$stmtEmail->execute(['email' => $email]);
$userEmailExists = $stmtEmail->fetchColumn() > 0;

if ($userNameExists || $userEmailExists) {
    if ($userNameExists) {
        setValidationError('name', 'Пользователь с таким именем уже существует');
        setOldValue('name', $name);
    }
    if ($userEmailExists) {
        setValidationError('email', 'Пользователь с таким адресом электронной почты уже существует');
        setOldValue('email', $email);
    }
    redirect('/register.php');
}

// Генерация уникального unique_id
$uniqueId = uniqid();

// Установка статуса active
$status = 'active';

// Выполняем валидацию полученных данных с формы
if (empty($name)) {
    setValidationError('name', 'Неверный формат имени');
} elseif (!preg_match('/[a-zA-Zа-яА-Я]/', $name)) {
    setValidationError('name', 'Имя должно содержать хотя бы одну букву');
}

$email_domains_pattern = '/@(yandex\.ru|mail\.ru|inbox\.ru|bk\.ru|hotmail\.com|live\.com|xakep\.ru|furmail\.ru|gmail\.com)$/i';

if (!preg_match($email_domains_pattern, $email)) {
    setValidationError('email', 'Домен электронной почты не допустим');
}

if (empty($password)) {
    setValidationError('password', 'Обязательное поле');
} elseif (strlen($password) < 8) { // Проверка минимальной длины пароля
    setValidationError('password', 'Пароль должен содержать минимум 8 символов');
} elseif (!preg_match('/[A-ZА-Я]/u', $password) || !preg_match('/[a-zа-я]/u', $password)) { // Проверка наличия хотя бы одной заглавной и одной строчной буквы в английском и русском алфавите
    setValidationError('password', 'Пароль должен содержать хотя бы одну прописную и одну заглавную букву в английском или русском алфавите');
}

if ($password !== $passwordConfirmation) {
    setValidationError('password', 'Пароли не совпадают');
}

if (!empty($avatar)) {
    $types = ['image/jpeg', 'image/png'];
    if (!in_array($avatar['type'], $types)) {
        setValidationError('avatar', 'Изображение профиля имеет неверный тип');
    }
    if (($avatar['size'] / 1000000) >= 1) {
        setValidationError('avatar', 'Изображение должно быть меньше 1 мб');
    }
}

// Если список с ошибками валидации не пустой, то производим редирект обратно на форму
if (!empty($_SESSION['validation'])) {
    setOldValue('name', $name);
    setOldValue('email', $email);
    redirect('/register.php');
}

// Загружаем аватарку, если она была отправлена в форме
if (!empty($avatar)) {
    $avatarPath = uploadFile($avatar, 'avatar');
}

// Обновленный запрос с полями unique_id и status
$query = "INSERT INTO users (name, email, avatar, password, role, unique_id, status) 
          VALUES (:name, :email, :avatar, :password, :role, :unique_id, :status)";

$params = [
    'name' => $name,
    'email' => $email,
    'avatar' => $avatarPath,
    'password' => sodium_crypto_pwhash_str($password, SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE, SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE),
    'role' => 3, // Автоматическое присвоение роли с ID 3
    'unique_id' => $uniqueId,
    'status' => $status
];

$stmt = $pdo->prepare($query);

try {
    $stmt->execute($params);
} catch (\Exception $e) {
    die($e->getMessage());
}

redirect('/index1.php');