<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/router.php';

// главная
get('/', '/pages/index.php');

// фильмы
get('/movie', 'include/movie.php');
get('/movieView', 'include/movie-view.php');
post('/movieView', 'include/movie-view.php');
post('/ChangeProfile', 'pages/profile/change_profile.php');

// CRUD для фильмов (страницы админки)
get('/addmovie',    'pages/profile/admin/movie/add.php');
post('/addmovie',   'pages/profile/admin/movie/add.php');

get('/editmovie',   'pages/profile/admin/movie/edit.php');   // показывает форму
get('/deletemovie', 'pages/profile/admin/movie/delete.php');
get('/viewmovie',   'pages/profile/admin/movie/view.php');
get('/myfilms',   'pages/profile/admin/movie/myfilms.php');

// API-эндпоинты для CRUD (доступ по POST)
post('/addmovieApi',      'api/movie/add_movie.php');
post('/editmovieApi',     'api/movie/edit_movie.php');
post('/deletemovieApi',   'api/movie/delete_movie.php');    
get('/searchmovies', 'api/search_movies.php');


// (если нужен просмотр через POST — обычно не нужен)
// post('/viewmovie', 'pages/profile/admin/movie/view.php');

// отзывы к фильмам
get('/movieDelete', 'api/review/delete_review.php');
get('/movieEdit',   'api/review/edit_review.php');
post('/reviewAction','api/review/review_action.php');


// профиль
get('/Profile',      'pages/profile/profile.php');
get('/myreviews',    'pages/profile/my-reviews.php');
get('/mymovies',     'pages/profile/my-movies.php');
get('/UploadAvatar', 'pages/profile/upload_avatar.php');
post('/UploadAvatar','pages/profile/upload_avatar.php');

// аутентификация
get('/signup',  'auth/sign-up.php');
post('/signup','auth/sign-up.php');

get('/signin', 'auth/sign-in.php');
post('/signin','auth/sign-in.php');
get('/logout', 'auth/logout.php');
post('/logout','auth/logout.php');

// 404
any('/404', 'pages/404.php');
