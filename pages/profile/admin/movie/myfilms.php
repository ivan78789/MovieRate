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


<?php $titleName = 'Мои фиьлмы'?>

<?php require_once "./layout/header.php"; ?>
<?php require_once "./layout/nav.php"; ?>

<div class="my-movies-container">
    <h2>Мои добавленные фильмы</h2>
<a class="back " href="/Profile">Назад</a>

    <?php if (count($myMovies) === 0): ?>
        <p class="no-movies">Фильмы не найдены.</p>
    <?php else: ?>
        <?php foreach ($myMovies as $movie): ?>
            <div class="movie-card">
              <a href="/movieView?id=<?= $movie['id'] ?>" class="movie-link" title="Перейти к фильму <?= htmlspecialchars($movie['title']) ?>">



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