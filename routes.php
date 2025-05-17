<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/router.php';
// главная
get('/', '/pages/index.php');
// фильмы
get('/movie', 'include/movie.php');
get('/movieView', 'include/movie-view.php');
post('/movieView', 'include/movie-view.php');

// профиль
get('/Profile', 'pages/profile/profile.php');
get('/myreviews', 'pages/profile/my-reviews.php');
get('/mymovies', 'pages/profile/my-movies.php');

// для адмиина редактирование фильмов удаление и длобавление
get('/addmovie', 'pages/profile/admin/add.php');
get('/editmovie', 'pages/profile/admin/edit.php');
get('/deletemovie', 'pages/profile/admin/delete.php');
get('/viewmovie', 'pages/profile/admin/view.php');



// регистрация
get('/signup', 'auth/sign-up.php');
post('/signup', 'auth/sign-up.php');

// вход
get('/signin', 'auth/sign-in.php');
post('/signin', 'auth/sign-in.php');

// выход
get('/logout', 'auth/logout.php');
post('/logout', 'auth/logout.php');

// 404
any('/404', 'pages/404.php');