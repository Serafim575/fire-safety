<?php
require 'includes/auth.php';
require 'includes/db.php';

// Получаем услуги из базы данных
try {
    $stmt = $pdo->query("SELECT * FROM services WHERE id != 8 ORDER BY price ASC");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $services = [];
    $error = "Ошибка загрузки услуг: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Услуги - Пожарная Безопасность</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .service-card {
            transition: all 0.3s ease;
            border: 1px solid #ffcccc;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(204, 0, 0, 0.15);
            border-color: #c00;
        }
        .service-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .placeholder-image {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-weight: bold;
        }
        .price-tag {
            background: linear-gradient(45deg, #c00, #900);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php" style="font-weight: 800; font-size: 1.8rem; color: #c00 !important;">
            ПОЖАРНАЯ БЕЗОПАСНОСТЬ
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link" href="about.php">О нас</a>
            <a class="nav-link active" href="services.php">Услуги</a>
            <a class="nav-link" href="contacts.php">Контакты</a>
            <?php if (isAdmin()): ?>
                <a class="nav-link text-warning" href="admin/index.php">👑 Админка</a>
            <?php endif; ?>
            <a class="nav-link" href="logout.php">Выход (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center mb-4" style="color: #c00;">Наши услуги</h1>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= $error ?>
            <br><small>Попробуйте <a href="init_database.php">инициализировать базу данных</a></small>
        </div>
    <?php endif; ?>
    
    <?php if (empty($services)): ?>
        <div class="alert alert-warning text-center">
            <h4>😔 Услуги не найдены</h4>
            <p>В базе данных нет услуг или произошла ошибка</p>
            <a href="init_database.php" class="btn btn-primary">Добавить тестовые услуги</a>
            <a href="admin/services.php?action=add" class="btn btn-success">Добавить услугу через админку</a>
        </div>
    <?php else: ?>
        <p class="text-center mb-5">Полный комплекс услуг в области пожарной безопасности для бизнеса и частных клиентов</p>
        
        <div class="row">
            <?php foreach ($services as $service): 
                $image_path = "uploads/services/" . $service['image'];
                $image_exists = file_exists($image_path);
            ?>
            <div class="col-md-4 mb-4">
                <div class="card service-card">
                    <?php if ($image_exists): ?>
                        <img src="<?= $image_path ?>" class="card-img-top service-image" alt="<?= htmlspecialchars($service['title']) ?>">
                    <?php else: ?>
                        <div class="placeholder-image">
                            <div class="text-center">
                                <div>📷</div>
                                <small>Изображение не найдено</small>
                                <br><small><?= $service['image'] ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($service['title']) ?></h5>
                        <p class="card-text flex-grow-1"><?= htmlspecialchars($service['description']) ?></p>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price-tag">от <?= number_format($service['price'], 0, '', ' ') ?> ₽</span>
                            </div>
                            <a href="submit_request.php?service_id=<?= $service['id'] ?>" class="btn btn-primary w-100">Заказать услугу</a>
                            
                            <?php if (isAdmin()): ?>
                                <!-- Отладочная информация только для админов -->
                                 <small class="text-muted d-block mt-2">
                                 ID: <?= $service['id'] ?> | 
                                 Изобр: <?= $image_exists ? '✅' : '❌' ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Информация если нет изображений -->
    <?php
    $missing_images = 0;
    foreach ($services as $service) {
        if (!file_exists("uploads/services/" . $service['image'])) {
            $missing_images++;
        }
    }
    ?>
    
    <?php if ($missing_images > 0): ?>
        <div class="alert alert-info mt-4">
            <h6>📷 Информация об изображениях</h6>
            <p>Не найдено изображений для <?= $missing_images ?> из <?= count($services) ?> услуг.</p>
            <a href="admin/upload_images.php" class="btn btn-sm btn-outline-primary">Загрузить изображения</a>
            <a href="admin/fix_image_names.php" class="btn btn-sm btn-outline-warning">Исправить имена в БД</a>
        </div>
    <?php endif; ?>
</div>

<footer class="mt-5">
    <div class="container">
        © 2026 Пожарная Безопасность.
    </div>
</footer>
</body>
</html>