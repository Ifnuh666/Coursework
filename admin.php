<?php
// Подключаем БД
require_once './db.php';

// Проверяем подключение
if (!isset($conn) || $conn->connect_error) {
    die("Ошибка подключения к базе данных");
}

// Обработка добавления товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $rating = floatval($_POST['rating']);
    $image = $conn->real_escape_string($_POST['image']);
    
    $sql = "INSERT INTO tovar (name, description, price, rating, image) VALUES ('$name', '$description', $price, $rating, '$image')";
    if ($conn->query($sql)) {
        $success = "Товар успешно добавлен!";
    } else {
        $error = "Ошибка: " . $conn->error;
    }
}

// Обработка удаления товара
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM tovar WHERE id = $id";
    if ($conn->query($sql)) {
        $success = "Товар успешно удален!";
    } else {
        $error = "Ошибка: " . $conn->error;
    }
}

// Обработка редактирования товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $rating = floatval($_POST['rating']);
    $image = $conn->real_escape_string($_POST['image']);
    
    $sql = "UPDATE tovar SET name='$name', description='$description', price=$price, rating=$rating, image='$image' WHERE id=$id";
    if ($conn->query($sql)) {
        $success = "Товар успешно обновлен!";
    } else {
        $error = "Ошибка: " . $conn->error;
    }
}

// Получаем все товары
$products = $conn->query("SELECT * FROM tovar ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель | Sakura</title>
    <link rel="icon" href="./images/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/styleadmin.css">
</head>
<body>
    <header class="admin-header">
        <div class="admin-logo">
            <img src="./images/logo.png" alt="Logo">
            <h1>Sakura <span>Админ-панель</span></h1>
        </div>
        <div class="admin-nav">
            <a href="index.php" class="view-site">Перейти на сайт</a>
            <a href="#">Выйти</a>
        </div>
    </header>

    <div class="admin-container">
        <aside class="admin-sidebar">
            <ul class="sidebar-menu">
                <li><a href="#" class="active" data-tab="dashboard">📊 Главная</a></li>
                <li><a href="#" data-tab="products">🌸 Товары</a></li>
                <li><a href="#" data-tab="orders">📦 Заказы</a></li>
                <li><a href="#" data-tab="users">👤 Пользователи</a></li>
                <li><a href="#" data-tab="settings">⚙️ Настройки</a></li>
            </ul>
        </aside>

        <main class="admin-content">
            <!-- Уведомления -->
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Вкладка Главная -->
            <div id="dashboard-tab">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Всего товаров</h3>
                        <div class="number"><?php echo $products->num_rows; ?></div>
                    </div>
                    <div class="stat-card">
                        <h3>Заказов сегодня</h3>
                        <div class="number">0</div>
                    </div>
                    <div class="stat-card">
                        <div class="number">🌸</div>
                        <h3>Активных пользователей</h3>
                        <div class="number">1</div>
                    </div>
                </div>
            </div>

            <!-- Вкладка Товары -->
            <div id="products-tab" style="display: none;">
                <!-- Форма добавления товара -->
                <div class="add-form">
                    <h3 class="form-title">➕ Добавить новый товар</h3>
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Название товара</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>Цена (₽)</label>
                                <input type="number" name="price" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label>Рейтинг (1-5)</label>
                                <input type="number" name="rating" step="0.1" min="0" max="5" required>
                            </div>
                            <div class="form-group">
                                <label>Название файла изображения</label>
                                <input type="text" name="image" placeholder="например: rose.jpg" required>
                            </div>
                            <div class="form-group">
                                <label>Описание</label>
                                <textarea name="description" rows="3" required></textarea>
                            </div>
                        </div>
                        <button type="submit" name="add_product" class="btn-submit">Добавить товар</button>
                    </form>
                </div>

                <!-- Таблица товаров -->
                <div class="products-table">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Изображение</th>
                                <th>Название</th>
                                <th>Цена</th>
                                <th>Рейтинг</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $products->data_seek(0);
                            while($product = $products->fetch_assoc()): 
                            ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <img src="./images/<?php echo $product['image']; ?>" class="product-img" 
                                         onerror="this.src='./images/logo.png'">
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo $product['price']; ?> ₽</td>
                                <td>⭐ <?php echo $product['rating']; ?></td>
                                <td class="actions">
                                    <button class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($product)); ?>)">✏️</button>
                                    <a href="?delete=<?php echo $product['id']; ?>" class="btn-delete" onclick="return confirm('Удалить товар?')">🗑️</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Вкладка Заказы -->
            <div id="orders-tab" style="display: none;">
                <div class="add-form">
                    <h3 class="form-title">📦 Список заказов</h3>
                    <p style="color: #888; padding: 20px; text-align: center;">Здесь будут отображаться заказы пользователей</p>
                </div>
            </div>

            <!-- Вкладка Пользователи -->
            <div id="users-tab" style="display: none;">
                <div class="add-form">
                    <h3 class="form-title">👤 Пользователи</h3>
                    <p style="color: #888; padding: 20px; text-align: center;">Здесь будет управление пользователями</p>
                </div>
            </div>

            <!-- Вкладка Настройки -->
            <div id="settings-tab" style="display: none;">
                <div class="add-form">
                    <h3 class="form-title">⚙️ Настройки сайта</h3>
                    <p style="color: #888; padding: 20px; text-align: center;">Здесь будут настройки сайта</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Модальное окно редактирования -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3 style="margin-bottom: 20px;">✏️ Редактировать товар</h3>
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Название</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description" id="edit_description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Цена (₽)</label>
                    <input type="number" name="price" id="edit_price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Рейтинг</label>
                    <input type="number" name="rating" id="edit_rating" step="0.1" min="0" max="5" required>
                </div>
                <div class="form-group">
                    <label>Изображение</label>
                    <input type="text" name="image" id="edit_image" required>
                </div>
                <button type="submit" name="edit_product" class="btn-submit">Сохранить</button>
                <button type="button" onclick="closeModal()" style="margin-left: 10px; padding: 12px 25px; background: #95a5a6; color: white; border: none; border-radius: 8px; cursor: pointer;">Отмена</button>
            </form>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Переключение вкладок
        $('.sidebar-menu a').on('click', function(e) {
            e.preventDefault();
            $('.sidebar-menu a').removeClass('active');
            $(this).addClass('active');
            
            var tab = $(this).data('tab');
            $('#dashboard-tab, #products-tab, #orders-tab, #users-tab, #settings-tab').hide();
            $('#' + tab + '-tab').show();
        });

        // Модальное окно редактирования
        function openEditModal(product) {
            $('#edit_id').val(product.id);
            $('#edit_name').val(product.name);
            $('#edit_description').val(product.description);
            $('#edit_price').val(product.price);
            $('#edit_rating').val(product.rating);
            $('#edit_image').val(product.image);
            $('#editModal').addClass('active');
        }

        function closeModal() {
            $('#editModal').removeClass('active');
        }

        // Закрытие модального окна при клике вне его
        $(document).on('click', function(e) {
            if ($(e.target).is('#editModal')) {
                closeModal();
            }
        });

        // Автоматическое скрытие уведомлений
        setTimeout(function() {
            $('.alert').fadeOut(500);
        }, 3000);
    </script>
</body>
</html>