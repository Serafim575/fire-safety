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
    if (isset($_POST['add_service'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $image = trim($_POST['image']);
        
        $stmt = $pdo->prepare("INSERT INTO services (title, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $price, $image]);
        $message = "✅ Услуга успешно добавлена";
        $action = 'list';
    }
    
    if (isset($_POST['edit_service'])) {
        $id = (int)$_POST['id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $image = trim($_POST['image']);
        
        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $description, $price, $image, $id]);
        $message = "✅ Услуга успешно обновлена";
        $action = 'list';
    }
    
    if (isset($_POST['delete_service'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $message = "✅ Услуга успешно удалена";
    }
}

$services = $pdo->query("SELECT * FROM services WHERE id != 8 ORDER BY id")->fetchAll();

$images_dir = '../uploads/services/';
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
    <title>Управление услугами - Админ-панель</title>
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
    <h1>Управление услугами</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    
    <div class="mb-4">
        <a href="?action=list" class="btn btn-outline-primary">Список услуг</a>
        <a href="?action=add" class="btn btn-outline-success">Добавить услугу</a>
        <a href="upload_images.php" class="btn btn-outline-secondary">Управление изображениями</a>
    </div>
    
    <?php if ($action === 'list'): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Список услуг (<?= count($services) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Описание</th>
                                <th>Цена</th>
                                <th>Изображение</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?= $service['id'] ?></td>
                                <td><strong><?= htmlspecialchars($service['title']) ?></strong></td>
                                <td><?= htmlspecialchars(mb_substr($service['description'], 0, 50)) ?>...</td>
                                <td><?= number_format($service['price'], 0, '', ' ') ?> руб.</td>
                                <td>
                                    <?php if ($service['image'] && file_exists("../uploads/services/{$service['image']}")): ?>
                                        <span class="text-success">✅ <?= $service['image'] ?></span>
                                    <?php else: ?>
                                        <span class="text-danger">❌ Нет изображения</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?= $service['id'] ?>" class="btn btn-sm btn-warning">✏️</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Удалить услугу?')">
                                        <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                        <button type="submit" name="delete_service" class="btn btn-sm btn-danger">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <?php
        $service = null;
        if ($action === 'edit' && isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $service = $stmt->fetch();
        }
        ?>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?= $action === 'add' ? 'Добавление услуги' : 'Редактирование услуги' ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if ($service): ?>
                        <input type="hidden" name="id" value="<?= $service['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Название услуги</label>
                        <input type="text" name="title" class="form-control" 
                               value="<?= $service ? htmlspecialchars($service['title']) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="4" required><?= $service ? htmlspecialchars($service['description']) : '' ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Цена (руб.)</label>
                        <input type="number" name="price" class="form-control" step="0.01"
                               value="<?= $service ? $service['price'] : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Изображение</label>
                        <select name="image" class="form-select" required>
                            <option value="">-- Выберите изображение --</option>
                            <?php foreach ($available_images as $image): ?>
                                <option value="<?= $image ?>" 
                                    <?= $service && $service['image'] === $image ? 'selected' : '' ?>>
                                    <?= $image ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            <a href="upload_images.php">Загрузить новые изображения</a>
                        </div>
                    </div>
                    
                    <button type="submit" name="<?= $action === 'add' ? 'add_service' : 'edit_service' ?>" 
                            class="btn btn-success">
                        <?= $action === 'add' ? 'Добавить услугу' : 'Сохранить изменения' ?>
                    </button>
                    <a href="?action=list" class="btn btn-secondary">Отмена</a>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>