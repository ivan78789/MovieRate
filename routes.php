<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/router.php';

get('/', '/pages/index.php');
get('/movie', 'include/movie.php');
get('/movieRate', 'pages/MovieRate.php');
get('/Profile', 'pages/profile/profile.php');

get('/signin', 'auth/sign-in.php');
post('/signin', 'auth/sign-in.php');

get('/signup', 'auth/sign-up.php');
post('/signup', 'auth/sign-up.php');

get('/logout', 'auth/logout.php');
post('/logout', 'auth/logout.php');


any('/404', 'pages/404.php');