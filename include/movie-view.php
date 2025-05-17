<?php
session_start();
$titleName = "Movie";
require_once __DIR__ . "/../config/db.php";
$reviews = [];

use App\Models\Movie;
use App\Models\Reviews;
use App\Controllers\MovieController;
use App\Controllers\ReviewsController;

$MovieModel = new Movie($conn);
$MovieController = new MovieController($MovieModel);

$ReviewsModel = new Reviews($conn);
$ReviewsController = new ReviewsController($ReviewsModel);


if (isset($_GET['id'])) {
    $movieId = (int) $_GET['id'];
    $movie = $MovieController->getById($movieId);

    if ($movie === null) {
        echo "Фильм не найден";
        exit;
    }
} else {
    echo "Фильм не выбран";
    exit;
}

$reviews = $ReviewsController->getByMovieId($movieId);
if (isset($_POST['submit'])) {
    $comment = trim($_POST['comment'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);
    $errors = [];

    if (!isset($_SESSION['user_id'])) {
        header("Location: /signin");
        exit;
    }

    if (empty($rating) || empty($comment)) {
        $errors['general'] = 'Пожалуйста, заполните все поля!';
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO reviews (movie_id, user_id, rating, comment, created_at)
                VALUES (:movie_id, :user_id, :rating, :comment, NOW())
            ");
            $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();

            // Перезагружаем страницу после успешной отправки
            header("Location: /movieView?id=$movieId");
            exit;
        } catch (PDOException $e) {
            $errors['general'] = 'Ошибка при добавлении отзыва. Попробуйте позже.';
        }
    }
}




require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/nav.php';
?>

<section>
    <div class="movie-view">
        <div class="movie-view__poster">
            <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>"
                class="movie-view__img">
        </div>
        <div class="movie-view__content">
            <h2 class="movie-view__title"><?= htmlspecialchars($movie['title']) ?></h2>
            <div class="movie-view__genre">Жанр: <span><?= htmlspecialchars($movie['genre']) ?></span></div>
            <div class="movie-view__desc"><?= htmlspecialchars($movie['description']) ?></div>
            <div class="movie-view__meta">
                <span class="movie-view__year">Год: <?= htmlspecialchars($movie['year']) ?></span>
                <span class="movie-view__author">Создал: <?= htmlspecialchars($movie['created_by']) ?></span>
                <span class="movie-view__date">Дата: <?= htmlspecialchars($movie['created_at']) ?></span>
            </div>
            <form method="post" action="/movieView?id=<?= $movieId ?>">

                <?php if (!empty($errors['general'])): ?>
                    <div class="errors"> <?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>
                <div class="movie-review-form__group">
                    <label for="comment">Ваш отзыв</label>
                    <textarea name="comment" id="comment" placeholder="Кратко опишите впечатление" maxlength="200"
                        required class="movie-review-form__textarea"></textarea>
                </div>
                <div class="movie-review-form__group">
                    <label for="rating">Оцените фильм</label>
                    <div class="movie-review-form__rating">
                        <input type="range" id="rating" name="rating" min="1" max="10" value="1"
                            oninput="document.getElementById('rating-value').textContent = this.value"
                            class="movie-review-form__range">
                        <span id="rating-value">1</span>
                    </div>
                </div>
                <button type="submit" name="submit">Отправить отзыв</button>
            </form>
            <div class="movies-comments">
                <?php foreach ($reviews as $review): ?>
                    <div class="movies-comment_conclusion">
                        <div class="movies-comment_conclusion-text">
                            <?= htmlspecialchars($review['comment']) ?>
                        </div>
                        <div class="movies-comment_conclusion-rating">
                            Оценка: <?= htmlspecialchars($review['rating']) ?><br>
                            Автор: <?= htmlspecialchars($review['username']) ?>
                            Дата: <?= htmlspecialchars($review['created_at']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>