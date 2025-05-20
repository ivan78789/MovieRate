<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['review_id'])) {
    header("Location: /");
    exit;
}

$reviewId = (int) $_POST['review_id'];

// Убедимся, что отзыв принадлежит текущему пользователю
$stmt = $conn->prepare("SELECT * FROM reviews WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $reviewId,
    ':user_id' => $_SESSION['user_id']
]);
$review = $stmt->fetch();

if ($review) {
    $delete = $conn->prepare("DELETE FROM reviews WHERE id = :id");
    $delete->bindParam(':id', $reviewId, PDO::PARAM_INT);
    $delete->execute();
}

header("Location: /movieView?id=" . $review['movie_id']);
exit;
