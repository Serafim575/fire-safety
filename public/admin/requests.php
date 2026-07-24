<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    die("Доступ запрещен. Только для администраторов.");
}

// Получаем ВСЕ заявки
$sql = "SELECT r.*, u.full_name, u.email, s.title as service_title 
        FROM requests r 
        JOIN users u ON r.user_id = u.id 
        JOIN services s ON r.service_id = s.id
        ORDER BY r.created_at DESC";

$stmt = $pdo->query($sql);
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявки - Админ-панель</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #900;">
    <div class="container">
        <a class="navbar-brand" href="index.php">👑 АДМИН-ПАНЕЛЬ</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="../services.php">На сайт</a>
            <a class="nav-link" href="logout.php">Выход</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Все заявки</h1>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Список заявок (<?= count($requests) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($requests)): ?>
                <p class="text-center text-muted">Заявок пока нет</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Клиент</th>
                                <th>Услуга</th>
                                <th>Телефон</th>
                                <th>Email</th>
                                <th>Комментарий</th>
                                <th>Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?= $request['id'] ?></td>
                                <td><strong><?= htmlspecialchars($request['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($request['service_title']) ?></td>
                                <td><?= htmlspecialchars($request['customer_phone'] ?? 'не указан') ?></td>
                                <td><?= htmlspecialchars($request['customer_email'] ?? 'не указан') ?></td>
                                <td>
                                    <?php if (!empty($request['comments'])): ?>
                                        <small><?= htmlspecialchars(mb_substr($request['comments'], 0, 80)) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                 </div>
                                <td><?= date('d.m.Y H:i', strtotime($request['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>