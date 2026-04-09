<?php
session_start();
require_once './db.php';

// Проверяем подключение
if (!isset($conn) || $conn->connect_error) {
    die("Ошибка подключения к базе данных");
}

// ========== ОБРАБОТКА ОТЗЫВА ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    // Проверяем, авторизован ли пользователь
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        $_SESSION['review_error'] = "Чтобы оставить отзыв, необходимо авторизоваться";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    // Получаем данные из формы
    $id_tovar = isset($_POST['id_tovar']) ? (int)$_POST['id_tovar'] : 0;
    $id_user = isset($_POST['id_user']) ? (int)$_POST['id_user'] : 0;
    $review = trim($_POST['review'] ?? '');
    
    // Валидация
    $errors = [];
    
    if ($id_tovar <= 0) {
        $errors[] = "Не указан товар";
    }
    
    if ($id_user <= 0) {
        $errors[] = "Ошибка: пользователь не найден";
    }
    
    if (empty($review)) {
        $errors[] = "Пожалуйста, напишите ваш отзыв";
    }
    
    if (strlen($review) < 10) {
        $errors[] = "Отзыв должен содержать не менее 10 символов";
    }
    
    // Если есть ошибки, сохраняем в сессию
    if (!empty($errors)) {
        $_SESSION['review_errors'] = $errors;
        $_SESSION['old_review'] = $review;
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?id=" . $id_tovar);
        exit();
    }
    
    // Вставляем отзыв в базу данных
    $insert_review = $conn->prepare("INSERT INTO reviews (id_user, id_tovar, review) VALUES (?, ?, ?)");
    $insert_review->bind_param("iis", $id_user, $id_tovar, $review);
    
    if ($insert_review->execute()) {
        $_SESSION['review_success'] = "Спасибо! Ваш отзыв успешно добавлен.";
        $insert_review->close();
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?id=" . $id_tovar);
        exit();
    } else {
        $_SESSION['review_errors'] = ["Ошибка при добавлении отзыва"];
        $insert_review->close();
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?id=" . $id_tovar);
        exit();
    }
}

