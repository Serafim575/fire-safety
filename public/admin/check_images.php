<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    die("Доступ запрещен. Только для администраторов.");
}

// Проверяем услуги
$services = $pdo->query("SELECT id, title, image FROM services")->fetchAll();

// Проверяем сертификаты
$certificates = [];
try {
    $certificates = $pdo->query("SELECT id, title, image FROM certificates")->fetchAll();
} catch (PDOException $e) {
    $certificates_error = "Таблица certificates не существует";
}

// Проверяем существование файлов
$service_images_dir = '../uploads/services/';
$certificate_images_dir = '../uploads/certificates/';

$service_stats = [
    'total' => count($services),
    'with_images' => 0,
    'without_images' => 0,
    'broken_names' => 0
];

$certificate_stats = [
    'total' => count($certificates),
    'with_images' => 0,
    'without_images' => 0
];

foreach ($services as $service) {
    $file_path = $service_images_dir . $service['image'];
    if (file_exists($file_path)) {
        $service_stats['with_images']++;
    } else {
        $service_stats['without_images']++;
    }
    
    if (strpos($service['image'], ' ') !== false) {
        $service_stats['broken_names']++;
    }
}

foreach ($certificates as $certificate) {
    $file_path = $certificate_images_dir . $certificate['image'];
    if (file_exists($file_path)) {
        $certificate_stats['with_images']++;
    } else {
        $certificate_stats['without_images']++;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Проверка системы - Админ-панель</title>
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php">👑 АДМИН-ПАНЕЛЬ</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="logout.php">Выход</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1>Проверка состояния системы</h1>
    
    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center <?= $service_stats['without_images'] > 0 ? 'border-warning' : 'border-success' ?>">
                <div class="card-body">
                    <h3><?= $service_stats['total'] ?></h3>
                    <p>Услуг всего</p>
                    <small class="<?= $service_stats['without_images'] > 0 ? 'text-warning' : 'text-success' ?>">
                        <?= $service_stats['with_images'] ?> с изображениями<br>
                        <?= $service_stats['without_images'] ?> без изображений
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center <?= $service_stats['broken_names'] > 0 ? 'border-danger' : 'border-success' ?>">
                <div class="card-body">
                    <h3><?= $service_stats['broken_names'] ?></h3>
                    <p>Неправильных имен</p>
                    <small class="<?= $service_stats['broken_names'] > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= $service_stats['broken_names'] ?> с пробелами в именах
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3><?= $certificate_stats['total'] ?></h3>
                    <p>Сертификатов</p>
                    <small>
                        <?= $certificate_stats['with_images'] ?> с изображениями<br>
                        <?= $certificate_stats['without_images'] ?> без изображений
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Детальная информация об услугах -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Детальная проверка услуг</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Услуга</th>
                            <th>Изображение в БД</th>
                            <th>Файл на сервере</th>
                            <th>Имя файла</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): 
                            $file_path = $service_images_dir . $service['image'];
                            $file_exists = file_exists($file_path);
                            $has_spaces = strpos($service['image'], ' ') !== false;
                        ?>
                        <tr>
                            <td><?= $service['id'] ?></td>
                            <td><strong><?= htmlspecialchars($service['title']) ?></strong></td>
                            <td><code><?= $service['image'] ?></code></td>
                            <td>
                                <?php if ($file_exists): ?>
                                    <span class="badge bg-success">✅ Существует</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">❌ Не существует</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($has_spaces): ?>
                                    <span class="badge bg-danger">❌ Есть пробелы</span>
                                <?php else: ?>
                                    <span class="badge bg-success">✅ OK</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($file_exists && !$has_spaces): ?>
                                    <span class="badge bg-success">✅ OK</span>
                                <?php elseif (!$file_exists && $has_spaces): ?>
                                    <span class="badge bg-danger">❌ Критично</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">⚠️ Проблема</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Действия -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Быстрые действия</h5>
        </div>
        <div class="card-body">
            <div class="d-grid gap-2 d-md-flex">
                <a href="upload_images.php" class="btn btn-primary">Загрузить изображения услуг</a>
                <a href="fix_image_names.php" class="btn btn-warning">Исправить имена в БД</a>
                <a href="services.php" class="btn btn-secondary">Управление услугами</a>
                <a href="certificates.php" class="btn btn-info">Управление сертификатами</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>