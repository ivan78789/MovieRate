<?php
session_start();
require_once __DIR__ . '/../../../../config/db.php';
$titleName = 'Все фильмы';
$isAdmin = $_SESSION['is_admin'] ?? 0;
$activeGenre = $_GET['genre'] ?? 'Все';
?>
<?php require_once "./layout/header.php"; ?>
<?php require_once "./layout/nav.php"; ?>
<h1 class="all_movie-view container">Все фильмы<?= $activeGenre && $activeGenre !== 'Все' ? ' - ' . htmlspecialchars($activeGenre) : '' ?></h1>
<a class="back " href="/">Назад</a>

<div id="movie-list" class="movie-grid container"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const genre = <?= json_encode($activeGenre) ?>;
    let url = '/searchmovies?';
    if (genre && genre !== 'Все') {
        url += `genre=${encodeURIComponent(genre)}`;
    }

    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            return res.json();
        })
        .then(data => {
            const container = document.getElementById('movie-list');
            if (!container) {
                console.error('Контейнер #movie-list не найден');
                return;
            }
            container.innerHTML = data.length === 0 ? '<p>Фильмы не найдены</p>' : '';
            data.forEach(movie => {
                const el = document.createElement('div');
                el.classList.add('movie-card');
                el.innerHTML = `
                    <a href="/movieView?id=${movie.id}" class="movie-card__link container">
                        <div class="movie-card__image">
                            <img src="${movie.poster_path || '/assets/img/placeholder.jpg'}" alt="${movie.title}">
                        </div>
                        <div class="movie-card__content">
                            <h3 class="movie-card__title">${movie.title}</h3>
                            <p class="movie-card__genre">Жанр: ${movie.genre || 'Не указан'}</p>
                            <p class="movie-card__year">Год: ${movie.year || 'Не указан'}</p>
                            <p class="movie-card__rating">Рейтинг: ${movie.rating || 'Не указан'}</p>
                            <p class="movie-card__desc" title="${movie.description || ''}">
                                ${movie.description ? movie.description.substring(0, 100) + '...' : 'Описание отсутствует'}
                            </p>
                            ${<?= $isAdmin ? 'true' : 'false' ?> ? `
                                <div class="movie-actions">
                                    <a href="/editmovie?id=${movie.id}">Редактировать</a>
                                    <a href="/deletemovie?id=${movie.id}" onclick="return confirm('Удалить фильм?');">Удалить</a>
                                </div>
                            ` : ''}
                        </a>`;
                container.appendChild(el);
            });
        })
        .catch(err => {
            console.error('Ошибка запроса:', err);
            const container = document.getElementById('movie-list');
            if (container) container.innerHTML = '<p>Ошибка при загрузке фильмов</p>';
        });
});
</script>
<?php require_once "./layout/footer.php"; ?>
<style>
    .all_movie-view{
        margin-top: 40px;
        margin-bottom: 10px;
        font-size: 24px;
        color: #333;
    }
</style>