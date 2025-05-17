<?php
require_once __DIR__ . '/../config/function.php';

if (isset($_POST['logout'])) {
    session_destroy();
    redirect_to('/');
}
?>

<header class="header container">
    <div class="header__logo">
        <img src="/assets/img/svg/logo.svg " class="header__link" alt="Organic Logo">
        <a href="/" class="header__link">Organic</a>
    </div>
    <nav class="header__nav">
        <a href="/" class="header__link">Главная</a>
        <a href="/about" class="header__link">About</a>

        <div class="header__link_dropdown">
            <button class="header__link" id="drop-down_page">
                <span class="header-dropdawn_pages">
                    Pages
                    <img class="arrow-pages" id="drop-down_arrow" src="/assets/img/svg/icon-arrow.svg"
                        alt="Dropdown Arrow">
                </span>
            </button>
            <div class="drop-down hidden" id="drop-down_menu">
                <div class="drop-down-content">
                    <a href="/licenses">Licenses</a>
                    <a href="/contact">Contact Us</a>
                    <a href="/team">Our Team</a>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/Profile" class="header__link">Профиль</a>
            <a href="/logout" class="header__link">Выйти</a>
        <?php else: ?>
            <a href="/signin " class="header__link">Войти</a>
            <a href="/signup" class="header__link">Зарегистрироваться</a>
        <?php endif; ?>
    </nav>

    <div class="header__nav-search-btn">
        <form action="/search" method="GET" class="header__nav-search">
            <input type="text" name="query" class="header__input" placeholder="Search...">
            <button type="submit" class="header__input-btn">
                <img src="/assets/img/svg/serch.svg" alt="search_icon">
            </button>
        </form>

        <div class="basket_nav">
            <a href="/basket">
                <span class="basket">
                    <img src="/assets/img/svg/basket.svg" alt="Basket Icon"> Cart (0)
                </span>
            </a>
        </div>
    </div>
</header>