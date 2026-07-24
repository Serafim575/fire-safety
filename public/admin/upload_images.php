<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    die("Доступ запрещен. Только для администраторов.");
}
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $upload_dir = '../uploads/services/';
    
    // Создаем папку если не существует
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $uploaded_files = [];
    
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
            $file_name = basename($_FILES['images']['name'][$key]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_ext, $allowed_ext)) {
                $new_file_name = uniqid() . '.' . $file_ext;
                $destination = $upload_dir . $new_file_name;
                
                if (move_uploaded_file($tmp_name, $destination)) {
                    $uploaded_files[] = $new_file_name;
                    $message .= "✅ Файл {$file_name} успешно загружен как {$new_file_name}<br>";
                } else {
                    $message .= "❌ Ошибка загрузки файла {$file_name}<br>";
                }
            } else {
                $message .= "❌ Неподдерживаемый формат файла: {$file_name}<br>";
            }
        }
    }
    
    // Показываем список загруженных файлов для копирования в БД
    if (!empty($uploaded_files)) {
        $message .= "<hr><strong>Загруженные файлы:</strong><br>";
        foreach ($uploaded_files as $file) {
            $message .= "📁 <code>{$file}</code><br>";
        }
    }
}

// Получаем существующие изображения
$images_dir = '../uploads/services/';
$existing_images = [];
if (is_dir($images_dir)) {
    $existing_images = glob($images_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $existing_images = array_map('basename', $existing_images);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка изображений - Админ-панель</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">👑 АДМИН-ПАНЕЛЬ</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="services.php">Услуги</a>
            <a class="nav-link" href="logout.php">Выход</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Загрузка изображений для услуг</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Загрузить новые изображения
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Выберите изображения</label>
                            <input type="file" name="images[]" multiple class="form-control" accept="image/*" required>
                            <div class="form-text">Можно выбрать несколько файлов (JPG, PNG, GIF)</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Загрузить</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Существующие изображения (<?= count($existing_images) ?>)
                </div>
                <div class="card-body">
                    <?php if (empty($existing_images)): ?>
                        <p class="text-muted">Нет загруженных изображений</p>
                    <?php else: ?>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($existing_images as $image): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                    <span>📁 <?= $image ?></span>
                                    <div>
                                        <small>
                                            <a href="../uploads/services/<?= $image ?>" target="_blank" class="btn btn-sm btn-outline-primary">просмотр</a>
                                            <a href="../uploads/services/<?= $image ?>" download class="btn btn-sm btn-outline-secondary">скачать</a>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="services.php" class="btn btn-secondary">Вернуться к услугам</a>
        <a href="fix_image_names.php" class="btn btn-warning">Исправить имена в БД</a>
    </div>
</div>
</body>
</html>