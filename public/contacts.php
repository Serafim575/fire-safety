<?php 
require 'includes/auth.php';
require 'includes/db.php';

$success = '';
$error = '';

$feedback_service_id = 8; // <-- ЗАМЕНИТЕ НА ВАШ ID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $customer_name = trim($_POST['customer_name']);
    $comments = trim($_POST['comments']);

    // Получаем email пользователя из БД
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $customer_email = $user ? $user['email'] : '';

    // Валидация
    if (empty($customer_name) || empty($comments)) {
        $error = "Заполните имя и сообщение.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO requests (user_id, customer_name, customer_email, customer_phone, comments, service_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $customer_name, $customer_email, '', $comments, $feedback_service_id]);
            $success = "Сообщение отправлено! Мы свяжемся с вами.";
        } catch (PDOException $e) {
            $error = "Ошибка: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Контакты - Пожарная Безопасность</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php" style="font-weight: 800; font-size: 1.8rem; color: #c00 !important; text-decoration: none;">
            ПОЖАРНАЯ БЕЗОПАСНОСТЬ
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="about.php">О нас</a>
            <a class="nav-link" href="services.php">Услуги</a>
            <a class="nav-link active" href="contacts.php">Контакты</a>
            <?php if (isAdmin()): ?>
                <a class="nav-link text-warning" href="admin/index.php">👑 Админка</a>
            <?php endif; ?>
            <a class="nav-link" href="logout.php">Выход (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center" style="color: #c00;">Наши контакты</h1>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Контактная информация</h3>
            <p><strong>Адрес:</strong> г. Москва, ул. Пожарная, д. 1</p>
            <p><strong>Телефон:</strong> +7 (495) 123-45-67</p>
            <p><strong>Email:</strong> info@pozharka.ru</p>
            <p><strong>Режим работы:</strong> Пн-Пт: 9:00-18:00</p>
        </div>
        <div class="col-md-6">
            <h3>Форма обратной связи</h3>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Ваше имя</label>
                    <input type="text" name="customer_name" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Сообщение</label>
                    <textarea name="comments" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Отправить сообщение</button>
            </form>
        </div>
    </div>
</div>

<footer class="mt-5">
    <div class="container">
        © 2026 Пожарная Безопасность. Все права защищены.
    </div>
</footer>
</body>
</html>