<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    die("Доступ запрещен. Только для администраторов.");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель - Пожарная Безопасность</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .admin-card {
            transition: all 0.3s ease;
            border: 2px solid #c00;
        }
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(204, 0, 0, 0.2);
        }
        .stats-card {
            background: linear-gradient(45deg, #c00, #900);
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #900;">
    <div class="container">
        <a class="navbar-brand" href="index.php">👑 АДМИН-ПАНЕЛЬ</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="../index.php">На сайт</a>
            <a class="nav-link" href="logout.php">Выход</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center mb-5" style="color: #c00;">Административная панель</h1>
    
    <!-- Статистика -->
    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <h3>
                        <?php 
                        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                        echo $stmt->fetchColumn();
                        ?>
                    </h3>
                    <p>Пользователей</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <h3>
                        <?php 
                        $stmt = $pdo->query("SELECT COUNT(*) FROM services");
                        echo $stmt->fetchColumn();
                        ?>
                    </h3>
                    <p>Услуг</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <h3>
                        <?php 
                        $stmt = $pdo->query("SELECT COUNT(*) FROM requests");
                        echo $stmt->fetchColumn();
                        ?>
                    </h3>
                    <p>Заявок</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <h3>
                        <?php 
                        $stmt = $pdo->query("SELECT COUNT(*) FROM certificates");
                        echo $stmt->fetchColumn();
                        ?>
                    </h3>
                    <p>Сертификатов</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Основные разделы админки -->
    <div class="row">
        <!-- Управление услугами -->
        <div class="col-md-6 mb-4">
            <div class="card admin-card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">🛠️ Управление услугами</h5>
                </div>
                <div class="card-body">
                    <p>Добавление, редактирование и удаление услуг</p>
                    <div class="d-grid gap-2">
                        <a href="services.php" class="btn btn-outline-danger">Список услуг</a>
                        <a href="services.php?action=add" class="btn btn-outline-danger">Добавить услугу</a>
                        <a href="upload_images.php" class="btn btn-outline-danger">Управление изображениями</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Управление заявками (только кнопка "Все заявки") -->
        <div class="col-md-6 mb-4">
            <div class="card admin-card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">📋 Управление заявками</h5>
                </div>
                <div class="card-body">
                    <p>Просмотр и обработка заявок от пользователей</p>
                    <div class="d-grid gap-2">
                        <a href="requests.php" class="btn btn-outline-danger">Все заявки</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Управление сертификатами -->
        <div class="col-md-6 mb-4">
            <div class="card admin-card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">📜 Управление сертификатами</h5>
                </div>
                <div class="card-body">
                    <p>Добавление и управление сертификатами компании</p>
                    <div class="d-grid gap-2">
                        <a href="certificates.php" class="btn btn-outline-danger">Список сертификатов</a>
                        <a href="certificates.php?action=add" class="btn btn-outline-danger">Добавить сертификат</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Управление пользователями -->
        <div class="col-md-6 mb-4">
            <div class="card admin-card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">👥 Управление пользователями</h5>
                </div>
                <div class="card-body">
                    <p>Просмотр и управление пользователями системы</p>
                    <div class="d-grid gap-2">
                        <a href="users.php" class="btn btn-outline-danger">Список пользователей</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 text-center py-3" style="background-color: #900; color: white;">
    © 2026 Пожарная Безопасность - Админ-панель
</footer>
</body>
</html>