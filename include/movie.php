<?php
require_once __DIR__ . "/../config/db.php";

use App\Models\Movie;
use App\Controllers\MovieController;


$MovieModel = new Movie($conn);
$MovieController = new MovieController($MovieModel);

$movies = $MovieController->getAllMovies();
?>


<?php $titleName = "Movies"; ?>
<?php require_once "./layout/header.php"; ?>
<?php require_once "./layout/nav.php"; ?>

<div class="container">
    <div class="movie">
        <div class="movie__grid">
            <?php foreach ($movies as $movie): ?>
                <a href="/movieView?id=<?= htmlspecialchars($movie['id']) ?>" class="movie__item">
                    <div class="movie__image">
                        <img src="<?= htmlspecialchars($movie['poster_path']) ?>"
                            alt="<?= htmlspecialchars($movie['title']) ?>" class="movie__img">
                    </div>
                    <div class="movie__info">
                        <h3 class="movie__title" title="<?= htmlspecialchars($movie['title']) ?>">
                            <?= htmlspecialchars($movie['title']) ?>
                        </h3>
                        <span class="movie__type">Жанр: <?= htmlspecialchars($movie['genre']) ?></span>

                    </div>
                    <div class="movie__desc-wrap">
                        <div class="movie__desc" title="<?= htmlspecialchars($movie['description']) ?>">
                            <?= htmlspecialchars($movie['description']) ?>
                        </div>
                        <div class="movie__year">
                            <span class="movie__year-text">Год: <?= htmlspecialchars($movie['year']) ?></span>
                        </div>
                        <div class="created_movie">
                            <span class="created_movie_text">Создал: <?= htmlspecialchars($movie['created_by'] ?? 'Админ')  ?></span>
                            <span class="created_movie_text">Дата: <?= htmlspecialchars($movie['created_at']) ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<style>
    .movie__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
    gap: 32px;
    margin-top: 36px;
}

.movie__item {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 6px 28px rgba(79, 140, 255, 0.1);
    overflow: hidden;
    text-decoration: none;
    color: #222;
    transition: box-shadow 0.22s, transform 0.22s;
    min-height: 420px;
    position: relative;
    border: 2px solid #e5e7eb;
}

.movie__item:hover {
    box-shadow: 0 12px 36px rgba(79, 140, 255, 0.18);
    transform: translateY(-6px) scale(1.035);
}

.movie__image {
    width: 100%;
    aspect-ratio: 2/3;
    min-height: 320px;
    max-height: 420px;
    overflow: hidden;
    background: #f3f6fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0 0 0 0;
    position: relative;
}

.movie__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-bottom: none;
    border-radius: 0;
    transition: transform 0.35s cubic-bezier(0.4, 2, 0.6, 1), box-shadow 0.2s;
    box-shadow: 0 2px 12px rgba(79, 140, 255, 0.08);
}

.movie__item:hover .movie__img {
    transform: scale(1.055);
    box-shadow: 0 8px 32px rgba(79, 140, 255, 0.13);
}

.movie__info {
    padding: 18px 18px 0 18px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.movie__title {
    font-size: 1.18rem;
    font-weight: 700;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #2356c7;
}

.movie__type {
    font-size: 0.98rem;
    color: #4f8cff;
    font-weight: 500;
}

.movie__desc-wrap {
    padding: 12px 18px 18px 18px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
}

.movie__desc {
    font-size: 0.98rem;
    color: #444;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 3.5em;
    max-height: 4.5em;
}

.movie__year {
    font-size: 0.95rem;
    color: #888;
    margin-bottom: 2px;
}

.created_movie {
    font-size: 0.92rem;
    color: #b0b0b0;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.created_movie_text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
<?php require_once "./layout/footer.php"; ?>