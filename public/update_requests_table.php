<?php
require 'includes/db.php';

$success_messages = [];
$error_messages = [];

try {
    // Проверяем существование таблицы requests
    $stmt = $pdo->query("SHOW TABLES LIKE 'requests'");
    $table_exists = $stmt->fetch();
    
    if (!$table_exists) {
        // Создаем таблицу с нуля
        $pdo->exec("
            CREATE TABLE requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                customer_name VARCHAR(255) NOT NULL,
                customer_email VARCHAR(255) NOT NULL,
                customer_phone VARCHAR(50) NOT NULL,
                comments TEXT,
                service_id INT NOT NULL,
                status ENUM('pending', 'confirmed', 'completed') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
            )
        ");
        $success_messages[] = "✅ Таблица requests создана с полными полями";
    } else {
        // Проверяем существование полей и добавляем недостающие
        $stmt = $pdo->query("DESCRIBE requests");
        $existing_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $columns_to_add = [
            'customer_name' => "ALTER TABLE requests ADD COLUMN customer_name VARCHAR(255) NOT NULL AFTER user_id",
            'customer_email' => "ALTER TABLE requests ADD COLUMN customer_email VARCHAR(255) NOT NULL AFTER customer_name",
            'customer_phone' => "ALTER TABLE requests ADD COLUMN customer_phone VARCHAR(50) NOT NULL AFTER customer_email",
            'comments' => "ALTER TABLE requests ADD COLUMN comments TEXT AFTER customer_phone"
        ];
        
        foreach ($columns_to_add as $column => $sql) {
            if (!in_array($column, $existing_columns)) {
                try {
                    $pdo->exec($sql);
                    $success_messages[] = "✅ Добавлено поле: {$column}";
                } catch (PDOException $e) {
                    $error_messages[] = "❌ Ошибка добавления поля {$column}: " . $e->getMessage();
                }
            } else {
                $success_messages[] = "ℹ️ Поле {$column} уже существует";
            }
        }
    }
    
} catch (PDOException $e) {
    $error_messages[] = "❌ Общая ошибка: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Обновление таблицы заявок</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h1>Обновление таблицы requests</h1>
    
    <?php if (!empty($success_messages)): ?>
        <div class="alert alert-success">
            <h4>Успешно:</h4>
            <ul class="mb-0">
                <?php foreach ($success_messages as $msg): ?>
                    <li><?= $msg ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_messages)): ?>
        <div class="alert alert-danger">
            <h4>Ошибки:</h4>
            <ul class="mb-0">
                <?php foreach ($error_messages as $msg): ?>
                    <li><?= $msg ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="submit_request.php?service_id=1" class="btn btn-success">Проверить форму заявки</a>
        <a href="services.php" class="btn btn-primary">Вернуться к услугам</a>
        <a href="admin/requests.php" class="btn btn-info">Просмотр заявок в админке</a>
    </div>
</body>
</html>