<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /signin');
    exit;
}

$id = $_POST['id'] ?? null;
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$genre = trim($_POST['genre'] ?? '');
$year = $_POST['year'] ?? null;

if (!$id || !$title || !$description || !$genre || !$year) {
    die("Пожалуйста, заполните все поля.");
}

try {
    if (!empty($_FILES['poster']['name'])) {
        $uploadDir = '/uploads/posters/';
        $filename = time() . '_' . basename($_FILES['poster']['name']); // уникальное имя
        $uploadPath = $uploadDir . $filename;

        // Создаем папку, если не существует
        $fullUploadDir = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
        if (!is_dir($fullUploadDir)) {
            mkdir($fullUploadDir, 0777, true);
        }

        // Перемещаем файл
        if (!move_uploaded_file($_FILES['poster']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $uploadPath)) {
            die("Ошибка загрузки файла.");
        }

        // Обновляем с постером
        $stmt = $conn->prepare("UPDATE movies SET title = ?, description = ?, genre = ?, year = ?, poster_path = ? WHERE id = ?");
        $stmt->execute([$title, $description, $genre, $year, $uploadPath, $id]);
    } else {
        // Обновляем без постера
        $stmt = $conn->prepare("UPDATE movies SET title = ?, description = ?, genre = ?, year = ? WHERE id = ?");
        $stmt->execute([$title, $description, $genre, $year, $id]);
    }

    header('Location: /viewmovie?id=' . $id);
    exit;
} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}