<?php
session_start();
require_once __DIR__ . '/../../../../config/db.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$userId = $_SESSION['user_id'];
$isAdmin = $_SESSION['is_admin'] ?? false;

$movieId = $_GET['id'] ?? null;
if (!$movieId) {
    echo "ID фильма не передан";
    exit;
}

// Получаем фильм
$stmt = $conn->prepare("SELECT id, user_id FROM movies WHERE id = ?");
$stmt->execute([$movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    echo "Фильм не найден";
    exit;
}

// Проверка прав: либо админ, либо автор фильма
if (!$isAdmin && $movie['user_id'] != $userId) {
    echo "У вас нет прав на удаление этого фильма";
    exit;
}

// Удаление фильма
$delStmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
$delStmt->execute([$movieId]);

header('Location: /viewmovie');
exit;
