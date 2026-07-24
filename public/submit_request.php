<?php
require 'includes/auth.php';
require 'includes/db.php';

// Получаем ID услуги из GET или POST
$service_id = (int)($_GET['service_id'] ?? ($_POST['service_id'] ?? 0));
if (!$service_id) {
    die("Неверный ID услуги");
}

// Получаем информацию об услуге
$stmt = $pdo->prepare("SELECT title, price FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();

if (!$service) {
    die("Услуга не найдена");
}

// Получаем данные пользователя для автозаполнения
$stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Обработка отправки формы
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $comments = trim($_POST['comments']);

    // Валидация
    if (empty($customer_name)) {
        $errors[] = "ФИО обязательно для заполнения";
    }
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный email";
    }
    if (empty($customer_phone)) {
        $errors[] = "Телефон обязателен для заполнения";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO requests 
                (user_id, service_id, customer_name, customer_email, customer_phone, comments) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id,
                $service_id,
                $customer_name,
                $customer_email,
                $customer_phone,
                $comments
            ]);
            $requestId = $pdo->lastInsertId();
            // Редирект с параметром успеха и ID заявки
            header("Location: submit_request.php?service_id=$service_id&success=1&request_id=$requestId");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Ошибка сохранения заявки: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Оформление заявки - Пожарная Безопасность</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .request-card {
            border: 2px solid #c00;
            box-shadow: 0 4px 15px rgba(204, 0, 0, 0.1);
        }
        .service-info {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 20px;
        }
        .required::after {
            content: ' *';
            color: #c00;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">ПОЖАРКА</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="about.php">О нас</a>
            <a class="nav-link" href="services.php">Услуги</a>
            <a class="nav-link" href="contacts.php">Контакты</a>
            <a class="nav-link" href="logout.php">Выход (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card request-card">
                <div class="card-header text-white text-center" style="background-color: #c00;">
                    <h4 class="mb-0">📋 Оформление заявки</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Информация об услуге -->
                    <div class="service-info mb-4">
                        <h5 class="text-danger">Выбранная услуга:</h5>
                        <h4><?= htmlspecialchars($service['title']) ?></h4>
                        <p class="fs-5 text-danger fw-bold">Стоимость: от <?= number_format($service['price'], 0, '', ' ') ?> руб.</p>
                    </div>
                    
                    <!-- Сообщения об ошибках -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h6>Пожалуйста, исправьте следующие ошибки:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Форма заявки -->
                    <form method="POST">
                        <input type="hidden" name="service_id" value="<?= $service_id ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">ФИО</label>
                                <input type="text" name="customer_name" class="form-control" 
                                       value="<?= htmlspecialchars($_POST['customer_name'] ?? $user['full_name'] ?? '') ?>" 
                                       required placeholder="Введите ваше полное имя">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Email</label>
                                <input type="email" name="customer_email" class="form-control" 
                                       value="<?= htmlspecialchars($_POST['customer_email'] ?? $user['email'] ?? '') ?>" 
                                       required placeholder="example@mail.ru">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label required">Телефон для связи</label>
                            <input type="tel" name="customer_phone" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>" 
                                   required placeholder="+7 (999) 999-99-99">
                            <div class="form-text">Мы перезвоним вам в ближайшее время</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Комментарий к заявке</label>
                            <textarea name="comments" class="form-control" rows="4" 
                                      placeholder="Опишите дополнительные пожелания или особенности вашего объекта..."><?= htmlspecialchars($_POST['comments'] ?? '') ?></textarea>
                            <div class="form-text">Необязательное поле</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                📞 Отправить заявку на обратный звонок
                            </button>
                            <a href="services.php" class="btn btn-secondary">← Вернуться к услугам</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно успеха -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">✅ Заявка отправлена!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Ваша заявка успешно принята. Наш менеджер свяжется с вами в ближайшее время.</p>
                <p><strong>Номер заявки:</strong> 
                    <?php 
                        $requestId = isset($_GET['request_id']) ? (int)$_GET['request_id'] : '—';
                        echo htmlspecialchars($requestId);
                    ?>
                </p>
            </div>
            <div class="modal-footer">
                <a href="services.php" class="btn btn-primary">Вернуться к услугам</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('successModal'));
        myModal.show();
    });
</script>
<?php endif; ?>

<footer class="mt-5">
    <div class="container">
        © 2026 Пожарная Безопасность.
    </div>
</footer>

<script src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>