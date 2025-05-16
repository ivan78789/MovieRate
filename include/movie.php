<?php

require_once __DIR__ . "/../config/db.php";

use App\Models\Movie;
use App\Controllers\MovieController;

$MovieModel = new Movie($conn);
$MovieController = new MovieController($MovieModel);

$movies = $MovieController->getAllMovies();
?>

~
<?php $titleName = "Movies"; ?>
<?php require_once "./layout/header.php"; ?>
<?php require_once "./layout/nav.php"; ?>

<div class="container">
    <div class="movie">
        <div class="movie__grid">
            <?php foreach ($movies as $movie): ?>
                <a href="/movie/<?= $movie['id'] ?>" class="movie__item">
                    <div class="movie__image">
                        <img src="<?= htmlspecialchars($movie['poster_path']) ?>"
                            alt="<?= htmlspecialchars($movie['title']) ?>" class="movie__img">
                    </div>
                    <div class="movie__info">
                        <h3 class="movie__title"><?= htmlspecialchars($movie['title']) ?></h3>
                        <span class="movie__type"><?= htmlspecialchars($movie['genre']) ?></span>
                    </div>
                    <div>
                        <div class="movie_desk">
                            <p><?= htmlspecialchars($movie['description']) ?></p>
                        </div>
                        <div class="movie__year">
                            <span class="movie__year-text"><?= htmlspecialchars($movie['year']) ?></span>
                        </div>
                        <div class="created_movie">
                            <span class="created_movie_text">Создано: <?= htmlspecialchars($movie['created_by']) ?></span>
                            <span class="created_movie_text">Дата: <?= htmlspecialchars($movie['created_at']) ?></span>
                        </div>
                    </div>
                </a>

            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require_once "./layout/footer.php"; ?>