// ========== ОБРАБОТКА ЗАКАЗА ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_order') {
    // Получаем и валидируем данные из формы
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $customer_name = trim($_POST['customer_name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    
    // Базовая валидация
    $errors = [];
    
    if ($product_id <= 0) {
        $errors[] = "Не указан товар";
    }
    
    if (empty($customer_name)) {
        $errors[] = "Пожалуйста, укажите ваше имя";
    }
    
    // Если нет ошибок, оформляем заказ
    if (empty($errors)) {
        // Проверяем, существует ли товар
        $stmt = $conn->prepare("SELECT id, name, price FROM tovar WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $stmt->close();
            
            // Получаем ID пользователя, если авторизован
            $id_user = $_SESSION['user_id'] ?? null;
            
            // Если пользователь не авторизован, ищем или создаем пользователя по имени
            if ($id_user === null && !empty($customer_name)) {
                // Разбиваем имя на части
                $name_parts = explode(' ', $customer_name);
                $first_name = $name_parts[0] ?? $customer_name;
                $last_name = $name_parts[1] ?? '';
                $patronymic = $name_parts[2] ?? '';
                
                // Проверяем, есть ли пользователь с таким именем
                $check_user = $conn->prepare("SELECT id FROM user WHERE first_name = ? AND last_name = ?");
                $check_user->bind_param("ss", $first_name, $last_name);
                $check_user->execute();
                $user_result = $check_user->get_result();
                
                if ($user_result->num_rows > 0) {
                    $user_data = $user_result->fetch_assoc();
                    $id_user = $user_data['id'];
                } else {
                    // Создаем нового пользователя
                    $login = 'user_' . time();
                    $password = md5('123456');
                    $insert_user = $conn->prepare("INSERT INTO user (first_name, last_name, patronymic, login, password) VALUES (?, ?, ?, ?, ?)");
                    $insert_user->bind_param("sssss", $first_name, $last_name, $patronymic, $login, $password);
                    if ($insert_user->execute()) {
                        $id_user = $insert_user->insert_id;
                    }
                    $insert_user->close();
                }
                $check_user->close();
            }
            
            // Вставляем заказ в таблицу order_tovar
            $date = date('Y-m-d');
            $status = 'новый';
            
            $insert_order = $conn->prepare("INSERT INTO order_tovar (id_user, date, status) VALUES (?, ?, ?)");
            $insert_order->bind_param("iss", $id_user, $date, $status);
            
            if ($insert_order->execute()) {
                $order_id = $insert_order->insert_id;
                $insert_order->close();
                
                // Вставляем связь заказа с товаром в order_tovar_prom
                $insert_order_item = $conn->prepare("INSERT INTO order_tovar_prom (id_order, id_tovar) VALUES (?, ?)");
                $insert_order_item->bind_param("ii", $order_id, $product_id);
                
                if ($insert_order_item->execute()) {
                    $insert_order_item->close();
                    
                    // Сохраняем данные заказа в сессию
                    $_SESSION['last_order'] = [
                        'order_id' => $order_id,
                        'product_name' => $product['name'],
                        'product_price' => $product['price'],
                        'customer_name' => $customer_name,
                        'comment' => $comment,
                        'status' => $status,
                        'date' => $date
                    ];
                    
                    // Перенаправляем на страницу успеха
                    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $product_id . "&success=1");
                    exit();
                } else {
                    $error_message = "Ошибка при добавлении товара в заказ";
                }
            } else {
                $error_message = "Ошибка при создании заказа";
            }
        } else {
            $error_message = "Товар не найден";
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// ========== ПОЛУЧАЕМ ДАННЫЕ ТОВАРА ==========
// Получаем ID товара из GET параметра
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Если ID не передан или равен 0, показываем ошибку
if ($productId <= 0) {
    die("❌ Товар не выбран. <a href='catalog.php'>← Вернуться в каталог</a>");
}

// Запрашиваем товар
$stmt = $conn->prepare("SELECT id, name, description, price, image, rating FROM tovar WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Товар не найден. <a href='catalog.php'>← Вернуться в каталог</a>");
}

$product = $result->fetch_assoc();
$stmt->close();

// Запрос отзывов
$reviewsStmt = $conn->prepare("
    SELECT r.review, u.first_name, u.last_name 
    FROM reviews r
    LEFT JOIN user u ON r.id_user = u.id
    WHERE r.id_tovar = ?
    ORDER BY r.id DESC
");
$reviewsStmt->bind_param("i", $product['id']);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();

// Определяем, авторизован ли пользователь
$userId = $_SESSION['user_id'] ?? null;
$isLoggedIn = ($userId !== null);

// Получаем данные пользователя для автозаполнения
$userName = '';
if ($isLoggedIn) {
    $userStmt = $conn->prepare("SELECT first_name, last_name FROM user WHERE id = ?");
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    if ($userResult->num_rows > 0) {
        $userData = $userResult->fetch_assoc();
        $userName = trim($userData['first_name'] . ' ' . $userData['last_name']);
    }
    $userStmt->close();
}

// Проверяем, нужно ли показать страницу успеха
$showSuccess = isset($_GET['success']) && $_GET['success'] == 1;
$orderData = $showSuccess && isset($_SESSION['last_order']) ? $_SESSION['last_order'] : null;

// Если показали страницу успеха, удаляем данные из сессии
if ($showSuccess && $orderData) {
    unset($_SESSION['last_order']);
}

// Получаем сообщения об ошибках/успехе отзывов
$reviewErrors = isset($_SESSION['review_errors']) ? $_SESSION['review_errors'] : null;
$reviewSuccess = isset($_SESSION['review_success']) ? $_SESSION['review_success'] : null;
$oldReview = isset($_SESSION['old_review']) ? $_SESSION['old_review'] : '';

// Очищаем сессионные переменные для отзывов
unset($_SESSION['review_errors']);
unset($_SESSION['review_success']);
unset($_SESSION['old_review']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/stylecard.css">
    <link rel="icon" href="./images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rochester&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?php echo $showSuccess ? 'Заказ оформлен' : 'Оформление заказа'; ?> - Sakura</title>
    <style>
        .success-icon { font-size: 4rem; color: #28a745; }
        .order-card { background: #f8f9fa; border-radius: 10px; padding: 20px; }
        .review-card { transition: transform 0.2s; }
        .review-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <header>
        <div class="header-block">
            <div class="block-logo">
                <a href="index.php"><img src="./images/logo.png" alt="Логотип"></a>
                <a href="index.php"><p>Sakura</p></a>
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
    
    <div class="container py-5">
        <!-- Сообщения об ошибках -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($reviewErrors): ?>
            <div class="alert alert-danger">
                <?php foreach ($reviewErrors as $error): ?>
                    <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($reviewSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($reviewSuccess) ?></div>
        <?php endif; ?>
        
        <?php if ($showSuccess && $orderData): ?>
            <!-- Страница успеха -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow text-center">
                        <div class="card-body py-5">
                            <div class="success-icon">✅</div>
                            <h1 class="mt-3">Заказ успешно оформлен!</h1>
                            <p class="lead">Спасибо за ваш заказ. Мы свяжемся с вами.</p>
                            <div class="order-card mt-4 text-start">
                                <h4>Детали заказа</h4>
                                <hr>
                                <p><strong>Номер заказа:</strong> #<?= htmlspecialchars($orderData['order_id']) ?></p>
                                <p><strong>Товар:</strong> <?= htmlspecialchars($orderData['product_name']) ?></p>
                                <p><strong>Сумма:</strong> <?= (int)$orderData['product_price'] ?> ₽</p>
                                <p><strong>Имя:</strong> <?= htmlspecialchars($orderData['customer_name']) ?></p>
                                <?php if (!empty($orderData['comment'])): ?>
                                    <p><strong>Комментарий:</strong> <?= htmlspecialchars($orderData['comment']) ?></p>
                                <?php endif; ?>
                                <p><strong>Статус:</strong> <span class="badge bg-warning"><?= htmlspecialchars($orderData['status']) ?></span></p>
                            </div>
                            <div class="mt-4">
                                <a href="catalog.php" class="btn btn-primary">Продолжить покупки</a>
                                <a href="account.php" class="btn btn-outline-secondary">Личный кабинет</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Страница оформления заказа -->
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow">
                        <img src="./images/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="text-muted">⭐ <?= htmlspecialchars($product['rating']) ?></p>
                            <p class="card-text"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                            <h4 class="text-primary mb-4"><?= (int)$product['price'] ?> ₽</h4>
                            
                            <!-- Форма заказа -->
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="submit_order">
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label">Ваше имя *</label>
                                    <input type="text" class="form-control" name="customer_name" value="<?= htmlspecialchars($userName) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Комментарий</label>
                                    <textarea class="form-control" name="comment" rows="3" placeholder="Ваши пожелания..."></textarea>
                                </div>
                                <button type="submit" class="btn-btn-primary w-100">Оформить заказ</button>
                            </form>
                            
                            <!-- Отзывы -->
                            <hr class="my-4">
                            <h4>Отзывы о товаре (<?= $reviewsResult->num_rows ?>)</h4>
                            <?php if ($reviewsResult->num_rows > 0): ?>
                                <?php while($review = $reviewsResult->fetch_assoc()): ?>
                                    <div class="review-card border rounded p-3 mb-3 bg-light">
                                        <strong><?= htmlspecialchars(($review['last_name'] ?? '') . ' ' . ($review['first_name'] ?? 'Покупатель')) ?></strong>
                                        <p class="mb-0 mt-2"><?= nl2br(htmlspecialchars($review['review'])) ?></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="alert alert-info">Пока нет отзывов. Будьте первым!</div>
                            <?php endif; ?>
                            
                            <!-- Форма добавления отзыва -->
                            <div class="mt-4">
                                <h5>Оставить отзыв</h5>
                                <?php if ($isLoggedIn): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="add_review">
                                        <input type="hidden" name="id_tovar" value="<?= (int)$product['id'] ?>">
                                        <input type="hidden" name="id_user" value="<?= (int)$userId ?>">
                                        <textarea class="form-control" name="review" rows="3" placeholder="Расскажите о вашем опыте..." required minlength="10"><?= htmlspecialchars($oldReview) ?></textarea>
                                        <small class="text-muted">Минимум 10 символов</small>
                                        <button type="submit" class="btn-btn-primary mt-2">Отправить отзыв</button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning"><a href="main.php">Войдите</a>, чтобы оставить отзыв</div>
                                <?php endif; ?>
                            </div>
                            
                            <a href="catalog.php" class="btn btn-outline-secondary w-100 mt-3">← Вернуться в каталог</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
                    <li><a href="./Frontend/about.html">О нас</a></li>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
if (isset($reviewsStmt)) $reviewsStmt->close();
$conn->close();
?>