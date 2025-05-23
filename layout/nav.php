<?php
require_once __DIR__ . '/../config/function.php';

if (isset($_POST['logout'])) {
    session_destroy();
    redirect_to('/');
}
// Это суперглобальная переменная PHP, которая возвращает текущий URL-путь, который пользователь открыл.
$currentPage = $_SERVER['REQUEST_URI'];
?>


<header class="header">
  <div class="container">
    <div class="header__container">
      <div class="header__logo">MoovieRate</div>

      <nav class="header__nav">
        <a href="/" class="header__link">Главная</a>

        <!-- КНОПКА ДЛЯ ВЫПАДАЮЩЕГО СПИСКА ЖАНРОВ -->
        <div class="header__dropdown-wrap">
          <div class="header__link header__link--dropdown" id="genres-toggle">
            Жанры
            <img src="assets/img/svg/icon-arrow.svg" alt="▼" style="width: 12px;" />
          </div>

          <div class="header__dropdown" id="genres-dropdown">
            <button class="header__dropdown-btn">Все</button>
            <?php $activeGenre = $_GET['genre'] ?? 'Все'; ?>
<a href="/viewmovie?genre=Криминал" class="header__dropdown-btn <?= $activeGenre === 'Криминал' ? 'active' : '' ?>">Криминал</a>
            <button class="header__dropdown-btn">Комедия</button>
            <button class="header__dropdown-btn">Драма</button>
            <button class="header__dropdown-btn">Ужасы</button>
            <button class="header__dropdown-btn">Триллер</button>
            <button class="header__dropdown-btn">Криминал</button>

            <!-- Дополнительные жанры -->
            <div id="more-genres" style="display: none;">
              <button class="header__dropdown-btn">Фантастика</button>
              <button class="header__dropdown-btn">Приключения</button>
              <button class="header__dropdown-btn">Аниме</button>
              <button class="header__dropdown-btn">Мелодрама</button>
            </div>

            <!-- Кнопка "Больше" -->
            <button class="header__dropdown-btn" id="show-more-btn">Больше</button>
          </div>
        </div>

        <a href="/viewmovie" class="header__link">Фильмы</a>
      </nav>

      <div class="header__actions">
        <div class="header__search">
          <img src="assets/img/svg/search.svg" alt="Поиск" width="20">
        </div>

        <div class="header__profile">
          <div class="header__icon">
            <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets/img/avatar/default-avatar.png') ?>" alt="Аватар" class="avatar-img">
          </div>
          <div class="header__dropdown">
            <a href="/Profile" class="header__dropdown-link">Профиль</a>
            <form method="POST" style="margin: 0;">
              <a href="/logout" class="header__dropdown-link">Выйти</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<style>
.header {
  width: 100%;
  background: #181c24;
  color: #fff;
  position: relative;
  box-shadow: 0 2px 16px rgba(35, 86, 199, 0.04);
  z-index: 100;
}

.header__container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  padding: 0 24px;
  min-height: 64px;
}

.header__logo {
  font-size: 2rem;
  font-weight: 800;
  color: #38bdf8;
  text-shadow: 0 2px 8px rgba(56, 189, 248, 0.08);
}

.header__nav {
  display: flex;
  align-items: center;
  gap: 28px;
  flex-wrap: wrap;
}

.header__link {
  color: #fff;
  text-decoration: none;
  padding: 8px 18px;
  border-radius: 6px;
  font-size: 1.08rem;
  font-weight: 500;
  transition: color 0.2s, background 0.2s;
  position: relative;
}

.header__link:hover,
.header__link.active {
  color: #38bdf8;
  background: rgba(56, 189, 248, 0.08);
}

.header__dropdown-wrap {
  position: relative;
}

.header__link--dropdown {
  cursor: pointer;
  user-select: none;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border-radius: 8px;
  transition: background 0.2s ease;
}

.header__link--dropdown:hover {
  background: rgba(255, 255, 255, 0.05);
}

.header__dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  background: #1f2533;
  border: 1.5px solid #2d3344;
  border-radius: 12px;
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.3);
  display: none;
  flex-direction: column;
  min-width: 190px;
  z-index: 200;
  padding: 14px 0;
  opacity: 0;
  transform: translateY(-10px);
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.header__dropdown.active {
  display: flex;
  opacity: 1;
  transform: translateY(0);
}

.header__dropdown-btn,
.header__dropdown-link {
  width: 100%;
  background: none;
  border: none;
  color: #e0e0e0;
  text-align: left;
  padding: 12px 20px;
  font-size: 1rem;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}

.header__dropdown-btn:hover,
.header__dropdown-link:hover {
  background-color: #34798c;
  color: #ffffff;
}

.header__actions {
  display: flex;
  align-items: center;
  gap: 20px;
  position: relative;
}

.header__search {
  font-size: 18px;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  color: #f3f4f6;
  transition: background 0.2s ease;
}

.header__search:hover {
  background: rgba(56, 189, 248, 0.15);
}

.header__icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border-radius: 50%;
}



</style>

<script>

  document.addEventListener('DOMContentLoaded', () => {
    const genresToggle = document.getElementById('genres-toggle');
    const genresDropdown = document.getElementById('genres-dropdown');
    const profileIcon = document.querySelector('.header__profile');
    const profileDropdown = profileIcon?.querySelector('.header__dropdown');
    const moreBtn = document.getElementById('show-more-btn');
    const moreGenres = document.getElementById('more-genres');

    document.addEventListener('click', (e) => {
      const clickInsideGenres = genresToggle && genresToggle.contains(e.target);
      const clickInsideProfile = profileIcon && profileIcon.contains(e.target);

      // Выпадающее меню жанров
      if (clickInsideGenres) {
        genresDropdown?.classList.toggle('active');
      } else if (genresDropdown && !genresDropdown.contains(e.target)) {
        genresDropdown.classList.remove('active');
      }

      // Выпадающее меню профиля
      if (clickInsideProfile) {
        profileDropdown?.classList.toggle('active');
      } else {
        profileDropdown?.classList.remove('active');
      }
    });

    // Кнопка "Больше" для жанров
    if (moreBtn && moreGenres) {
      moreBtn.addEventListener('click', () => {
        const isHidden = moreGenres.style.display === 'none' || moreGenres.style.display === '';
        moreGenres.style.display = isHidden ? 'block' : 'none';
        moreBtn.textContent = isHidden ? 'Меньше' : 'Больше';
      });
    }
  });




</script>


