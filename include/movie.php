<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../config/db.php";

use App\Models\Movie;
use App\Models\Reviews;
use App\Controllers\MovieController;
use App\Controllers\ReviewsController;

$MovieModel = new Movie($conn);
$MovieController = new MovieController($MovieModel);

$ReviewsModel = new Reviews($conn);
$ReviewsController = new ReviewsController($ReviewsModel);

// Получаем фильтр жанра из GET
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : null;

// Получаем фильмы
$movies = $MovieController->getAllMovies();

// Фильтрация по жанру (по подстроке, регистронезависимо, только если выбран жанр)
if ($genreFilter) {
    $movies = array_filter($movies, function ($movie) use ($genreFilter) {
        // Разбиваем жанры по разделителям и ищем точное совпадение
        $genresArr = preg_split('/[\/,;|]+/', $movie['genre']);
        foreach ($genresArr as $g) {
            if (mb_strtolower(trim($g)) === mb_strtolower(trim($genreFilter))) {
                return true;
            }
        }
        return false;
    });
}


// Массив жанров
$genres = ['Драма', 'Фантастика', 'Приключения', 'Комедия', 'Ужасы'];

$titleName = "Movies";
require_once "./layout/header.php";
require_once "./layout/nav.php";

// Ищем фильм "Джон Уик 3" только если нет фильтра жанра
$featuredMovie = null;
if (!$genreFilter) {
    foreach ($movies as $movie) {
        if (mb_strtolower(trim($movie['title'])) === mb_strtolower('Джон Уик 3')) {
            $featuredMovie = $movie;
            break;
        }
    }
}
?>

<?php if ($featuredMovie): ?>
<div class="banner">
    <div class="banner-content">
        <h2 class="banner-title"><?= htmlspecialchars($featuredMovie['title']) ?></h2>
        <p class="banner-rating">Рейтинг: <?= htmlspecialchars($featuredMovie['rating'] ?? 'N/A') ?></p>
    </div>
    <div class="banner-content-desc">
        <h3 class="banner-desc"><?= htmlspecialchars($featuredMovie['description'] ?? 'Описание недоступно') ?></h3>
        <a href="/movieView?id=<?= urlencode($featuredMovie['id']) ?>" class="banner-details">Подробнее</a>
    </div>
</div>
<?php elseif (!$genreFilter): ?>
    <p>Фильм "Джон Уик 3" не найден.</p>
<?php endif; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<?php
// Если фильтр жанра есть — показываем только его в секции
if ($genreFilter) {
    $genres = [$genreFilter];
}
?>

<?php foreach ($genres as $index => $genre): ?>
    <?php
    // Фильтрация фильмов по жанру для секций (точное совпадение по одному из жанров)
    $filteredMovies = array_filter($movies, function ($movie) use ($genre) {
        $genresArr = preg_split('/[\/,;|]+/', $movie['genre']);
        foreach ($genresArr as $g) {
            if (mb_strtolower(trim($g)) === mb_strtolower(trim($genre))) {
                return true;
            }
        }
        return false;
    });
    if (empty($filteredMovies)) continue;
    ?>

    <div class="movie-section container">
        <div class="genre-title">
            <h2>
                <?php if (!$genreFilter): ?>
                    <a href="/movie?genre=<?= urlencode($genre) ?>"><?= htmlspecialchars($genre) ?></a>
                <?php else: ?>
                    <?= htmlspecialchars($genre) ?>
                <?php endif; ?>
            </h2>
        </div>

        <div class="swiper movie-swiper swiper-<?= $index ?>">
            <div class="swiper-wrapper">
                <?php foreach ($filteredMovies as $movie): ?>
                    <div class="swiper-slide">
                        <div class="card">
                            <img src="<?= htmlspecialchars($movie['poster_path'] ?: 'assets/img/placeholder.jpg') ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                            <div class="card-body">
                                <h2><?= htmlspecialchars($movie['title']) ?></h2>
                                <p><?= htmlspecialchars($movie['year']) ?></p>
                                <p><?= htmlspecialchars($movie['genre']) ?></p>
                                <a href="/movieView?id=<?= htmlspecialchars($movie['id']) ?>">Подробнее</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-button-next swiper-button-next-<?= $index ?>"></div>
            <div class="swiper-button-prev swiper-button-prev-<?= $index ?>"></div>
        </div>
    </div>
<?php endforeach; ?>


<!-- Исправление стрелок свайпера для страницы жанра -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.movie-swiper').forEach((swiperEl, index) => {
    const swiper = new Swiper(swiperEl, {
      slidesPerView: 2.3,
      spaceBetween: 20,
      loop: false,
      navigation: {
        nextEl: `.swiper-button-next-${index}`,
        prevEl: `.swiper-button-prev-${index}`,
      },
      breakpoints: {
        480: { slidesPerView: 2.3 },
        768: { slidesPerView: 3.5 },
        1024: { slidesPerView: 4.5 },
        1280: { slidesPerView: 5.5 }
      }
    });
    // Скрывать стрелки если фильмов меньше 2
    const slidesCount = swiperEl.querySelectorAll('.swiper-slide').length;
    if (slidesCount < 2) {
      swiperEl.querySelector(`.swiper-button-next-${index}`).style.display = 'none';
      swiperEl.querySelector(`.swiper-button-prev-${index}`).style.display = 'none';
    }
    swiperEl.querySelector(`.swiper-button-next-${index}`).addEventListener('click', () => {
      if (swiper.isEnd) {
        swiper.slideTo(0, 0);
      }
    });
    swiperEl.querySelector(`.swiper-button-prev-${index}`).addEventListener('click', () => {
      if (swiper.isBeginning) {
        swiper.slideTo(swiper.slides.length - 1, 0);
      }
    });
  });
});
</script>

<?php require_once "./layout/footer.php"; ?>