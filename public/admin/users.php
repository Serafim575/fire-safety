<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    die("Доступ запрещен. Только для администраторов.");
}
$message = '';

// Обработка назначения админа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_admin'])) {
    $user_id = (int)$_POST['user_id'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // Не позволяем снять админские права с самого себя
    if ($user_id == $_SESSION['user_id'] && $is_admin == 0) {
        $message = "❌ Нельзя снять админские права с самого себя";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->execute([$is_admin, $user_id]);
        $message = "✅ Права пользователя обновлены";
    }
}

// Получаем список пользователей
$users = $pdo->query("SELECT id, email, full_name, created_at, is_admin FROM users ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление пользователями - Админ-панель</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">👑 АДМИН-ПАНЕЛЬ</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="../index.php">На сайт</a>
            <a class="nav-link" href="logout.php">Выход</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Управление пользователями</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    
    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3><?= count($users) ?></h3>
                    <p>Всего пользователей</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3><?= count(array_filter($users, fn($u) => $u['is_admin'])) ?></h3>
                    <p>Администраторов</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3><?= count(array_filter($users, fn($u) => !$u['is_admin'])) ?></h3>
                    <p>Обычных пользователей</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Список пользователей -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Список пользователей (<?= count($users) ?>)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Email</th>
                            <th>Дата регистрации</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><strong><?= htmlspecialchars($user['full_name']) ?></strong></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge bg-danger">Администратор</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Пользователь</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_admin" value="1" 
                                               <?= $user['is_admin'] ? 'checked' : '' ?>
                                               onchange="this.form.submit()"
                                               <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                        <input type="hidden" name="toggle_admin" value="1">
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>