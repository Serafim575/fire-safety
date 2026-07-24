<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    die("Доступ запрещен. Только для администраторов.");
}

$action = $_GET['action'] ?? 'list';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_certificate'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $image = trim($_POST['image']);
        $issued_date = $_POST['issued_date'];
        
        $stmt = $pdo->prepare("INSERT INTO certificates (title, description, image, issued_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $image, $issued_date]);
        $message = "✅ Сертификат успешно добавлен";
        $action = 'list';
    }
    
    if (isset($_POST['edit_certificate'])) {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $image = trim($_POST['image']);
        $issued_date = $_POST['issued_date'];
        
        $stmt = $pdo->prepare("UPDATE certificates SET title = ?, description = ?, image = ?, issued_date = ? WHERE id = ?");
        $stmt->execute([$title, $description, $image, $issued_date, $id]);
        $message = "✅ Сертификат успешно обновлен";
        $action = 'list';
    }
    
    if (isset($_POST['delete_certificate'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
        $stmt->execute([$id]);
        $message = "✅ Сертификат успешно удален";
    }
}

$certificates = $pdo->query("SELECT * FROM certificates ORDER BY issued_date DESC")->fetchAll();

$images_dir = '../uploads/certificates/';
$available_images = [];
if (is_dir($images_dir)) {
    $available_images = glob($images_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $available_images = array_map('basename', $available_images);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление сертификатами - Админ-панель</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #900;">
    <div class="container">
        <a class="navbar-brand" href="index.php">👑 АДМИН-ПАНЕЛЬ</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="../about.php">На сайт</a>
            <a class="nav-link" href="logout.php">Выход</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Управление сертификатами</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    
    <div class="mb-4">
        <a href="?action=list" class="btn btn-outline-primary">Список сертификатов</a>
        <a href="?action=add" class="btn btn-outline-success">Добавить сертификат</a>
        <a href="upload_certificate_images.php" class="btn btn-outline-secondary">Загрузить изображения</a>
    </div>
    
    <?php if ($action === 'list'): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Список сертификатов (<?= count($certificates) ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($certificates)): ?>
                    <p class="text-center text-muted">Сертификатов нет</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Дата выдачи</th>
                                    <th>Изображение</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($certificates as $certificate): ?>
                                <tr>
                                    <td><?= $certificate['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($certificate['title']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars(mb_substr($certificate['description'], 0, 50)) ?>...</small>
                                    </td>
                                    <td><?= date('d.m.Y', strtotime($certificate['issued_date'])) ?></td>
                                    <td>
                                        <?php if ($certificate['image'] && file_exists("../uploads/certificates/{$certificate['image']}")): ?>
                                            <span class="text-success">✅ <?= $certificate['image'] ?></span>
                                        <?php else: ?>
                                            <span class="text-danger">❌ Нет изображения</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="?action=edit&id=<?= $certificate['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить сертификат?')">
                                            <input type="hidden" name="id" value="<?= $certificate['id'] ?>">
                                            <button type="submit" name="delete_certificate" class="btn btn-sm btn-danger">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <?php
        $certificate = null;
        if ($action === 'edit' && isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $certificate = $stmt->fetch();
        }
        ?>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?= $action === 'add' ? 'Добавление сертификата' : 'Редактирование сертификата' ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if ($certificate): ?>
                        <input type="hidden" name="id" value="<?= $certificate['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Название сертификата</label>
                        <input type="text" name="title" class="form-control" 
                               value="<?= $certificate ? htmlspecialchars($certificate['title']) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3"><?= $certificate ? htmlspecialchars($certificate['description']) : '' ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Дата выдачи</label>
                        <input type="date" name="issued_date" class="form-control"
                               value="<?= $certificate ? $certificate['issued_date'] : date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Изображение</label>
                        <select name="image" class="form-select" required>
                            <option value="">-- Выберите изображение --</option>
                            <?php foreach ($available_images as $image): ?>
                                <option value="<?= $image ?>" 
                                    <?= $certificate && $certificate['image'] === $image ? 'selected' : '' ?>>
                                    <?= $image ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            <a href="upload_certificate_images.php">Загрузить новые изображения</a>
                        </div>
                    </div>
                    
                    <button type="submit" name="<?= $action === 'add' ? 'add_certificate' : 'edit_certificate' ?>" 
                            class="btn btn-success">
                        <?= $action === 'add' ? 'Добавить сертификат' : 'Сохранить изменения' ?>
                    </button>
                    <a href="?action=list" class="btn btn-secondary">Отмена</a>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>