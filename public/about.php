<?php 
require 'includes/auth.php';
require 'includes/db.php';

// Получаем сертификаты из базы данных
try {
    $stmt = $pdo->query("SELECT * FROM certificates ORDER BY issued_date DESC");
    $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $certificates = [];
    $certificates_error = "Ошибка загрузки сертификатов: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>О нас - Пожарная Безопасность</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .certificate-card {
            transition: all 0.3s ease;
            border: 1px solid #ffcccc;
            height: 100%;
            cursor: pointer;
        }
        .certificate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(204, 0, 0, 0.15);
            border-color: #c00;
        }
        .certificate-image {
            height: 200px;
            object-fit: contain;
            width: 100%;
            padding: 15px;
            background: #f8f9fa;
            transition: transform 0.3s ease;
        }
        .certificate-card:hover .certificate-image {
            transform: scale(1.05);
        }
        .placeholder-image {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            cursor: pointer;
        }
        .certificate-title {
            color: #c00;
            font-weight: bold;
            font-size: 0.95em;
        }
        .issued-date {
            color: #666;
            font-size: 0.85em;
        }
        .stats-card {
            background: linear-gradient(45deg, #c00, #900);
            color: white;
        }
        
        /* Стили для модального окна */
        .modal-certificate-image {
            max-width: 100%;
            max-height: 80vh;
            width: auto;
            height: auto;
            margin: 0 auto;
            display: block;
        }
        .modal-content {
            border: 3px solid #c00;
            border-radius: 10px;
        }
        .zoom-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .certificate-card:hover .zoom-icon {
            opacity: 1;
        }
        .company-values {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="index.php" style="font-weight: 800; font-size: 1.8rem; color: #c00 !important; text-decoration: none;">
            ПОЖАРНАЯ БЕЗОПАСНОСТЬ
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Главная</a>
            <a class="nav-link active" href="about.php">О нас</a>
            <a class="nav-link" href="services.php">Услуги</a>
            <a class="nav-link" href="contacts.php">Контакты</a>
            <?php if (isAdmin()): ?>
                <a class="nav-link text-warning" href="admin/index.php">👑 Админка</a>
            <?php endif; ?>
            <a class="nav-link" href="logout.php">Выход (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1 class="text-center mb-4" style="color: #c00;">О нашей компании</h1>
    
    <!-- Статистика компании -->
    <div class="row text-center mb-5">
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <h2>10+</h2>
                    <p>Лет опыта</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <h2>500+</h2>
                    <p>Довольных клиентов</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <h2><?= count($certificates) ?></h2>
                    <p>Сертификатов</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <h2>100%</h2>
                    <p>Гарантия качества</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Основная информация -->
    <div class="company-values">
        <div class="row">
            <div class="col-md-6">
                <h3 class="text-danger mb-3">🏢 Наша миссия</h3>
                <p class="lead">Обеспечение пожарной безопасности объектов любой сложности с применением современных технологий и оборудования.</p>
                <p>Мы стремимся сделать мир безопаснее, предотвращая пожары и минимизируя риски для жизни и имущества. Наша работа - это гарантия спокойствия и защищенности для наших клиентов.</p>
            </div>
            <div class="col-md-6">
                <h3 class="text-danger mb-3">📈 Наш опыт</h3>
                <p class="lead">Более 10 лет на рынке пожарной безопасности. Более 500 довольных клиентов.</p>
                <p>Среди наших клиентов - крупные промышленные предприятия, бизнес-центры, образовательные и медицинские учреждения. Мы гордимся каждым успешно реализованным проектом.</p>
            </div>
        </div>
    </div>

    <!-- Ценности компании -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4" style="color: #c00;">Наши ценности</h2>
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 3rem; color: #c00;">🛡️</div>
                            <h5>Надежность</h5>
                            <p>Мы гарантируем качество всех предоставляемых услуг и используемого оборудования</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 3rem; color: #c00;">⚡</div>
                            <h5>Оперативность</h5>
                            <p>Быстрое реагирование на запросы клиентов и сжатые сроки выполнения работ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 3rem; color: #c00;">📚</div>
                            <h5>Профессионализм</h5>
                            <p>Высококвалифицированные специалисты с многолетним опытом работы</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Сертификаты -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4" style="color: #c00;">Наши сертификаты и лицензии</h2>
            
            <?php if (isset($certificates_error)): ?>
                <div class="alert alert-danger text-center">
                    <h5>❌ Ошибка загрузки сертификатов</h5>
                    <?= $certificates_error ?>
                    <?php if (isAdmin()): ?>
                        <br><a href="update_certificates_5.php" class="btn btn-sm btn-primary mt-2">Добавить сертификаты</a>
                    <?php endif; ?>
                </div>
            <?php elseif (empty($certificates)): ?>
                <div class="alert alert-warning text-center">
                    <h5>📜 Сертификаты не найдены</h5>
                    <p>В базе данных нет сертификатов</p>
                    <?php if (isAdmin()): ?>
                        <a href="update_certificates_5.php" class="btn btn-primary">Добавить 5 сертификатов</a>
                    <?php else: ?>
                        <p class="text-muted">Информация о сертификатах будет добавлена в ближайшее время</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-center mb-4">Мы работаем в строгом соответствии с законодательством и имеем все необходимые лицензии и сертификаты. <strong>Нажмите на любой сертификат для увеличения.</strong></p>
                
                <div class="row justify-content-center">
                    <?php foreach ($certificates as $certificate): 
                        $image_path = "uploads/certificates/" . $certificate['image'];
                        $image_exists = file_exists($image_path);
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card certificate-card" data-bs-toggle="modal" data-bs-target="#certificateModal" 
                             data-certificate-title="<?= htmlspecialchars($certificate['title']) ?>"
                             data-certificate-description="<?= htmlspecialchars($certificate['description']) ?>"
                             data-certificate-date="<?= date('d.m.Y', strtotime($certificate['issued_date'])) ?>"
                             data-certificate-image="<?= $image_exists ? $image_path : '' ?>">
                            
                            <?php if ($image_exists): ?>
                                <div class="position-relative">
                                    <img src="<?= $image_path ?>" class="card-img-top certificate-image" alt="<?= htmlspecialchars($certificate['title']) ?>">
                                    <div class="zoom-icon">🔍</div>
                                </div>
                            <?php else: ?>
                                <div class="placeholder-image">
                                    <div class="text-center">
                                        <div style="font-size: 2.5rem;">📄</div>
                                        <small>Нажмите для просмотра</small>
                                        <?php if (isAdmin()): ?>
                                            <br><small class="text-danger">Файл: <?= $certificate['image'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h6 class="certificate-title"><?= htmlspecialchars($certificate['title']) ?></h6>
                                <?php if (!empty($certificate['description'])): ?>
                                    <p class="card-text small text-muted"><?= htmlspecialchars(mb_substr($certificate['description'], 0, 80)) ?>...</p>
                                <?php endif; ?>
                                <div class="issued-date">
                                    Выдан: <?= date('d.m.Y', strtotime($certificate['issued_date'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Модальное окно для увеличения сертификата -->
    <div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateModalTitle">Сертификат</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalCertificateImage" src="" alt="" class="modal-certificate-image">
                    <div class="mt-3">
                        <h6 id="modalCertificateName" class="text-danger"></h6>
                        <p id="modalCertificateDescription" class="small text-muted"></p>
                        <p id="modalCertificateDate" class="small text-muted"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5">
    <div class="container">
        © 2026 Пожарная Безопасность. Все права защищены.
    </div>
</footer>

<script src="assets/js/bootstrap.bundle.js"></script>
<script>
    // Обработка клика по сертификату для открытия модального окна
    document.addEventListener('DOMContentLoaded', function() {
        const certificateModal = document.getElementById('certificateModal');
        
        certificateModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const title = button.getAttribute('data-certificate-title');
            const description = button.getAttribute('data-certificate-description');
            const date = button.getAttribute('data-certificate-date');
            const image = button.getAttribute('data-certificate-image');
            
            document.getElementById('certificateModalTitle').textContent = title;
            document.getElementById('modalCertificateName').textContent = title;
            document.getElementById('modalCertificateDescription').textContent = description;
            document.getElementById('modalCertificateDate').textContent = 'Дата выдачи: ' + date;
            
            const modalImage = document.getElementById('modalCertificateImage');
            if (image) {
                modalImage.src = image;
                modalImage.alt = title;
                modalImage.style.display = 'block';
            } else {
                modalImage.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>