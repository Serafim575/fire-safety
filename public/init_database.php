<?php
require 'includes/db.php';

$errors = [];
$success = [];

try {
    // 1. Добавляем поле is_admin в users если его нет
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_admin BOOLEAN DEFAULT FALSE");
        $success[] = "✅ Поле is_admin добавлено в таблицу users";
    } catch (PDOException $e) {
        $success[] = "ℹ️ Поле is_admin уже существует";
    }

    // 2. Создаем таблицу certificates если ее нет
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS certificates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                image VARCHAR(255) NOT NULL,
                issued_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $success[] = "✅ Таблица certificates создана";
        
        // Добавляем тестовые сертификаты
        $certificates = [
            ['Сертификат соответствия ГОСТ', 'Соответствие всем требованиям государственных стандартов пожарной безопасности', 'certificate1.jpg', '2024-01-15'],
            ['Лицензия МЧС России', 'Официальная лицензия на осуществление деятельности в области пожарной безопасности', 'certificate2.jpg', '2023-11-20'],
            ['Сертификат ISO 9001', 'Международный стандарт системы менеджмента качества', 'certificate3.jpg', '2024-03-10'],
            ['Сертификат профессиональной подготовки', 'Подтверждение квалификации наших специалистов', 'certificate4.jpg', '2024-02-28']
        ];
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO certificates (title, description, image, issued_date) VALUES (?, ?, ?, ?)");
        foreach ($certificates as $cert) {
            $stmt->execute($cert);
        }
        $success[] = "✅ Добавлены тестовые сертификаты";
        
    } catch (PDOException $e) {
        $errors[] = "❌ Ошибка создания certificates: " . $e->getMessage();
    }

    // 3. Проверяем и обновляем таблицу services
    try {
        // Добавляем тестовые услуги если их мало
        $stmt = $pdo->query("SELECT COUNT(*) FROM services");
        $count = $stmt->fetchColumn();
        
        if ($count < 10) {
            $services = [
                ['Пожарный аудит объекта', 'Комплексная проверка здания на соответствие нормам пожарной безопасности с выдачей официального заключения', 15000.00, 'audit.jpg'],
                ['Установка пожарной сигнализации', 'Монтаж современной системы пожарной сигнализации с датчиками дыма и тепла', 25000.00, 'alarm.jpg'],
                ['Обслуживание огнетушителей', 'Проверка, перезарядка и техническое обслуживание огнетушителей всех типов', 5000.00, 'fire_extinguisher.jpg'],
                ['Пожарный инструктаж персонала', 'Обучение сотрудников правилам пожарной безопасности и действиям при возгорании', 8000.00, 'training.jpg'],
                ['Разработка плана эвакуации', 'Создание и оформление планов эвакуации при пожаре согласно ГОСТ', 12000.00, 'evacuation_plan.jpg'],
                ['Монтаж систем автоматического пожаротушения', 'Установка автоматических систем пожаротушения (водяных, порошковых, газовых)', 45000.00, 'fire_system.jpg']
            ];
            
            $stmt = $pdo->prepare("INSERT IGNORE INTO services (title, description, price, image) VALUES (?, ?, ?, ?)");
            foreach ($services as $service) {
                $stmt->execute($service);
            }
            $success[] = "✅ Добавлены тестовые услуги";
        }
        
    } catch (PDOException $e) {
        $errors[] = "❌ Ошибка с services: " . $e->getMessage();
    }

    // 4. Назначаем первого пользователя администратором
    $stmt = $pdo->query("SELECT id, email FROM users ORDER BY id LIMIT 1");
    $first_user = $stmt->fetch();
    
    if ($first_user) {
        $stmt = $pdo->prepare("UPDATE users SET is_admin = TRUE WHERE id = ?");
        $stmt->execute([$first_user['id']]);
        $success[] = "✅ Пользователь ID {$first_user['id']} ({$first_user['email']}) назначен администратором";
    } else {
        $errors[] = "❌ Пользователи не найдены. Сначала зарегистрируйтесь.";
    }

} catch (PDOException $e) {
    $errors[] = "❌ Общая ошибка: " . $e->getMessage();
}

// Выводим результаты
echo "<!DOCTYPE html>
<html>
<head>
    <title>Инициализация базы данных</title>
    <link href='assets/css/bootstrap.css' rel='stylesheet'>
</head>
<body class='container mt-5'>
    <h1>Инициализация базы данных</h1>";

if (!empty($success)) {
    echo "<div class='alert alert-success'><h4>Успешно:</h4><ul>";
    foreach ($success as $msg) {
        echo "<li>{$msg}</li>";
    }
    echo "</ul></div>";
}

if (!empty($errors)) {
    echo "<div class='alert alert-danger'><h4>Ошибки:</h4><ul>";
    foreach ($errors as $msg) {
        echo "<li>{$msg}</li>";
    }
    echo "</ul></div>";
}

echo "
    <div class='mt-4'>
        <a href='login.php' class='btn btn-primary'>Перейти к входу</a>
        <a href='admin/index.php' class='btn btn-success'>Перейти в админку</a>
        <a href='index.php' class='btn btn-secondary'>На главную</a>
    </div>
</body>
</html>";
?>