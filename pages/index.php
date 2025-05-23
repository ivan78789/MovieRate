<?php
session_start();
require_once __DIR__ . "/../config/db.php"; 
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username, avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

use App\Models\Movie;
use App\Controllers\MovieController;

$MovieModel = new Movie($conn);
$MovieController = new MovieController($MovieModel);

// Жанр из GET-параметра
$genre = $_GET['genre'] ?? null;

// Получаем фильмы по жанру
if ($genre && $genre !== 'Все') {
    $movies = $MovieController->getMoviesByGenre($genre);
} else {
    $movies = $MovieController->getAllMovies();
}

?>
<?php $titleName = 'MovieRate' ?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/nav.php'; ?>
<main>
    <div id="movie-list" class="movie-grid"></div>
</main>

<?php require_once __DIR__ . '/../include/movie.php' ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>