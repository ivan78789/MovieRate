<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Проверка на админа
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1) {
    header('Location: /signin');
    exit;
}

// Получение данных из POST
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$genre = $_POST['genre'] ?? '';
$year = $_POST['year'] ?? '';
$posterPath = null;

// Загрузка постера
if (!empty($_FILES['poster']['tmp_name'])) {
    $uploadDir = __DIR__ . '/../../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $posterName = time() . '_' . basename($_FILES['poster']['name']);
    $posterPath = '/uploads/' . $posterName;
    move_uploaded_file($_FILES['poster']['tmp_name'], $uploadDir . $posterName);
}

// Добавление фильма в базу
$query = "INSERT INTO movies (title, description, genre, year, poster_path, user_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->execute([$title, $description, $genre, $year, $posterPath, $_SESSION['user_id']]);

// Перенаправление
header('Location: /addmovie');
exit;
?>
