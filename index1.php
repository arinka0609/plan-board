<?php
require_once __DIR__ . '/src/helpers.php';

checkGuest();
?>

<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>PlanBoard</title>
    <link rel="stylesheet" href="assets/login.css">
</head>
<body>

<form class="card" action="src/actions/login.php" method="post">
    <h2>Личный кабинет</h2>

    <?php if(hasMessage('error')): ?>
        <div class="notice error"><?php echo getMessage('error') ?></div>
    <?php endif; ?>

    <label for="email">
        Email
        <input
            type="text"
            id="email"
            name="email"
            placeholder="mail@mail.ru"
            value="<?php echo old('email') ?>"
            <?php echo validationErrorAttr('email'); ?>
        >
        <?php if(hasValidationError('email')): ?>
            <small class="error"><?php echo validationErrorMessage('email'); ?></small>
        <?php endif; ?>
    </label>

    <label for="password">
        Пароль
        <input
            type="password"
            id="password"
            name="password"
            placeholder="******"
        >
    </label>

    <button type="submit" id="submit">Вход</button>
    <p>У меня еще нет <a href="/register.php">аккаунта</a></p>
    <p><a href="/index.php">Перейти на главную страницу</a></p>
    <p><a href="/forgot_password.php">Восстановить пароль</a></p> <!-- Ссылка на страницу восстановления пароля -->
</form>
</body>
</html>