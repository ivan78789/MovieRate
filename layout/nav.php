<?php
require_once __DIR__ . '/../config/function.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_POST['logout'])) {
    session_destroy();
    redirect_to('/');
}
$currentPage = $_SERVER['REQUEST_URI'];
?>
<link rel="stylesheet" href="../assets/css/style.css">
<header class="Myheader">
  <div class="container">
    <div class="header__container">
      
      <div class="header__logo">MoovieRate</div>

      <nav class="header__nav">
        <a href="/" class="header__link">Главная</a>

        <?php
          $activeGenre = $_GET['genre'] ?? 'Все';
        ?>

        <div class="header__dropdown-wrap">
          <div class="header__link header__link--dropdown" id="genres-toggle">
            Жанры
            <img src="assets/img/svg/icon-arrow.svg" alt="▼" style="width: 12px;" />
          </div>

          <div class="header__dropdown genres-dropdown" id="genres-dropdown">
            <a href="/viewmovie" class="header__dropdown-btn <?= ($activeGenre === 'Все') ? 'active' : ''; ?>">Все</a>
            <a href="/viewmovie?genre=Драма" class="header__dropdown-btn <?= ($activeGenre === 'Драма') ? 'active' : ''; ?>">Драма</a>
            <a href="/viewmovie?genre=Криминал" class="header__dropdown-btn <?= ($activeGenre === 'Криминал') ? 'active' : ''; ?>">Криминал</a>
            <a href="/viewmovie?genre=Комедия" class="header__dropdown-btn <?= ($activeGenre === 'Комедия') ? 'active' : ''; ?>">Комедия</a>
            <a href="/viewmovie?genre=Фэнтези" class="header__dropdown-btn <?= ($activeGenre === 'Фэнтези') ? 'active' : ''; ?>">Фэнтези</a>
            <a href="/viewmovie?genre=Ужасы" class="header__dropdown-btn <?= ($activeGenre === 'Ужасы') ? 'active' : ''; ?>">Ужасы</a>
            <a href="/viewmovie?genre=Мелодрама" class="header__dropdown-btn <?= ($activeGenre === 'Мелодрама') ? 'active' : ''; ?>">Мелодрама</a>
            <a href="/viewmovie?genre=Приключения" class="header__dropdown-btn <?= ($activeGenre === 'Приключения') ? 'active' : ''; ?>">Приключения</a>

            <div id="more-genres" style="display:none;">
              <a href="/viewmovie?genre=Боевик" class="header__dropdown-btn <?= ($activeGenre === 'Боевик') ? 'active' : ''; ?>">Боевик</a>
              <a href="/viewmovie?genre=Детектив" class="header__dropdown-btn <?= ($activeGenre === 'Детектив') ? 'active' : ''; ?>">Детектив</a>
              <a href="/viewmovie?genre=Исторический" class="header__dropdown-btn <?= ($activeGenre === 'Исторический') ? 'active' : ''; ?>">Исторический</a>
              <a href="/viewmovie?genre=Документальный" class="header__dropdown-btn <?= ($activeGenre === 'Документальный') ? 'active' : ''; ?>">Документальный</a>
              <a href="/viewmovie?genre=Фантастика" class="header__dropdown-btn <?= ($activeGenre === 'Фантастика') ? 'active' : ''; ?>">Фантастика</a>
            </div>

            <button type="button" class="header__dropdown-btn" id="show-more-btn">Больше</button>
          </div>
        </div>

        <a href="/viewmovie" class="header__link <?= (strpos($_SERVER['REQUEST_URI'], '/viewmovie') !== false) ? 'active' : ''; ?>">Фильмы</a>
      </nav>

      <div class="header__search">
        <img class="header__search_img" src="assets/img/svg/search.svg" alt="Поиск" />
        <div class="header__search-box">
          <input type="text" id="search-input" placeholder="Найти фильм..." />
          <div class="header__filters">
            <select id="filter-genre">
              <option value="">Все жанры</option>
              <option value="Драма">Драма</option>
              <option value="Криминал">Криминал</option>
              <option value="Комедия">Комедия</option>
              <option value="Фэнтези">Фэнтези</option>
              <option value="Ужасы">Ужасы</option>
              <option value="Мелодрама">Мелодрама</option>
              <option value="Приключения">Приключения</option>
              <option value="Боевик">Боевик</option>
              <option value="Детектив">Детектив</option>
              <option value="Исторический">Исторический</option>
              <option value="Документальный">Документальный</option>
              <option value="Фантастика">Фантастика</option>
            </select>
            <input type="number" id="filter-year" placeholder="Год" min="1900" max="2025" />
            <select id="filter-rating">
              <option value="">Любой рейтинг</option>
              <option value="9">9+</option>
              <option value="8">8+</option>
              <option value="7">7+</option>
              <option value="6">6+</option>
              <option value="5">5+</option>
            </select>
            <button id="apply-filters">Применить</button>
          </div>
        </div>
      </div>

      <div class="header__profile">
        <?php if (!empty($user)): ?>
          <div class="header__icon" id="profile-toggle">
            <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets/img/avatar/default-avatar.png') ?>" alt="Аватар" class="avatar-img" />
          </div>
          <div class="header__dropdown" id="profile-dropdown">
            <a href="/Profile" class="header__dropdown-link">Профиль</a>
            <form method="POST" action="/logout" class="logout-form">
              <button type="submit" name="logout" class="header__dropdown-link btn-logout">Выйти</button>
            </form>
          </div>
        <?php else: ?>
          <div class="header__auth-links">
            <a href="/signin" class="header__link">Войти</a>
            <span>или</span>
            <a href="/signup" class="header__link">Зарегистрироваться</a>
          </div>
        <?php endif; ?>
      </div>

    </div> <!-- /.header__container -->
  </div> <!-- /.container -->
