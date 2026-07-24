<?php
session_start();
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    die("Доступ запрещен. Только для администраторов.");
}

// Правильные имена файлов (без пробелов, с расширениями)
$image_fixes = [
    'gn jpg' => 'audit.jpg',
    'alarm jpg' => 'alarm.jpg', 
    'fire_extinguisher jpg' => 'fire_extinguisher.jpg',
    'training jpg' => 'training.jpg',
    'evacuation_plan.jpg' => 'evacuation_plan.jpg',
    'fire_system jpg' => 'fire_system.jpg'
];

$results = [];

if (isset($_POST['fix_images'])) {
    foreach ($image_fixes as $old_name => $new_name) {
        $stmt = $pdo->prepare("UPDATE services SET image = ? WHERE image = ?");
        $stmt->execute([$new_name, $old_name]);
        
        if ($stmt->rowCount() > 0) {
            $results[] = "✅ {$old_name} -> {$new_name} (обновлено записей: {$stmt->rowCount()})";
        } else {
            $results[] = "⚠️ {$old_name} -> {$new_name} (не найдено для обновления)";
        }
    }
}

// Проверим текущее состояние
$services = $pdo->query("SELECT id, title, image FROM services ORDER BY id")->fetchAll();

// Проверим существование файлов
$images_dir = '../uploads/services/';
$file_status = [];
foreach ($services as $service) {
    $file_path = $images_dir . $service['image'];
    $file_status[$service['id']] = file_exists($file_path);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Исправление имен изображений - Админ-панель</title>
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
    <h1>Исправление имен изображений в БД</h1>
    
    <?php if (!empty($results)): ?>
        <div class="alert alert-info">
            <h5>Результаты исправления:</h5>
            <?php foreach ($results as $result): ?>
                <div><?= $result ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Исправить имена изображений
                </div>
                <div class="card-body">
                    <p class="text-danger"><strong>Проблема:</strong> в БД имена файлов содержат пробелы и неправильные расширения.</p>
                    <form method="POST">
                        <button type="submit" name="fix_images" class="btn btn-warning btn-lg">Исправить имена в БД</button>
                    </form>
                    
                    <hr>
                    <h6>Будут исправлены:</h6>
                    <ul class="list-group">
                        <?php foreach ($image_fixes as $old => $new): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-danger"><s><?= $old ?></s></span>
                                <span class="text-success"><?= $new ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Текущее состояние услуг
                </div>
                <div class="card-body">
                    <div style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Услуга</th>
                                    <th>Изображение</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?= $service['id'] ?></td>
                                        <td><small><?= htmlspecialchars($service['title']) ?></small></td>
                                        <td>
                                            <?php if (strpos($service['image'], ' ') !== false): ?>
                                                <span class="badge bg-danger">❌ <?= $service['image'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-success">✅ <?= $service['image'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($file_status[$service['id']]): ?>
                                                <span class="badge bg-success">✅ Файл есть</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">❌ Файла нет</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="services.php" class="btn btn-secondary">Вернуться к услугам</a>
        <a href="upload_images.php" class="btn btn-primary">Загрузить изображения</a>
    </div>
</div>
</body>
</html>