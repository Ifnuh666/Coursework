<?php
session_start();
require_once 'db.php';

// Проверка авторизации - если не авторизован, перенаправляем на страницу входа
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: main.php");
    exit();
}

// Получаем данные из сессии
$user_fullname = $_SESSION['user_fullname'] ?? 'Пользователь';
$user_login = $_SESSION['user_login'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';

// Функция для получения статуса на русском
function getStatusText($status) {
    $statuses = [
        'Доставлен' => 'Доставлен',
        'processing' => 'В обработке',
        'В обработке' => 'В обработке',
        'Оплачен' => 'Оплачен',
        'Отменен' => 'Отменен',
        'новый' => 'Новый'
    ];
    return $statuses[$status] ?? $status;
}

// Функция для получения цвета статуса
function getStatusClass($status) {
    $statuses = [
        'Доставлен' => 'delivered',
        'processing' => 'processing',
        'В обработке' => 'processing',
        'Оплачен' => 'paid',
        'Отменен' => 'cancelled',
        'новый' => 'new'
    ];
    return $statuses[$status] ?? '';
}

// Получение заказов пользователя из БД
$orders = [];
$sql = "SELECT id, id_user, date, status FROM order_tovar WHERE id_user = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Для каждого заказа получаем товары
        $order_items = [];
        $items_sql = "SELECT otp.*, t.name, t.price 
              FROM order_tovar_prom otp 
              JOIN tovar t ON otp.id_tovar = t.id 
              WHERE otp.id_order = ?";
        $items_stmt = $conn->prepare($items_sql);
        $items_stmt->bind_param("i", $row['id']);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();
        
        $total = 0;
        while ($item = $items_result->fetch_assoc()) {
            $order_items[] = $item;
            $total += $item['price'];
        }
        $items_stmt->close();
        
        $orders[] = [
            'id' => $row['id'],
            'date' => $row['date'],
            'status' => $row['status'],
            'items' => $order_items,
            'total' => $total
        ];
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="icon" href="../Flow/images/logo.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rochester&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Личный кабинет</title>
    <style>
        .status.new { background-color: #ffc107; color: #000; padding: 5px 10px; border-radius: 5px; font-size: 12px; }
        .status.processing { background-color: #17a2b8; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 12px; }
        .status.delivered { background-color: #28a745; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 12px; }
        .status.paid { background-color: #007bff; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 12px; }
        .status.cancelled { background-color: #dc3545; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 12px; }
        .order-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }
        .order-items {
            padding: 10px 0;
        }
        .order-total {
            text-align: right;
            font-weight: bold;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .empty-orders {
            text-align: center;
            padding: 50px;
            background: #fff;
            border-radius: 10px;
            margin-top: 20px;
        }
        .user_info {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .user-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }
        .user-details h2 {
            margin: 0;
            font-size: 24px;
        }
        .user-details p {
            margin: 5px 0 0;
            color: #666;
        }
        .logout {
            margin-left: auto;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-block">
            <div class="block-logo">
                <a href="http://localhost/Flow/index.php"><img src="../Flow/images/logo.png" alt="Логотип"></a>
                <a href="http://localhost/Flow/index.php"><p>Sakura</p></a>
            </div>
            <div class="catalog-account">
                <a href="#"><img src="../Flow/images/account.png" alt="Личный кабинет"></a>
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
    <main class="user" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        <section class="info_user">
            <div class="user_info">
                <div class="user-avatar">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Аватар">
                </div>
                <div class="user-details">
                    <h2><?php echo htmlspecialchars($user_fullname); ?></h2>
                    <p><?php echo htmlspecialchars($user_login); ?></p>
                </div>
                <div class="logout">
                    <a href="logout.php" class="btn btn-danger">Выйти</a>
                </div>
            </div>
        </section>

        <?php if (empty($orders)): ?>
            <div class="empty-orders">
                <p>У вас пока нет заказов</p>
                <a href="catalog.php" class="btn btn-primary">Перейти в каталог</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <strong>Заказ №<?php echo $order['id']; ?></strong>
                        <span class="date"><?php echo date('d.m.Y', strtotime($order['date'])); ?></span>
                        <span class="status <?php echo getStatusClass($order['status']); ?>">
                            <?php echo getStatusText($order['status']); ?>
                        </span>
                    </div>
                    <div class="order-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <p><?php echo htmlspecialchars($item['name']); ?> - <?php echo number_format($item['price'], 0, '', ' '); ?> ₽</p>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-total">Итого: <?php echo number_format($order['total'], 0, '', ' '); ?> ₽</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column">
                <div class="footer-logo">
                    <a href="index.html"><img src="./images/logo.png" alt="Логотип"></a>
                    <p>Sakura</p>
                </div>
                <p class="footer-about">Цветочная мастерская. Создаем букеты с душой для самых важных моментов вашей жизни.</p>
                <div class="footer-social">
                    <a href="#"><img src="../Flow/images/tg.png" alt="Telegram"></a>
                    <a href="#"><img src="../Flow/images/vk.png" alt="VK"></a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>