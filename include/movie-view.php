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
// // Считаем количество уникальных пользователей, оставивших отзывы для этого фильма
// $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) FROM reviews WHERE movie_id = :movie_id");
// $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
// $stmt->execute();
// $uniqueUsersCount = (int) $stmt->fetchColumn();

// if ($uniqueUsersCount < 6) {
//     $averageRating = null; // Или 0 или сообщение "Мало отзывов"
// } else {
//     // Считаем средний рейтинг по всем отзывам
//     $stmt = $conn->prepare("SELECT AVG(rating) FROM reviews WHERE movie_id = :movie_id");
//     $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
//     $stmt->execute();
//     $averageRating = round($stmt->fetchColumn(), 2);
// }

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
    Средняя оценка: <?= $averageRating !== null ? (int)$averageRating : '0' ?>/10
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
                                <input type="hidden" name="movie_id" value="<?= urlencode($movieId) ?>">
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

    .movie-view {
  max-width: 1100px;
  margin: 40px auto 0 auto;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-wrap: wrap;
  gap: 32px;
  padding: 36px 32px 32px 32px;
  align-items: flex-start;
}

/* Кнопки редактирования и удаления */
.edit-btn,
.delete-btn {
  cursor: pointer;
  padding: 8px 18px;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 6px;
  border: none;
  transition: background 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.edit-btn {
  background: linear-gradient(90deg, #4f8cff 0%, #2356c7 100%);
  color: #fff;
  margin-right: 10px;
}

.edit-btn:hover {
  background: linear-gradient(90deg, #2356c7 0%, #4f8cff 100%);
  box-shadow: 0 4px 16px rgba(35, 86, 199, 0.6);
}

.delete-btn {
  background: linear-gradient(90deg, #ff6b6b 0%, #d32f2f 100%);
  color: #fff;
}

.delete-btn:hover {
  background: linear-gradient(90deg, #d32f2f 0%, #ff6b6b 100%);
  box-shadow: 0 4px 16px rgba(211, 47, 47, 0.6);
}

/* Модальное окно */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
  box-sizing: border-box;
}

.modal-content {
  background: #fff;
  border-radius: 12px;
  padding: 24px 30px;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

/* Заголовки и подписи */
.modal-content label {
  font-weight: 600;
  font-size: 1rem;
  color: #333;
  margin-bottom: 6px;
}

/* Текстовое поле */
.modal-content textarea,
.modal-content input[type="number"] {
  width: 100%;
  padding: 12px 14px;
  font-size: 1rem;
  border: 1.8px solid #cbd5e1;
  border-radius: 8px;
  resize: vertical;
  transition: border-color 0.3s ease;
  box-sizing: border-box;
}

.modal-content textarea:focus,
.modal-content input[type="number"]:focus {
  outline: none;
  border-color: #4f8cff;
  background-color: #f0f5ff;
}

/* Кнопки модального окна */
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 10px;
}

.modal-actions button {
  cursor: pointer;
  padding: 10px 22px;
  font-weight: 600;
  font-size: 1rem;
  border-radius: 8px;
  border: none;
  transition: background 0.3s ease, box-shadow 0.3s ease;
}

.modal-actions button[type="submit"] {
  background: #2356c7;
  color: #fff;
}

.modal-actions button[type="submit"]:hover {
  background: #4f8cff;
  box-shadow: 0 4px 12px rgba(79, 140, 255, 0.6);
}

.modal-actions button[type="button"] {
  background: #e0e0e0;
  color: #555;
}

.modal-actions button[type="button"]:hover {
  background: #c0c0c0;
}

/* Диапазон рейтинга в форме отзыва */
.movie-review-form__range {
  width: 320px; /* убрал дублирование и оставил фиксированное значение */
  accent-color: #4f8cff;
  padding: 6px 8px;
  font-size: 1rem;
  border-radius: 6px;
  text-align: center;
  margin-left: 8px;
  margin-right: 8px;
  box-sizing: border-box;
  background-color: #f1ecec;
}

.movie-view__poster {
  flex: 0 0 320px;
  max-width: 320px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 18px;
}

.movie-view__img {
  width: 100%;
  height: 420px;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(79, 140, 255, 0.1);
  background: #f3f6fa;
}

.movie-view__content {
  flex: 1 1 320px;
  display: flex;
  flex-direction: column;
  gap: 18px;
  min-width: 0;
}

.movie-view__title {
  font-size: 2rem;
  font-weight: 700;
  color: #2356c7;
  margin: 0 0 8px 0;
  word-break: break-word;
}

.movie-view__genre {
  font-size: 1.08rem;
  color: #4f8cff;
  font-weight: 500;
  margin-bottom: 8px;
}

.movie-view__rating {
  font-size: 1.08rem;
  color: #ffb800;
  font-weight: 600;
  margin-bottom: 8px;
}

.movie-view__desc {
  font-size: 1.08rem;
  color: #444;
  background: #f9fafb;
  border-radius: 8px;
  padding: 14px 16px;
  min-height: 60px;
  max-height: 180px;
  overflow-y: auto;
  box-shadow: 0 1px 4px rgba(79, 140, 255, 0.04);
}

.line {
  display: flex;
  height: 1px;
  width: 100%;
  background-color: #ccc; /* исправлено с color на background-color */
}

.movie-view__meta {
  display: flex;
  flex-wrap: wrap;
  gap: 18px;
  font-size: 0.98rem;
  color: #888;
  margin-top: 10px;
}

.movie-view__year,
.movie-view__author,
.movie-view__date {
  background: #f3f6fa;
  border-radius: 6px;
  padding: 4px 10px;
}

.errors {
  color: #d32f2f;
  background: #fff0f0;
  border-radius: 6px;
  padding: 8px 12px;
  margin-bottom: 10px;
  font-size: 0.98rem;
  text-align: center;
}

.movie-review-form__group {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 10px;
}

.movie-review-form__textarea {
  font-size: 1.08rem;
  padding: 14px 12px;
  width: 100%;
  min-height: 60px;
  max-width: 700px;
  border-radius: 6px;
  border: 1.5px solid #d1d5db;
  background: #fff;
  transition: border 0.2s;
  margin-bottom: 2px;
  resize: vertical;
}

.movie-review-form__textarea:focus {
  border: 1.5px solid #4f8cff;
  outline: none;
  background: #f9fafb;
}

.movie-review-form__rating {
  display: flex;
  align-items: center;
  gap: 10px;
}

.movies-comments {
  margin-top: 28px;
  background: #f9fafb;
  border-radius: 12px;
  padding: 22px 22px 10px 22px;
  box-shadow: 0 2px 12px rgba(79, 140, 255, 0.07);
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.movies-comment_conclusion {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 1px 6px rgba(79, 140, 255, 0.06);
  border: 1.5px solid #e5e7eb;
  padding: 14px 18px 10px 18px;
  margin-bottom: 0;
  display: flex;
  flex-direction: column;
  gap: 6px;
  transition: box-shadow 0.18s, border 0.18s;
}

.movies-comment_conclusion:hover {
  box-shadow: 0 4px 18px rgba(79, 140, 255, 0.13);
  border: 1.5px solid #4f8cff;
}

.movies-comment_conclusion-text {
  font-size: 1.08rem;
  color: #222;
  margin-bottom: 4px;
  font-weight: 500;
  word-break: break-word;
}

.review-actions {
  display: flex;
  gap: 10px;
  margin: 8px 0 8px 0;
}

.rewies-edit,
.rewies-delete {
  background: linear-gradient(90deg, #4f8cff 0%, #2356c7 100%);
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 7px 18px;
  font-size: 0.98rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.18s, box-shadow 0.18s;
  box-shadow: 0 1px 4px rgba(79, 140, 255, 0.08);
}

.rewies-edit:hover {
  background: linear-gradient(90deg, #2356c7 0%, #4f8cff 100%);
}

.rewies-delete {
  background: linear-gradient(90deg, #ff6b6b 0%, #d32f2f 100%);
}

.rewies-delete:hover {
  background: linear-gradient(90deg, #d32f2f 0%, #ff6b6b 100%);
}

.movies-comment_conclusion-rating {
 display: flex;       
 flex-direction: column;
 gap: 5px;
  font-size: 0.98rem;
  color: #4f8cff;
  gap: 18px;
  flex-wrap: wrap;
  align-items: start;
}

.rewies-btn {
  background: linear-gradient(90deg, #4f8cff 0%, #2356c7 100%);
  color: #fff;
  border: none;
}
</style>