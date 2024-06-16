<?php
require_once __DIR__ . '/src/helpers.php';

checkGuest();
?>



<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Сброс пароля - PlanBoard</title>
    <link rel="stylesheet" href="assets/login.css">
</head>
<body>

<form class="card" action="/src/reset_password.php" method="post">
    <h2>Сброс пароля</h2>

    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">

    <?php if(hasMessage('error')): ?>
        <div class="notice error"><?php echo getMessage('error') ?></div>
    <?php endif; ?>

    <label for="password">
        Новый пароль
        <input
            type="password"
            id="password"
            name="password"
            placeholder="******"
            <?php echo validationErrorAttr('password'); ?>
        >
        <?php if(hasValidationError('password')): ?>
            <small class="error"><?php echo validationErrorMessage('password'); ?></small>
        <?php endif; ?>
    </label>

    <label for="password_confirmation">
        Подтвердите пароль
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            placeholder="******"
            <?php echo validationErrorAttr('password_confirmation'); ?>
        >
        <?php if(hasValidationError('password_confirmation')): ?>
            <small class="error"><?php echo validationErrorMessage('password_confirmation'); ?></small>
        <?php endif; ?>
    </label>

    <button
        type="submit"
        id="submit"
    >Сбросить пароль</button>
</form>
</body>
</html>