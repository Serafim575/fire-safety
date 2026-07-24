<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    die("Доступ запрещен. Только для администраторов.");
}
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $upload_dir = '../uploads/certificates/';
    $uploaded_files = [];
    
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
            $file_name = basename($_FILES['images']['name'][$key]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_ext, $allowed_ext)) {
                $new_file_name = 'certificate_' . uniqid() . '.' . $file_ext;
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
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка изображений сертификатов - Админ-панель</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">👑 АДМИН-ПАНЕЛЬ</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="certificates.php">Сертификаты</a>
            <a class="nav-link" href="logout.php">Выход</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Загрузка изображений для сертификатов</h1>
    
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
                            <label class="form-label">Выберите изображения сертификатов</label>
                            <input type="file" name="images[]" multiple class="form-control" accept="image/*">
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
                    Существующие изображения
                </div>
                <div class="card-body">
                    <?php
                    $images_dir = '../uploads/certificates/';
                    if (is_dir($images_dir)) {
                        $images = glob($images_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
                        if (empty($images)) {
                            echo "<p>Нет загруженных изображений</p>";
                        } else {
                            echo "<div style='max-height: 300px; overflow-y: auto;'>";
                            foreach ($images as $image) {
                                $file_name = basename($image);
                                echo "<div class='d-flex justify-content-between align-items-center mb-2 p-2 border rounded'>";
                                echo "<span>📁 {$file_name}</span>";
                                echo "<small><a href='../uploads/certificates/{$file_name}' target='_blank'>просмотр</a></small>";
                                echo "</div>";
                            }
                            echo "</div>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="certificates.php" class="btn btn-secondary">Вернуться к сертификатам</a>
    </div>
</div>
</body>
</html>