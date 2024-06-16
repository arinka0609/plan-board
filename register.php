<?php
require_once __DIR__ . '/src/helpers.php';
checkGuest();
?>

<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>PlanBoard</title>
    <link rel="stylesheet" href="assets/register.css">
</head>

<body>
<form class="card" action="src/actions/register.php" method="post" enctype="multipart/form-data">
    <h2>Регистрация</h2>

    <label for="name">
        Имя
        <input
            type="text"
            id="name"
            name="name"
            placeholder="Иванов Иван"
            value="<?php echo old('name') ?>"
            <?php echo validationErrorAttr('name'); ?>
        >
        <?php if(hasValidationError('name')): ?>
            <small class="error"><?php echo validationErrorMessage('name'); ?></small>
        <?php endif; ?>
    </label>

    <label for="email">
        E-mail
        <input
            type="text"
            id="email"
            name="email"
            placeholder="my@mail.ru"
            value="<?php echo old('email') ?>"
            <?php echo validationErrorAttr('email'); ?>
        >
        <?php if(hasValidationError('email')): ?>
            <small class="error"><?php echo validationErrorMessage('email'); ?></small>
        <?php endif; ?>
    </label>

    Изображение профиля <p>
    <span class="label-file-btn">
        <input
            type="file"
            id="avatar"
            name="avatar"
            <?php echo validationErrorAttr('avatar'); ?>
        >
        </span>
        <?php if(hasValidationError('avatar')): ?>
            <small class="error"><?php echo validationErrorMessage('avatar'); ?></small>
        <?php endif; ?>
 

    <div class="grid">
        <label for="password">
            Пароль
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
            Подтверждение
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="******"
            >
        </label>
    </div>
    
    <button
        type="submit"
        id="submit"
    >Продолжить</button>
    <p>У меня уже есть <a href="/index1.php">аккаунт</a></p>
    
</form>

</body>
</html>