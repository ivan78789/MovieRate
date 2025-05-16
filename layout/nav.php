<?php

require_once "./config/function.php";

if (isset($_POST['logout'])) {
    session_destroy();
    redirect_to('/');
}


?>
<header class="header container">
    <div class="header__logo">
        <img src="./assets/img/svg/logo.svg" alt="Organic Logo">
        <a href="./index.php">Organic</a>
    </div>
    <nav class="header__nav">
        <a href="/" class="header__link">Home</a>
        <a href="/about" class="header__link">About</a>
        <!-- выпадающее меню -->
        <div class="header__link_dropdown">
            <button class="header__link" id="drop-down_page">
                <span class="header-dropdawn_pages">
                    Pages
                    <img class="arrow-pages" id="drop-down_arrow" src="./assets/img/svg/icon-arrow.svg"
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
        <a href="/movie" class="header__link">Shop</a>
        <a href="/movie" class="header__link">Projects</a>
        <a href="/movie" class="header__link">News</a>
    </nav>
    <div class="header__nav-search-btn">

        <div class="header__nav-search">

            <input type="text" class="header__input" placeholder="">
            <a href=""></a><button class="header__input-btn"><img src="assets\img\svg\serch.svg"
                    alt="serch_icon"></button>

        </div>

        <div class="basket_nav">
            <a href="/basket"> <span class="basket">
                    <img src="assets/img/svg/basket.svg" alt="Basket Icon"> Cart (0)
                </span></a>
        </div>
    </div>
</header>