</header>



<script src="../assets/js/app.js"></script>
<script>


document.addEventListener('DOMContentLoaded', () => {
    const genresToggle = document.getElementById('genres-toggle');
    const genresDropdown = document.getElementById('genres-dropdown');
    const moreBtn = document.getElementById('show-more-btn');
    const moreGenres = document.getElementById('more-genres');
    const searchIcon = document.querySelector('.header__search_img');
    const searchBox = document.querySelector('.header__search-box');
    const filtersBox = document.querySelector('.header__filters');
    const searchInput = document.getElementById('search-input');
    const filterGenre = document.getElementById('filter-genre');
    const filterYear = document.getElementById('filter-year');
    const filterRating = document.getElementById('filter-rating');
    const applyFiltersBtn = document.getElementById('apply-filters');

    let searchTimeout;

    if (genresToggle && genresDropdown) {
        genresToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            genresDropdown.classList.toggle('active');
        });
    }

    if (moreBtn && moreGenres) {
        moreBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = moreGenres.style.display === 'none' || moreGenres.style.display === '';
            moreGenres.style.display = isHidden ? 'block' : 'none';
            moreBtn.textContent = isHidden ? 'Меньше' : 'Больше';
        });
    }
    // Профиль: показать/скрыть дропдаун по клику на аватар
    const profile = document.querySelector('.header__profile');
    const profileDropdown = profile?.querySelector('.header__dropdown');
    const profileIcon = profile?.querySelector('.header__icon');

   if (profile && profileDropdown && profileIcon) {
  profileIcon.addEventListener('click', (e) => {
    e.stopPropagation();
    profileDropdown.classList.toggle('active');
  });

  document.addEventListener('click', (e) => {
    if (!profile.contains(e.target)) {
      profileDropdown.classList.remove('active');
    }
  });
}

    if (searchIcon && searchBox && filtersBox) {
        searchIcon.addEventListener('click', (e) => {
            e.stopPropagation();
            searchBox.classList.toggle('active');
            filtersBox.style.display = searchBox.classList.contains('active') ? 'flex' : 'none';
            if (searchBox.classList.contains('active')) {
                searchInput.focus();
            }
        });
    }

    document.addEventListener('click', (e) => {
        if (genresDropdown && !genresDropdown.contains(e.target) && !genresToggle.contains(e.target)) {
            genresDropdown.classList.remove('active');
        }
        if (searchBox && !searchBox.contains(e.target) && !searchIcon.contains(e.target)) {
            searchBox.classList.remove('active');
            filtersBox.style.display = 'none';
        }
    });

    function performSearch() {
        const container = document.getElementById('movie-list');
        if (!container) {
            console.log('Контейнер #movie-list отсутствует на этой странице');
            return;
        }

        const query = searchInput?.value.trim() || '';
        const genre = filterGenre?.value || '';
        const year = filterYear?.value || '';
        const rating = filterRating?.value || '';

        let url = '/searchmovies?';
        const params = [];
        if (query) params.push(`query=${encodeURIComponent(query)}`);
        if (genre && genre !== 'Все') params.push(`genre=${encodeURIComponent(genre)}`);
        if (year) params.push(`year=${encodeURIComponent(year)}`);
        if (rating) params.push(`rating=${encodeURIComponent(rating)}`);
        url += params.join('&');

        fetch(url)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    container.innerHTML = data.length === 0 ? '<p>Фильмы не найдены</p>' : '';
                    container.classList.add('movie__grid'); // Добавляем класс для сетки
                    data.forEach(movie => {
                        const el = document.createElement('a');
                        el.classList.add('movie__item');
                        el.href = `/movieView?id=${movie.id}`;
                        el.innerHTML = `
                            <div class="movie__image">
                                <img src="${movie.poster_path || '/assets/img/placeholder.jpg'}" alt="${movie.title}" class="movie__img">
                            </div>
                            <div class="movie__info">
                                <h3 class="movie__title">${movie.title}</h3>
                                <p class="movie__type">${movie.genre || 'Не указан'}</p>
                            </div>
                            <div class="movie__desc-wrap">
                                <p class="movie__desc">${movie.description ? movie.description.substring(0, 150) + '...' : 'Описание отсутствует'}</p>
                                <p class="movie__year">${movie.year || 'Не указан'}</p>
                                <div class="created_movie">
                                    <span class="created_movie_text">Рейтинг: ${movie.rating || 'Не указан'}</span>
                                </div>
                            </div>`;
                        container.appendChild(el);
                    });
                } catch (e) {
                    console.error('Ответ не JSON:', text);
                    throw new Error('Не JSON');
                }
            })
            .catch(err => {
                console.error('Ошибка запроса:', err);
                if (container) {
                    container.innerHTML = '<p>Ошибка при поиске фильмов</p>';
                }
            });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                performSearch();
            }
        });
    }

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', () => {
            clearTimeout(searchTimeout);
            performSearch();
        });
    }
});
</script>





<style>

</style>

