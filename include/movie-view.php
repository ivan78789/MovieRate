<?php
session_start();
$titleName = "Movie";
require_once __DIR__ . "/../config/db.php";

use App\Models\Movie;
use App\Models\Reviews;
use App\Controllers\MovieController;
use App\Controllers\ReviewsController;

$MovieModel = new Movie($conn);
$MovieController = new MovieController($MovieModel);

$ReviewsModel = new Reviews($conn);
$ReviewsController = new ReviewsController($ReviewsModel);

// CSRF токен
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Проверка выбран ли фильм и существует ли он
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Фильм не выбран";
    exit;
}

$movieId = (int) $_GET['id'];
$movie = $MovieController->getById($movieId);

if ($movie === null) {
    echo "Фильм не найден";
    exit;
}

// Получаем средний рейтинг
$averageRating = $ReviewsController->getAverageRating($movieId);

// Обработка отправки отзыва
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Проверка CSRF токена
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ошибка безопасности. Попробуйте снова.');
    }

    // Проверка авторизации
    if (!isset($_SESSION['user_id'])) {
        header("Location: /signin");
        exit;
    }

    // Проверка, не оставлял ли пользователь уже отзыв
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND movie_id = :movie_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
    $stmt->execute();
    $reviewCount = $stmt->fetchColumn();

    if ($reviewCount > 0) {
        $errors['general'] = 'Вы уже оставили отзыв для этого фильма.';
    }

    // Получаем данные из формы
    $comment = trim($_POST['comment'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);

    if ($rating < 1 || $rating > 10) {
        $errors['general'] = 'Пожалуйста, выберите оценку от 1 до 10!';
    }
    if (empty($comment)) {
        $errors['general'] = 'Пожалуйста, заполните комментарий!';
    }

    // Если ошибок нет, вставляем отзыв
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

            header("Location: /movieView?id=$movieId");
            exit;
        } catch (PDOException $e) {
            $errors['general'] = 'Ошибка при добавлении отзыва. Попробуйте позже.';
        }
    }
}
// Считаем количество уникальных пользователей, оставивших отзывы для этого фильма
$stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) FROM reviews WHERE movie_id = :movie_id");
$stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
$stmt->execute();
$uniqueUsersCount = (int) $stmt->fetchColumn();

$averageRating = null;

if ($uniqueUsersCount >= 2) {
    // Считаем средний рейтинг по всем отзывам
    $stmt = $conn->prepare("SELECT AVG(rating) FROM reviews WHERE movie_id = :movie_id");
    $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
    $stmt->execute();
    $averageRating = round($stmt->fetchColumn(), 2);
}

// Получаем все отзывы
$reviews = $ReviewsController->getByMovieId($movieId);

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/nav.php';
?>

<section>
    <div class="movie-view">
        <div class="movie-view__poster">
            <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="movie-view__img">
        </div>
        <div class="movie-view__content">
            <h2 class="movie-view__title"><?= htmlspecialchars($movie['title']) ?></h2>
            <div class="movie-view__genre">Жанр: <span><?= htmlspecialchars($movie['genre']) ?></span></div>
<div class="movie-view__rating">
    Средняя оценка: <?= $averageRating !== null ? $averageRating . '/10' : '' ?>
</div>


            <div class="movie-view__desc"><?= nl2br(htmlspecialchars($movie['description'])) ?></div>
            <div class="movie-view__meta">
                <span class="movie-view__year">Год: <?= htmlspecialchars($movie['year']) ?></span>
                <span class="movie-view__author">Создал: <?= htmlspecialchars($movie['created_by'] ?? $movie['created_by'] ?? $review['username'] ?? 'Админ') ?></span>
                <span class="movie-view__date">Дата: <?= htmlspecialchars($movie['created_at']) ?></span>
            </div>

            <form method="post" action="/movieView?id=<?= urlencode($movieId) ?>" class="movie-review-form" novalidate>
                <?php if (!empty($errors['general'])): ?>
                    <div class="errors"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="movie-review-form__group">
                    <label for="comment">Ваш отзыв</label>
                    <textarea name="comment" id="comment" placeholder="Кратко опишите впечатление" maxlength="200" required
                        class="movie-review-form__textarea"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
                </div>

                <div class="movie-review-form__group">
                    <label for="rating">Оцените фильм</label>
                    <div class="movie-review-form__rating">
                        <input type="number" id="rating" name="rating" min="1" max="10"
                            oninput="document.getElementById('rating-value').textContent = this.value"
                            class="movie-review-form__range"
                            value="<?= isset($_POST['rating']) ? (int)$_POST['rating'] : 1 ?>">
                        <span id="rating-value"><?= isset($_POST['rating']) ? (int)$_POST['rating'] : 1 ?></span>
                    </div>
                </div>

                <button class="reviews-btn" type="submit" name="submit">Отправить отзыв</button>
            </form>

            <div class="movies-comments">
                <?php foreach ($reviews as $review): ?>
                    <div class="movies-comment_conclusion">
                        <div class="movies-comment_conclusion-text">
                            <?= nl2br(htmlspecialchars($review['comment'])) ?>
                        </div>
                        <div class="line"></div>
                        <div class="movies-comment_conclusion-rating">
                            <div>
                                Оценка: <?= htmlspecialchars($review['rating']) ?>
                            </div>
                            <div>
                                Автор: <?= htmlspecialchars($review['username']) ?>
                            </div>
                            <div>
                                Дата: <?= htmlspecialchars($review['created_at']) ?>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                            <button class="edit-btn" data-review-id="<?= (int)$review['id'] ?>"
                                data-comment="<?= htmlspecialchars($review['comment'], ENT_QUOTES) ?>"
                                data-rating="<?= (int)$review['rating'] ?>"
                                type="button">
                                Редактировать
                            </button>

                            <form method="post" action="/reviewAction" onsubmit="return confirm('Удалить отзыв?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="review_id" value="<?= (int)$review['id'] ?>">
                                <input type="hidden" name="movie_id" value="<?= (int)$movieId ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                <button class="delete-btn" type="submit">Удалить</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Модальное окно для редактирования -->
<div id="editModal" class="modal" style="display:none;">
    <div class="modal-content">
        <form method="post" action="/reviewAction" novalidate>
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="review_id" id="modalReviewId" value="">
            <input type="hidden" name="movie_id" value="<?= urlencode($movieId) ?>">

            <label for="modalComment">Комментарий</label>
            <textarea name="comment" id="modalComment" required></textarea>

            <label for="modalRating">Оценка (1–10)</label>
            <input type="number" name="rating" id="modalRating" min="1" max="10" required>

            <div class="modal-actions">
                <button type="submit">Сохранить</button>
                <button type="button" onclick="closeModal()">Отмена</button>
            </div>
        </form>
    </div>
</div>

<script>
    const  Rating = document.getElementById('rating');
  const  Comment = document.getElementById('comment');
const editButtons = document.querySelectorAll('.edit-btn');
const modal = document.getElementById('editModal');
const modalReviewId = document.getElementById('modalReviewId');
const modalComment = document.getElementById('modalComment');
const modalRating = document.getElementById('modalRating');

editButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        modalReviewId.value = btn.dataset.reviewId;
        modalComment.value = btn.dataset.comment;
        modalRating.value = btn.dataset.rating;
        modal.style.display = 'flex';
    });
});

function closeModal() {
    modal.style.display = 'none';
    modalReviewId.value = '';
    modalComment.value = '';
    modalRating.value = '';
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
});

modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
});

// При загрузке страницы закрываем модалку и очищаем форму
window.addEventListener('load', () => {
    closeModal();
});

</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
<style>

</style>