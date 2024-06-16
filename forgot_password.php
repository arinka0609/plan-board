<?php
require_once __DIR__ . '/src/helpers.php';

checkGuest();
?>


<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Восстановление пароля - PlanBoard</title>
    <link rel="stylesheet" href="assets/login.css">
</head>
<body>

<form class="card" action="/src/send_reset_link.php" method="post">
    <h2>Восстановление пароля</h2>

    <?php if(hasMessage('error')): ?>
        <div class="notice error"><?php echo getMessage('error') ?></div>
    <?php endif; ?>

    <?php if(hasMessage('success')): ?>
        <div class="notice success"><?php echo getMessage('success') ?></div>
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

    <button
        type="submit"
        id="submit"
    >Отправить ссылку для восстановления</button>
    <p><a href="/index.php">Вернуться на страницу входа</a></p>
</form>
</body>
</html>