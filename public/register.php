<?php
require 'includes/db.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);

    if (empty($email) || empty($password) || empty($full_name)) {
        $error = "Все поля обязательны.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Некорректный email.";
    } elseif (strlen($password) < 6) {
        $error = "Пароль должен быть не менее 6 символов.";
    } else {
        // Проверка уникальности email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Пользователь с таким email уже существует.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password, full_name) VALUES (?, ?, ?)");
            $stmt->execute([$email, $hashed, $full_name]);
            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-header text-white" style="background-color: #c00;">
            Регистрация
        </div>
        <div class="card-body">
            <?php if ($success): ?>
                <div class="alert alert-success">Регистрация успешна! <a href="login.php">Войдите</a></div>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">ФИО</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Пароль (мин. 6 символов)</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                    <div class="text-center mt-2">
                        <a href="login.php">Уже есть аккаунт? Войти</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>