<?php
$host = 'MySQL-8.0'; 
$dbname = 'pozharka';
$username = 'root';
$password = ''; // пустой пароль

try {
    // Без указания порта!
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Ошибка подключения к БД: " . $e->getMessage());
}
?>