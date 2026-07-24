<?php
session_start();

// Проверка авторизации (любой вход)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Для обычных пользователей: восстанавливаем имя из БД если нужно
if (!isset($_SESSION['user_name']) && isset($_SESSION['user_id'])) {
    require 'db.php';
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_name'] = $user['full_name'];
    } else {
        session_destroy();
        header("Location: login.php");
        exit;
    }
}

// Функция проверки прав администратора
function isAdmin() {
    // Админ через секретный вход
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        return true;
    }
    
    // Админ из базы данных (если есть поле is_admin)
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    require 'db.php';
    try {
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        return $user && (bool)$user['is_admin'];
    } catch (PDOException $e) {
        // Если поля is_admin нет в таблице
        return false;
    }
}
?>