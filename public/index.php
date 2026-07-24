<?php 
require 'includes/auth.php';
require 'includes/db.php';

// Получаем изображения для слайдера
try {
    $stmt = $pdo->query("SELECT * FROM slider_images WHERE is_active = TRUE ORDER BY sort_order, created_at DESC LIMIT 3");
    $slider_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $slider_images = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пожарная Безопасность - Главная</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
    
    <!-- Фавикон -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <style>
        /* Стили для слайдера */
        .hero-slider {
            height: 70vh;
            min-height: 500px;
            position: relative;
            overflow: hidden;
        }
        .carousel-item {
            height: 70vh;
            min-height: 500px;
        }
        .carousel-item img {
            object-fit: cover;
            height: 100%;
            width: 100%;
            filter: brightness(0.7);
        }
        .carousel-caption {
            bottom: 50%;
            transform: translateY(50%);
            text-align: center;
        }
        .carousel-caption h1 {
            font-size: 3.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
            margin-bottom: 1rem;
        }
        .carousel-caption p {
            font-size: 1.3rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
            margin-bottom: 2rem;
        }
        .btn-slider {
            background: linear-gradient(45deg, #c00, #900);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        .btn-slider:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(204, 0, 0, 0.4);
        }
        
        /* Стили для навигации с текстовым логотипом */
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: white !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        .navbar-brand:hover {
            color: #ffcccc !important;
            text-decoration: none;
        }
        
        /* Стили для преимуществ */
        .feature-icon {
            font-size: 3rem;
            color: #c00;
            margin-bottom: 1rem;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        /* Адаптивность */
        @media (max-width: 768px) {
            .hero-slider, .carousel-item {
                height: 50vh;
                min-height: 400px;
            }
            .carousel-caption h1 {
                font-size: 2.5rem;
            }
            .carousel-caption p {
                font-size: 1.1rem;
            }
            .navbar-brand {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
<!-- Навигация с текстовым логотипом -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: rgba(192, 0, 0, 0.95);">
    <div class="container">
        <a class="navbar-brand" href="index.php" style="font-weight: 800; font-size: 1.8rem; color: white !important; text-decoration: none;">
            ПОЖАРНАЯ БЕЗОПАСНОСТЬ
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">О нас</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="services.php">Услуги</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contacts.php">Контакты</a>
                </li>
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="admin/index.php">
                            <i class="bi bi-gear-fill"></i> Админка
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Выход (<?= htmlspecialchars($_SESSION['user_name']) ?>)
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Главный слайдер -->
<div id="heroSlider" class="carousel slide hero-slider" data-bs-ride="carousel">
    <?php if (!empty($slider_images)): ?>
        <!-- Индикаторы -->
        <div class="carousel-indicators">
            <?php foreach ($slider_images as $index => $image): ?>
                <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?= $index ?>" 
                        class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" 
                        aria-label="Slide <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <!-- Слайды -->
        <div class="carousel-inner">
            <?php foreach ($slider_images as $index => $image): 
                $image_path = "uploads/slider/" . $image['image_path'];
                $image_exists = file_exists($image_path);
            ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" data-bs-interval="5000">
                <?php if ($image_exists): ?>
                    <img src="<?= $image_path ?>" class="d-block w-100" alt="<?= htmlspecialchars($image['title']) ?>">
                <?php else: ?>
                    <!-- Заглушка если изображение не найдено -->
                    <div class="d-block w-100 bg-dark" style="height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                        <div class="text-center">
                            <i class="bi bi-image" style="font-size: 4rem;"></i>
                            <h3>Изображение слайдера</h3>
                            <p><?= htmlspecialchars($image['title']) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="carousel-caption">
                    <h1>Пожарная безопасность</h1>
                    <h3><?= htmlspecialchars($image['title']) ?></h3>
                    <p><?= htmlspecialchars($image['description']) ?></p>
                    <a href="<?= htmlspecialchars($image['button_link']) ?>" class="btn btn-slider btn-lg">
                        <?= htmlspecialchars($image['button_text']) ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Кнопки управления -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Предыдущий</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Следующий</span>
        </button>
    <?php else: ?>
        <!-- Заглушка если нет изображений слайдера -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="d-block w-100 bg-danger" style="height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                    <div class="text-center">
                        <h1>Пожарная безопасность</h1>
                        <p class="lead">Профессиональные решения для вашей безопасности</p>
                        <a href="create_slider_table.php" class="btn btn-light btn-lg">Добавить слайдер</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Основной контент -->
<div class="container mt-5">
    <!-- Преимущества -->
    <div class="row text-center mb-5">
        <div class="col-12">
            <h2 class="mb-4" style="color: #c00;">Почему выбирают нас?</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon">🛡️</div>
                    <h5>Надежность</h5>
                    <p>10+ лет успешной работы на рынке пожарной безопасности. Гарантия качества всех услуг.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon">⚡</div>
                    <h5>Оперативность</h5>
                    <p>Быстрое реагирование на заявки. Монтаж и обслуживание в кратчайшие сроки.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card feature-card h-100">
                <div class="card-body">
                    <div class="feature-icon">📋</div>
                    <h5>Профессионализм</h5>
                    <p>Квалифицированные специалисты с соответствующими сертификатами и допусками.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Призыв к действию -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body text-center py-5">
                    <h3 class="text-danger mb-3">Готовы обеспечить пожарную безопасность?</h3>
                    <p class="lead mb-4">Оставьте заявку и мы свяжемся с вами в течение 15 минут</p>
                    <a href="services.php" class="btn btn-danger btn-lg me-3">Посмотреть услуги</a>
                    <a href="contacts.php" class="btn btn-outline-danger btn-lg">Связаться с нами</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Пожарная Безопасность</h5>
                <p>Профессиональные решения в области пожарной безопасности для бизнеса и частных клиентов.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5>Контакты</h5>
                <p>📞 +7 (495) 123-45-67<br>
                   📧 info@pozharka.ru<br>
                   🕐 Пн-Пт: 9:00-18:00</p>
            </div>
        </div>
        <hr>
        <div class="text-center">
            © 2026 Пожарная Безопасность. Все права защищены.
        </div>
    </div>
</footer>

<script src="assets/js/bootstrap.bundle.js"></script>
</body>
</html>