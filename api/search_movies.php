<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$rating = isset($_GET['rating']) ? (float)$_GET['rating'] : 0;
$featured = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;

$sql = "SELECT * FROM movies WHERE 1=1";
$params = [];

if ($query) {
    $sql .= " AND title LIKE ?";
    $params[] = "%$query%";
}

if ($genre && $genre !== 'Все') {
    // Предполагается, что жанры хранятся в БД в виде CSV (например: "Драма,Комедия,Триллер")
    // Используем FIND_IN_SET для точного поиска жанра
    $sql .= " AND FIND_IN_SET(?, genre) > 0";
    $params[] = $genre;
}

if ($year) {
    $sql .= " AND year = ?";
    $params[] = $year;
}

if ($rating) {
    $sql .= " AND rating >= ?";
    $params[] = $rating;
}

if ($featured) {
    // Для избранных фильмов рейтинг должен быть >= 8
    $sql .= " AND rating >= 8";
}

$sql .= " ORDER BY created_at DESC";

try {
    if (!isset($conn) || !$conn) {
        throw new Exception('Подключение к базе данных не инициализировано');
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($movies);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сервера: ' . $e->getMessage()]);
}
