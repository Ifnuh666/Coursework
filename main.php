<?php
session_start();
require_once 'db.php';

// Проверяем, авторизован ли уже пользователь
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Если уже авторизован, перенаправляем в личный кабинет
    header("Location: account.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Flow/css/styleauth.css">
    <link rel="icon" href="../Flow/images/logo.png" type="image/png">
    <title>Авторизация</title>
    <style>
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="auth_block">
        <h2>Авторизация</h2>
        
        <?php if (isset($_SESSION['login_errors'])): ?>
            <div class="error-message">
                <?php foreach ($_SESSION['login_errors'] as $error): ?>
                    <p style="margin: 0;"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['login_errors']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <p style="margin: 0;"><?= htmlspecialchars($_SESSION['success_message']) ?></p>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <form action="login.php" method="post">
            <label>Логин:</label>
            <input type="text" name="login" value="<?= htmlspecialchars($_SESSION['old_login'] ?? '') ?>" required>
            <label>Пароль:</label>
            <input type="password" name="password" required>
            <button type="submit" class="auth">Войти</button>
            <a class="registr" href="registration_form.php">Регистрация</a>
        </form>
    </div>
</body>
</html>
<?php 
unset($_SESSION['old_login']);
?>