<?php
require 'includes/db.php';

echo "<h2>Проверка сертификатов в БД</h2>";

try {
    $stmt = $pdo->query("SELECT * FROM certificates");
    $certificates = $stmt->fetchAll();
    
    if (empty($certificates)) {
        echo "<p style='color: red;'>❌ Сертификатов нет в базе данных</p>";
        echo "<a href='init_database.php' class='btn btn-primary'>Добавить тестовые данные</a>";
    } else {
        echo "<p style='color: green;'>✅ Найдено сертификатов: " . count($certificates) . "</p>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Название</th><th>Описание</th><th>Изображение</th><th>Дата</th></tr>";
        foreach ($certificates as $cert) {
            echo "<tr>";
            echo "<td>{$cert['id']}</td>";
            echo "<td>{$cert['title']}</td>";
            echo "<td>{$cert['description']}</td>";
            echo "<td>{$cert['image']}</td>";
            echo "<td>{$cert['issued_date']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Ошибка: " . $e->getMessage() . "</p>";
    echo "<a href='init_database.php' class='btn btn-primary'>Создать таблицу certificates</a>";
}

echo "<br><a href='about.php' class='btn btn-secondary'>Вернуться к разделу 'О нас'</a>";
?>