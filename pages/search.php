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
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE :query");
    $stmt->execute(['query' => "%$query%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php $titleName = 'Поиск фильмов' ?>
<?php require_once "./layout/header.php"; ?>

    <div class="container">
        <input id="search-input" type="text" value="<?= htmlspecialchars($_GET['query'] ?? '') ?>" placeholder="Поиск фильмов" autocomplete="off" />
        <div id="movie-list" class="movie-grid1"></div>
    </div>

    <script>
        async function fetchMovies(query) {
            const movieList = document.getElementById('movie-list');
            try {
                const response = await fetch('/api/search_movies.php?query=' + encodeURIComponent(query));
                if (!response.ok) {
                    movieList.innerHTML = '<p>Ошибка при загрузке данных</p>';
                    return;
                }
                const movies = await response.json();

                if (movies.length === 0) {
                    movieList.innerHTML = '<p>Фильмы не найдены</p>';
                    return;
                }
const html = movies.map(movie => `
<a href="/movieView?id=${movie.id}" class="movie__item">
  <div class="movie__image">
    <img src="${movie.poster_path}" alt="${movie.title}" class="movie__img" loading="lazy">
  </div>
  <div class="movie__info">
    <h3 class="movie__title" title="${movie.title}">${movie.title}</h3>
    <span class="movie__type">Жанр: ${movie.genre}</span>
  </div>
  <div class="movie__desc-wrap" title="${movie.description}">
    ${movie.description}
    <div class="movie__year">Год: ${movie.year}</div>
    <div class="created_movie">
      <span>Создал: ${movie.created_by || 'Админ'}</span>
      <span>Дата: ${movie.created_at}</span>
    </div>
  </div>
</a>

`).join('');

                movieList.innerHTML = html;
            } catch (error) {
                movieList.innerHTML = '<p>Ошибка при загрузке данных</p>';
                console.error('Fetch error:', error);
            }
        }

        const searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim();
            if (query.length > 1) {
                fetchMovies(query);
            } else {
                document.getElementById('movie-list').innerHTML = '';
            }
        });

        if (searchInput.value.trim() !== '') {
            fetchMovies(searchInput.value.trim());
        }
    </script>
