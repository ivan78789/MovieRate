<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$userId = $_SESSION['user_id'];
$isAdmin = $_SESSION['is_admin'] ?? 0;

// Получаем и проверяем данные
$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$genre = trim($_POST['genre'] ?? '');
$year = $_POST['year'] ?? null;

if (!$id || !$title || !$description || !$genre || !$year) {
    die("Пожалуйста, заполните все поля.");
}

// Проверка фильма и прав
$stmt = $conn->prepare("SELECT user_id FROM movies WHERE id = ?");
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    die("Фильм не найден.");
}

if ($movie['user_id'] != $userId && !$isAdmin) {
    die("У вас нет прав на редактирование этого фильма.");
}

// Обработка файла (если есть)
try {
    if (!empty($_FILES['poster']['name'])) {
        $uploadDir = '/uploads/posters/';
        $filename = time() . '_' . basename($_FILES['poster']['name']);
        $uploadPath = $uploadDir . $filename;
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $uploadPath;

        // Создаём директорию при необходимости
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        if (!move_uploaded_file($_FILES['poster']['tmp_name'], $fullPath)) {
            die("Ошибка загрузки файла.");
        }

        // Обновление с постером
        $stmt = $conn->prepare("UPDATE movies SET title = ?, description = ?, genre = ?, year = ?, poster_path = ? WHERE id = ?");
        $stmt->execute([$title, $description, $genre, $year, $uploadPath, $id]);
    } else {
        // Без постера
        $stmt = $conn->prepare("UPDATE movies SET title = ?, description = ?, genre = ?, year = ? WHERE id = ?");
        $stmt->execute([$title, $description, $genre, $year, $id]);
    }

    header('Location: /viewmovie');
    exit;
} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
