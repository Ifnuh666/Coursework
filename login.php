<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    $errors = [];

    if (empty($login)) {
        $errors[] = "Введите логин";
    }

    if (empty($password)) {
        $errors[] = "Введите пароль";
    }

    if (empty($errors)) {
        $sql = "SELECT id, last_name, first_name, patronymic, login, password FROM user WHERE login = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $user = $res->fetch_assoc();
            
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_login'] = $user['login'];
                $_SESSION['user_fullname'] = trim($user['last_name'] . ' ' . $user['first_name'] . ' ' . ($user['patronymic'] ?? ''));
                $_SESSION['user_firstname'] = $user['first_name'];
                $_SESSION['user_lastname'] = $user['last_name'];
                $_SESSION['logged_in'] = true;
                
                // Перенаправляем в личный кабинет
                header("Location: account.php");
                exit();
            } else {
                $errors[] = "Неверный логин или пароль";
            }
        } else {
            $errors[] = "Неверный логин или пароль";
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        $_SESSION['old_login'] = $login;
        header("Location: main.php");
        exit();
    }
} else {
    header("Location: main.php");
    exit();
}
?>