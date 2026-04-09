<?php
session_start();
require_once './db.php';
require_once 'check_auth.php'; // Добавляем проверку авторизации

// Проверяем подключение
if (!isset($conn) || $conn->connect_error) {
    die("Ошибка подключения к базе данных");
}

// Выполняем запрос и сохраняем результат
$result = $conn->query("SELECT * FROM tovar ORDER BY rating DESC LIMIT 4");
$result2 = $conn->query("SELECT * FROM tovar ORDER BY rating DESC LIMIT 4, 4");
$result3 = $conn->query("SELECT * FROM tovar ORDER BY rating DESC LIMIT 8, 4");
$result4 = $conn->query("SELECT * FROM tovar ORDER BY rating DESC LIMIT 12, 4");

// Проверяем, успешен ли запрос
if (!$result || !$result2) {
    die("Ошибка запроса: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Большой выбор букетов: розы, тюльпаны, пионы, хризантемы. Собственная флористика. 
    Доставка по Москве, СПб, Екатеринбургу от 2 часов. Выберите свой букет и оформите заказ.">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="./images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rochester&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Sakura | Каталог</title>
</head>
<body>
    <header>
        <div class="header-block">
            <div class="block-logo">
                <a href="http://localhost/Flow/index.php"><img src="./images/logo.png" alt="Логотип"></a>
                <a href="http://localhost/Flow/index.php"><p>Sakura</p></a>
            </div>
            <div class="catalog-account">
                <a href="http://localhost/Flow/main.php"><img src="./images/account.png" alt="Личынй кабинет"></a>
                <input type="checkbox" id="burger-toggle">
                <label for="burger-toggle" class="burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </label>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="http://localhost/Flow/index.php">Главная</a></li>
                        <li><a href="./Frontend/about.html">О нас</a></li>
                        <li><a href="http://localhost/Flow/catalog.php">Каталог</a></li>
                        <li><a href="http://localhost/Flow/main.php">Личный кабинет</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <div class="container">
        <main class="main-block">
            <h1>Букеты с доставкой</h1>
            <h2>Самые популярные букеты</h2>
            <section class="popular-tovar">
                <?php while($b = $result->fetch_assoc()): ?>
                    <div class="card">
                        <img src="./images/<?php echo $b['image']; ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $b['name']; ?></h5>
                            <p class="card-text"><?php echo $b['description']; ?></p>
                            <p>⭐ <?php echo $b['rating']; ?></p>
                            <p class="card-price"><?php echo $b['price']; ?> ₽</p>
                            <a href="order.php?id=<?= $b['id'] ?>" class="btn-btn-primary">Заказать</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>
            <h2>Подборки букетов</h2>
            <section class="popular-tovar">
                <?php while($b = $result2->fetch_assoc()): ?>
                    <div class="card">
                        <img src="./images/<?php echo $b['image']; ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $b['name']; ?></h5>
                            <p class="card-text"><?php echo $b['description']; ?></p>
                            <p>⭐ <?php echo $b['rating']; ?></p>
                            <p class="card-price"><?php echo $b['price']; ?> ₽</p>
                            <a href="order.php?id=<?= $b['id'] ?>" class="btn-btn-primary">Заказать</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>
            <h2>Ищут также</h2>
            <section>
                <div class="type_flow">
                    <div class="elem_type1">
                        <p>Цветы маме</p>
                    </div>
                    <div class="elem_type1">
                        <p>Цветы на день рождения</p>
                    </div>
                    <div class="elem_type1">
                        <p>До 2000</p>
                    </div>
                    <div class="elem_type1">
                        <p>Любимой девушке</p>
                    </div>
                </div>
            </section>
            <section class="popular-tovar">
                <?php while($b = $result3->fetch_assoc()): ?>
                    <div class="card">
                        <img src="./images/<?php echo $b['image']; ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $b['name']; ?></h5>
                            <p class="card-text"><?php echo $b['description']; ?></p>
                            <p>⭐ <?php echo $b['rating']; ?></p>
                            <p class="card-price"><?php echo $b['price']; ?> ₽</p>
                            <a href="order.php?id=<?= $b['id'] ?>" class="btn-btn-primary">Заказать</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>
            <section class="popular-tovar">
                <?php while($b = $result4->fetch_assoc()): ?>
                    <div class="card">
                        <img src="./images/<?php echo $b['image']; ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $b['name']; ?></h5>
                            <p class="card-text"><?php echo $b['description']; ?></p>
                            <p>⭐ <?php echo $b['rating']; ?></p>
                            <p class="card-price"><?php echo $b['price']; ?> ₽</p>
                            <a href="order.php?id=<?= $b['id'] ?>" class="btn-btn-primary">Заказать</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>
        </main>
    </div>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column">
                <div class="footer-logo">
                    <a href="http://localhost/Flow/index.php"><img src="./images/logo.png" alt="Логотип"></a>
                    <p>Sakura</p>
                </div>
                <p class="footer-about">Цветочная мастерская. Создаем букеты с душой для самых важных моментов вашей жизни.</p>
                <div class="footer-social">
                    <a href="#"><img src="./images/tg.png" alt="Telegram"></a>
                    <a href="#"><img src="./images/vk.png" alt="VK"></a>
                </div>
            </div>
            <div class="footer-column">
                <h4>Навигация</h4>
                <ul>
                    <li><a href="http://localhost/Flow/index.php">Главная</a></li>
                    <li><a href="../Frontend/about.html">О нас</a></li>
                    <li><a href="http://localhost/Flow/catalog.php">Каталог</a></li>
                    <li><a href="http://localhost/Flow/main.php">Личный кабинет</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Каталог</h4>
                <ul>
                    <li><a href="#">Букеты</a></li>
                    <li><a href="#">Композиции</a></li>
                    <li><a href="#">Свадебные</a></li>
                    <li><a href="#">Подарки</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Контакты</h4>
                <ul class="footer-contact">
                    <li>📍 г. Ярославль, ул. Цветочная, 15</li>
                    <li>📞 +7 (999) 123-45-67</li>
                    <li>✉️ info@sakura.ru</li>
                    <li>🕒 Пн-Вс: 9:00 - 21:00</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Sakura. Все права защищены.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>