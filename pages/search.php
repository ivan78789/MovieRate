<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../api/search_movies.php'; 

$query = $_GET['query'] ?? '';

$movies = [];
if ($query !== '') {
    $movies = searchMovies($query);
}

function searchMovies($query) {
    global $pdo; // твое подключение к БД

    $stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE :query");
    $stmt->execute(['query' => "%$query%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


  <title>Результаты поиска: <?= htmlspecialchars($query) ?></title>


<?php include 'header.php';  ?>

<div class="container">

  <div class id="movie-list" class="movie">
    <div class="movie__grid">
      <?php if (!empty($movies)): ?>
        <p>Ошибка фильм не найден</p>
        <?php else: ?>
        <?php foreach ($movies as $movie): ?>
          <a href="/movieView?id=<?= htmlspecialchars($movie['id']) ?>" class="movie__item">
            <div class="movie__image">
              <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" class="movie__img">
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
                <span class="created_movie_text">Создал: <?= htmlspecialchars($movie['created_by'] ?? 'Админ') ?></span>
                <span class="created_movie_text">Дата: <?= htmlspecialchars($movie['created_at']) ?></span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
    
</style>