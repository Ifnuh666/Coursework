<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Flow/css/styleauth.css">
    <link rel="icon" href="../Flow/images/logo.png" type="image/png">
    <title>Регистрация</title>
</head>
<body>
    <?php
        // Показ ошибок
        if (isset($_SESSION['register_errors']) && !empty($_SESSION['register_errors'])) {
            foreach ($_SESSION['register_errors'] as $error) {
                echo "<div class='error'>❌ $error</div>";
            }
            unset($_SESSION['register_errors']);
        }
        // Старые данные для восстановления формы
        $old = $_SESSION['old_register_data'] ?? [];
        unset($_SESSION['old_register_data']);
        ?>
    <div class="auth_block">
        <h2>Регистрация</h2>
        <form action="../Flow/registr.php" method="post">
            <label>Фамилия:</label>
            <input type="text" name="last_name" required>
            <label>Имя:</label>
            <input type="text" name="first_name" required>
            <label>Отчество:</label>
            <input type="text" name="patronymic" required>
            <label>Логин:</label>
            <input type="text" name="login" required>
            <label>Пароль:</label>
            <input type="password" name="password" required>
            <button type="submit" class="auth">Зарегистрироваться</button>
        </form>
    </div>
</body>
</html>