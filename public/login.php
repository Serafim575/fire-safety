<?php
session_start();
require 'includes/db.php';

$error = '';

// СЕКРЕТНЫЕ ДАННЫЕ АДМИНА
$admin_login = 'admin';
$admin_password = '12345678';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);      // теперь это поле называется login
    $password = $_POST['password'];

    // ПРОВЕРКА: админ ли это?
    if ($login === $admin_login && $password === $admin_password) {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Администратор';
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_logged_in'] = true;
        
        header("Location: index.php");
        exit;
    }
    
    // Обычная проверка пользователя по email
    if (empty($login) || empty($password)) {
        $error = "Все поля обязательны.";
    } else {
        $stmt = $pdo->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Неверный логин/email или пароль.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 400px;">
        <div class="card-header text-white" style="background-color: #c00;">
            Вход в аккаунт
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Логин / Email</label>
                    <input type="text" name="login" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
                <div class="text-center mt-2">
                    <a href="register.php">Нет аккаунта? Зарегистрируйтесь</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>