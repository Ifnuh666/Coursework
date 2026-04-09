<?php

$host = 'localhost';
$db   = 'flow';
$user = 'root';
$pass = '';
$charset = 'utf8mb4'; // Кодировка, которая поддерживает стикеры, иероглифы и т.п. вещи

// Создание подключения
$conn = new mysqli($host, $user, $pass, $db);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}


?>