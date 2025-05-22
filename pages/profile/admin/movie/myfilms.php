<?php
session_start();
require_once __DIR__ . '/../../../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT * FROM movies WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$userId]);
$myMovies = $stmt->fetchAll();
?>




<div class="my-movies-container">
    <h2>Мои добавленные фильмы</h2>

    <?php if (count($myMovies) === 0): ?>
        <p class="no-movies">Фильмы не найдены.</p>
    <?php else: ?>
        <?php foreach ($myMovies as $movie): ?>
            <div class="movie-card">
               <a href="/movie?id=18" class="movie-link" title="Перейти к фильму <?= htmlspecialchars($movie['title']) ?>">


                    <div class="movie-poster">
                        <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="Постер <?= htmlspecialchars($movie['title']) ?>">
                    </div>
                    <div class="movie-info">
                        <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
                        <p class="movie-genre"><?= htmlspecialchars($movie['genre']) ?></p>
                        <p class="movie-description"><?= nl2br(htmlspecialchars($movie['description'])) ?></p>
                        <p class="movie-year"><strong>Год выпуска:</strong> <?= htmlspecialchars($movie['year'] ?? 'не указан') ?></p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>.my-movies-container {
    max-width: 800px;
    margin: 30px auto;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    padding: 0 15px;
}

.my-movies-container h2 {
    color: #2c3e50;
    border-bottom: 3px solid #3498db;
    padding-bottom: 8px;
    margin-bottom: 25px;
    font-weight: 700;
    font-size: 2rem;
}

.no-movies {
    font-style: italic;
    color: #777;
    font-size: 1.1rem;
    text-align: center;
    margin-top: 40px;
}

.movie-card {
    display: flex;
    background: #fefefe;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 25px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.movie-card:hover {
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}

.movie-link {
    display: flex;
    text-decoration: none;
    color: inherit;
    width: 100%;
}

.movie-poster {
    flex-shrink: 0;
    width: 150px;
    height: 225px;
    overflow: hidden;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
}

.movie-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.movie-card:hover .movie-poster img {
    transform: scale(1.05);
}

.movie-info {
    padding: 15px 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    flex-grow: 1;
}

.movie-title {
    margin: 0 0 6px 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
}

.movie-genre {
    font-weight: 600;
    color: #3498db;
    margin: 0 0 12px 0;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.9rem;
}

.movie-description {
    flex-grow: 1;
    font-size: 1rem;
    line-height: 1.4;
    color: #555;
    margin-bottom: 10px;
    white-space: pre-wrap;
}

.movie-year {
    font-size: 0.9rem;
    color: #888;
}


</style>