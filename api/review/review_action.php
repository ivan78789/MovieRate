<?php
session_start();
require_once __DIR__ . "/../../config/db.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: /signin");
    exit;
}

$action = $_POST['action'] ?? '';
$reviewId = (int) ($_POST['review_id'] ?? 0);
$movieId = (int) ($_POST['movie_id'] ?? 0);
$isAdmin = $_SESSION['is_admin'] ?? false;

if ($reviewId <= 0 || $movieId <= 0) {
    die("Неверные данные");
}

try {
    // Получаем автора отзыва
    $stmt = $conn->prepare("SELECT user_id FROM reviews WHERE id = :id");
    $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
    $stmt->execute();
    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$review) {
        die("Отзыв не найден");
    }

    // Проверка прав: либо автор, либо админ
    if ($review['user_id'] !== $_SESSION['user_id'] && !$isAdmin) {
        die("Недостаточно прав");
    }

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
        $stmt->execute();

        // После удаления редирект на страницу фильма
        header("Location: /movieView?id=" . $movieId);
        exit;

    } elseif ($action === 'edit') {
        $comment = trim($_POST['comment'] ?? '');
        $rating = (int) ($_POST['rating'] ?? 0);

        if ($rating < 1 || $rating > 10 || empty($comment)) {
            die("Неверный ввод");
        }

        $stmt = $conn->prepare("
            UPDATE reviews 
            SET comment = :comment, rating = :rating 
            WHERE id = :id
        ");
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':id', $reviewId, PDO::PARAM_INT);
        $stmt->execute();

        // После редактирования редирект на страницу фильма
        header("Location: /movieView?id=" . $movieId);
        exit;
    }

    // Если действие не delete и не edit — просто редирект на страницу фильма
    header("Location: /movieView?id=" . $movieId);
    exit;

} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
