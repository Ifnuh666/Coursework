<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // REQUEST_METHOD - переменная, которая хранит в себе HTTP-метод и сравнивается содержит ли она как раз-таки метод POST
    $last_name = trim($_POST['last_name'] ?? ''); // Берем данные из поля last_name на форме. ?? - если у нас нет такого имени или ключа и мы не хотим ошибку, то запишется пустое значение
    $first_name = trim($_POST['first_name'] ?? '');
    $patronymic = trim($_POST['patronymic'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = []; // Массив для хранения ошибок

    // Валидация - проверяем не пустое ли поле
    if (empty($last_name)) $errors[] = "Введите фамилию";
    if (empty($first_name)) $errors[] = "Введите имя";
    if (empty($login)) $errors[] = "Введите логин";
    if (empty($password)) $errors[] = "Введите пароль";

    // Проверка: занят ли логин
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM user WHERE login = ?"); // prepare() — подготавливает SQL запрос к выполнению
        $stmt->bind_param("s", $login); // bind_param() — привязывает переменные PHP к плейсхолдерам (в нашем случае это ?) в подготовленном запросе. Вместо ? подставляется значение из переменной login. "s" - тип параметра строка
        $stmt->execute(); // execute() - метод выполнения запроса
        $result = $stmt->get_result(); // get_result() — получает результат выполнения запроса в виде объекта
        
        if ($result->num_rows > 0) {
            $errors[] = "Пользователь с таким логином уже существует";
        }
        $stmt->close();
    }

    // Регистрация
    if (empty($errors)) {
        //$id_role = 1;

        $stmt = $conn->prepare("INSERT INTO user (last_name, first_name, patronymic, login, password) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $last_name, $first_name, $patronymic, $login, $password); // i - тип параметра число
        
        if ($stmt->execute()) {
            // Автоматический вход после регистрации
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_login'] = $login;
            $_SESSION['user_fullname'] = "$last_name $first_name " . ($patronymic ?? '');
            $_SESSION['logged_in'] = true;
            
            header("Location: account.php");
            exit();
        } else {
            $errors[] = "Ошибка регистрации: " . $conn->error;
        }
        $stmt->close();
    }

    // Если есть ошибки — сохраняем в сессию и возвращаем на форму
    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['old_register_data'] = [
            'login' => $login,
            'last_name' => $last_name,
            'first_name' => $first_name,
            'patronymic' => $patronymic,
        ];
        header("Location: registration_form.php");
        exit();
    }
}
?>