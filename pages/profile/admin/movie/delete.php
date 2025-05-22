<?php
require_once __DIR__ . '/../../../../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$isAdmin = $_SESSION['is_admin'] ?? false;
if (!$isAdmin) {
    echo "У вас нет прав на удаление этого фильма";
    exit;
}

$movieId = $_GET['id'] ?? null;
if (!$movieId) {
    echo "ID фильма не передан";
    exit;
}

// Проверяем, существует ли фильм
$stmt = $conn->prepare("SELECT id FROM movies WHERE id = ?");
$stmt->execute([$movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    echo "Фильм не найден";
    exit;
}

// Удаляем фильм
$delStmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
$delStmt->execute([$movieId]);

header('Location: /viewmovie');
exit;
