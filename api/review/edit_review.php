<?php
session_start();
require_once '../../config/db.php';
require_once '../../vendor/autoload.php';

use App\Models\Reviews;
use App\Controllers\ReviewsController;

if (!isset($_SESSION['user_id'])) {
    die('Вы не авторизованы');
}

$reviewId = (int) ($_GET['id'] ?? 0);
$reviews = new Reviews($conn);
$review = $reviews->getById($reviewId);

if (!$review || $review['user_id'] != $_SESSION['user_id']) {
    die('Отзыв не найден или вы не владелец.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewId = (int) $_POST['review_id'];
    $comment = trim($_POST['comment']);
    $rating = (int) $_POST['rating'];
    $userId = $_SESSION['user_id'];

    $controller = new ReviewsController($reviews);

    if ($controller->update($reviewId, $userId, $comment, $rating)) {
        header('Location: /pages/movie/movieView.php?id=' . $review['movie_id']);
        exit;
    } else {
        echo "Ошибка при обновлении.";
    }
}

?>

<form method="POST" action="edit_review.php">
    <input type="hidden" name="review_id" value="<?= $reviewId ?>">
    <label>Комментарий:</label><br>
    <textarea name="comment"><?= htmlspecialchars($review['comment']) ?></textarea><br>
    <label>Оценка (1-10):</label><br>
    <input type="number" name="rating" min="1" max="10" value="<?= $review['rating'] ?>"><br>
    <button type="submit">Сохранить</button>
</form>