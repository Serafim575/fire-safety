<?php
// Файл для инициализации базы данных
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Подключаемся к MySQL без выбора базы данных
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Создаем базу данных если не существует
    $pdo->exec("CREATE DATABASE IF NOT EXISTS pozharka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE pozharka");

    // Таблица пользователей
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Таблица услуг
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            image VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Таблица заявок
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            service_id INT NOT NULL,
            status ENUM('pending', 'confirmed', 'completed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        )
    ");

    // Таблица сертификатов
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

    // Вставляем тестовые данные услуг (расширенный список)
    $services = [
        // Основные услуги
        ['Пожарный аудит объекта', 'Комплексная проверка здания на соответствие нормам пожарной безопасности с выдачей официального заключения', 15000.00, 'audit.jpg'],
        ['Установка пожарной сигнализации', 'Монтаж современной системы пожарной сигнализации с датчиками дыма и тепла', 25000.00, 'alarm.jpg'],
        ['Обслуживание огнетушителей', 'Проверка, перезарядка и техническое обслуживание огнетушителей всех типов', 5000.00, 'fire_extinguisher.jpg'],
        ['Пожарный инструктаж персонала', 'Обучение сотрудников правилам пожарной безопасности и действиям при возгорании', 8000.00, 'training.jpg'],
        ['Разработка плана эвакуации', 'Создание и оформление планов эвакуации при пожаре согласно ГОСТ', 12000.00, 'evacuation_plan.jpg'],
        ['Монтаж систем автоматического пожаротушения', 'Установка автоматических систем пожаротушения (водяных, порошковых, газовых)', 45000.00, 'fire_system.jpg'],
        
        // Дополнительные услуги
        ['Установка системы оповещения', 'Монтаж системы голосового оповещения и управления эвакуацией', 18000.00, 'notification_system.jpg'],
        ['Проектирование пожарной безопасности', 'Разработка проектной документации по пожарной безопасности для новых объектов', 30000.00, 'project_design.jpg'],
        ['Огнезащитная обработка конструкций', 'Обработка металлических, деревянных и других конструкций огнезащитными составами', 22000.00, 'fire_protection.jpg'],
        ['Монтаж противопожарных дверей', 'Установка противопожарных дверей и ворот с соответствующими сертификатами', 35000.00, 'fire_doors.jpg'],
        ['Техническое обслуживание систем', 'Регулярное техническое обслуживание и проверка работоспособности пожарных систем', 9000.00, 'maintenance.jpg'],
        ['Аварийно-спасательные работы', 'Проведение аварийно-спасательных работ при пожарах и задымлениях', 28000.00, 'rescue_works.jpg'],
        ['Замер сопротивления изоляции', 'Электротехнические измерения сопротивления изоляции проводки', 7000.00, 'electrical_test.jpg'],
        ['Установка системы дымоудаления', 'Монтаж системы принудительного дымоудаления из помещений', 38000.00, 'smoke_removal.jpg'],
        ['Проверка вентиляционных систем', 'Диагностика и проверка противодымной вентиляции', 11000.00, 'ventilation_check.jpg'],
        ['Сопровождение при проверках МЧС', 'Профессиональное сопровождение во время проверок органов государственного пожарного надзора', 15000.00, 'mchs_support.jpg']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO services (title, description, price, image) VALUES (?, ?, ?, ?)");
    foreach ($services as $service) {
        $stmt->execute($service);
    }

    // Вставляем тестовые данные сертификатов
    $certificates = [
        ['Сертификат соответствия ГОСТ', 'Соответствие всем требованиям государственных стандартов пожарной безопасности', 'certificate1.jpg', '2024-01-15'],
        ['Лицензия МЧС России', 'Официальная лицензия на осуществление деятельности в области пожарной безопасности', 'certificate2.jpg', '2023-11-20'],
        ['Сертификат ISO 9001', 'Международный стандарт системы менеджмента качества', 'certificate3.jpg', '2024-03-10'],
        ['Сертификат профессиональной подготовки', 'Подтверждение квалификации наших специалистов', 'certificate4.jpg', '2024-02-28']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO certificates (title, description, image, issued_date) VALUES (?, ?, ?, ?)");
    foreach ($certificates as $certificate) {
        $stmt->execute($certificate);
    }

    echo "<h2 style='color: green;'>✅ База данных успешно инициализирована!</h2>";
    echo "<p>Созданы таблицы: users, services, requests, certificates</p>";
    echo "<p>Добавлено " . count($services) . " услуг и " . count($certificates) . " сертификатов</p>";
    echo "<p><a href='../services.php' style='color: #c00;'>Перейти к услугам</a></p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Ошибка инициализации базы данных: " . $e->getMessage() . "</h2>";
}
?>