<?php
//начало сессии
session_start();
$titleName = "Movie";
require_once __DIR__ . "/../config/db.php";
// Подключаем файл конфигурации базы данных
$reviews = [];

//подключени еконтротроллеров и моделей
use App\Models\Movie;
use App\Models\Reviews;
use App\Controllers\MovieController;
use App\Controllers\ReviewsController;

// Создаётся экземпляр модели и контроллера фильмов.
$MovieModel = new Movie($conn);
$MovieController = new MovieController($MovieModel);

$ReviewsModel = new Reviews($conn);
$ReviewsController = new ReviewsController($ReviewsModel);



// получаем айди спомощью зепроса гет и проверяем найден ли фильм
//  получаем айди из URL и выводим из базы если он существует если нет ошибка 
if (isset($_GET['id'])) {
    $movieId = (int) $_GET['id'];
    $movie = $MovieController->getById($movieId);
    // ДЛЯ ПОЛУЧЕНИЯ РЕЙТИНГА ОБЩЕГО
    $averageRating = $ReviewsController->getAverageRating($movieId);

    if ($movie === null) {
        echo "Фильм не найден";
        exit;
    }
} else {
    echo "Фильм не выбран";
    exit;
}
// Получаем все отзывы для данного фильма.
$reviews = $ReviewsController->getByMovieId($movieId);
// объявляем метод пост чтобы отправить отзыввы в базу ревьюс после которого выводим все отзывы к фильму
if (isset($_POST['submit'])) {
    // Удаляет пробелы в начале и конце строк trim  и если коммента нет как указан оператор  ?? '' то вернем путсую строку
    $comment = trim($_POST['comment'] ?? '');
    // Получаем рейтинг отзыва в числовом виде. Если рейтинг не передан то данное: свойство " ?? 0 " -по умолчанию будет 0
    $rating = (int) ($_POST['rating'] ?? 0);
    $errors = [];

    if ($rating < 1 || $rating > 10) {
        $errors['general'] = 'Пожалуйста, выберите оценку!';
    }

    // еслт пользователь не аторизован он не сможет оставить отзыв и перенаправляется на страницу входа
    if (!isset($_SESSION['user_id'])) {
        header("Location: /signin");
        exit;
    }
    // проверка на заполнение всех полей
    if (empty($rating) || empty($comment)) {
        $errors['general'] = 'Пожалуйста, заполните все поля!';
    }
    // если ошибкок нет то добвляем отзыв в базу 
    if (empty($errors)) {
        // пробуем выолнить блок кода и если ошибка то выводим ошибку catch
        try {
            // подготовляем запрос для втсавки отзыва в базу
            $stmt = $conn->prepare("
                INSERT INTO reviews (movie_id, user_id, rating, comment, created_at)
                VALUES (:movie_id, :user_id, :rating, :comment, NOW())
            ");
            //  привязывает переменную к параметру в SQL-запроса
            $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();

            // Перезагружаем страницу после успешной отправки
            header("Location: /movieView?id=$movieId");
            exit;
            // Ловит ошибки PDO
        } catch (PDOException $e) {
            $errors['general'] = 'Ошибка при добавлении отзыва. Попробуйте позже.';
        }
    }
}
if (isset($_POST['delete_comment'])) {
    $reviewIdToDelete = (int) $_POST['delete_comment'];
    $review = $ReviewsController->getById($reviewIdToDelete);

    if ($review && $reviews['user_id'] == $_SESSION['user_id']) {
        $ReviewsController->delete($reviewIdToDelete);
        exit;
    } else {
        $errors['general'] = 'Вы нен можете уджалиьт этот  отзыв';
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
            <div class="movie-view__rating">Средняя оценка: <?= $averageRating ?>/10</div>

            <div class="movie-view__desc"><?= htmlspecialchars($movie['description']) ?></div>
            <div class="movie-view__meta">
                <span class="movie-view__year">Год: <?= htmlspecialchars($movie['year']) ?></span>
                <span class="movie-view__author">Создал: <?= htmlspecialchars($movie['created_by']) ?></span>
                <span class="movie-view__date">Дата: <?= htmlspecialchars($movie['created_at']) ?></span>
            </div>
            <form method="post" action="/movieView?id=<?= $movieId ?>">
                <!-- сюда выводятся ошибки -->
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
                        <input type="range" id="rating" name="rating" min="0" max="10" value=""
                            oninput="document.getElementById('rating-value').textContent = this.value"
                            class="movie-review-form__range">
                        <span id="rating-value">0</span>
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