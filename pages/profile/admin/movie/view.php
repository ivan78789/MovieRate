<?php
session_start();
require_once __DIR__ . '/../../../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$isAdmin = $_SESSION['is_admin'] ?? 0;

// Фильтрация по жанру
$genre = $_GET['genre'] ?? null;

if ($genre && $genre !== 'Все') {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE genre = ? ORDER BY created_at DESC");
    $stmt->execute([$genre]);
} else {
    $stmt = $conn->query("SELECT * FROM movies ORDER BY created_at DESC");
}

$movies = $stmt->fetchAll();
?>







<h1>Все фильмы<?= $genre && $genre !== 'Все' ? ' - ' . htmlspecialchars($genre) : '' ?></h1>
<a href="/">Назад</a>

<div class="movie-grid">
    <?php foreach ($movies as $movie): ?>
        <div class="movie-card">
            <?php if (!empty($movie['poster_path'])): ?>
                <img src="<?= htmlspecialchars($movie['poster_path']) ?>" alt="Постер">
            <?php endif; ?>

            <div class="movie-title"><?= htmlspecialchars($movie['title']) ?></div>
            <div class="movie-genre"><?= htmlspecialchars($movie['genre']) ?> (<?= $movie['year'] ?>)</div>

            <?php if ($isAdmin): ?>
                <div class="movie-actions">
                    <a href="/editmovie?id=<?= $movie['id'] ?>">Редактировать</a>
                    <a href="/deletemovie?id=<?= $movie['id'] ?>" onclick="return confirm('Удалить фильм?');">Удалить</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

    <style>
.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 24px;
}
.movie-card {
    background: white;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.movie-card img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    margin-bottom: 12px;
}
.movie-title {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 6px;
}
.movie-genre {
    color: #555;
    font-size: 0.9rem;
    margin-bottom: 8px;
}
.movie-actions a {
    display: inline-block;
    margin-right: 8px;
    color: #2563eb;
    text-decoration: none;
    font-size: 0.9rem;
}
.movie-actions a:hover {
    text-decoration: underline;
}

    </style>