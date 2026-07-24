<?php
require 'includes/db.php';

try {
    // Создаем таблицу для слайдера
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS slider_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            image_path VARCHAR(255) NOT NULL,
            button_text VARCHAR(100) DEFAULT 'Подробнее',
            button_link VARCHAR(255) DEFAULT '#',
            is_active BOOLEAN DEFAULT TRUE,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Добавляем тестовые изображения для слайдера
    $slider_images = [
        ['Пожарная безопасность для бизнеса', 'Профессиональные решения для защиты вашего предприятия от пожара', 'slider1.jpg', 'Наши услуги', 'services.php'],
        ['Современные системы пожаротушения', 'Установка и обслуживание автоматических систем пожаротушения', 'slider2.jpg', 'Узнать больше', 'about.php'],
        ['Аудит и консультации', 'Полная проверка объекта на соответствие нормам пожарной безопасности', 'slider3.jpg', 'Заказать аудит', 'services.php']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO slider_images (title, description, image_path, button_text, button_link) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($slider_images as $image) {
        $stmt->execute($image);
    }
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Создание слайдера</title>
        <link href='assets/css/bootstrap.css' rel='stylesheet'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-success'>
            <h4>✅ Таблица слайдера создана!</h4>
            <p>Добавлены 3 тестовых изображения для слайдера:</p>
            <ol>
                <li>Пожарная безопасность для бизнеса</li>
                <li>Современные системы пожаротушения</li>
                <li>Аудит и консультации</li>
            </ol>
        </div>
        <a href='index.php' class='btn btn-primary'>Перейти на главную</a>
    </body>
    </html>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>
            <h4>❌ Ошибка:</h4>
            <p>" . $e->getMessage() . "</p>
          </div>";
}
?>