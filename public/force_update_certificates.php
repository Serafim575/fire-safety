<?php
require 'includes/db.php';

try {
    // Принудительно удаляем ВСЕ сертификаты
    $pdo->exec("DELETE FROM certificates");
    
    // Сбрасываем автоинкремент
    $pdo->exec("ALTER TABLE certificates AUTO_INCREMENT = 1");
    
    // Добавляем ТОЛЬКО 5 сертификатов
    $certificates = [
        ['Сертификат соответствия ГОСТ Р', 'Соответствие всем требованиям государственных стандартов пожарной безопасности. Документ подтверждает нашу компетентность в проведении пожарных аудитов.', 'certificate1.jpg', '2024-01-15'],
        ['Лицензия МЧС России', 'Официальная лицензия на осуществление деятельности в области пожарной безопасности. Разрешает проведение монтажных и пусконаладочных работ.', 'certificate2.jpg', '2023-11-20'],
        ['Сертификат ISO 9001:2015', 'Международный стандарт системы менеджмента качества. Подтверждает высокий уровень организации рабочих процессов компании.', 'certificate3.jpg', '2024-03-10'],
        ['Сертификат профессиональной подготовки', 'Подтверждение квалификации наших специалистов. Все сотрудники прошли обучение и имеют соответствующие допуски.', 'certificate4.jpg', '2024-02-28'],
        ['Сертификат партнера производителя', 'Официальный партнер ведущих производителей пожарного оборудования. Прямые поставки с гарантией качества.', 'certificate5.jpg', '2024-04-05']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO certificates (title, description, image, issued_date) VALUES (?, ?, ?, ?)");
    
    $inserted_count = 0;
    foreach ($certificates as $cert) {
        $stmt->execute($cert);
        $inserted_count++;
    }
    
    // Проверяем сколько сейчас сертификатов в базе
    $check_stmt = $pdo->query("SELECT COUNT(*) as total FROM certificates");
    $current_count = $check_stmt->fetch()['total'];
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Принудительное обновление сертификатов</title>
        <link href='assets/css/bootstrap.css' rel='stylesheet'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-success'>
            <h4>✅ Принудительное обновление завершено!</h4>
            <p><strong>Удалены все старые сертификаты</strong></p>
            <p><strong>Добавлено новых сертификатов: {$inserted_count}</strong></p>
            <p><strong>Всего в базе сейчас: {$current_count}</strong></p>
            
            <h5 class='mt-3'>Добавленные сертификаты:</h5>
            <ol>";
    
    foreach ($certificates as $cert) {
        echo "<li><strong>{$cert[0]}</strong> - {$cert[3]}</li>";
    }
    
    echo "    </ol>
        </div>
        <div class='mt-3'>
            <a href='about.php' class='btn btn-primary'>Перейти в раздел 'О нас'</a>
            <a href='check_certificates.php' class='btn btn-info'>Проверить сертификаты</a>
            <a href='admin/certificates.php' class='btn btn-warning'>Управление в админке</a>
        </div>
    </body>
    </html>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Ошибка</title>
        <link href='assets/css/bootstrap.css' rel='stylesheet'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-danger'>
            <h4>❌ Ошибка при обновлении сертификатов:</h4>
            <p>" . $e->getMessage() . "</p>
        </div>
    </body>
    </html>";
}
?>