<?php

require_once __DIR__ . '/router.php';

get('/', 'pages/index.php');
get('/movie', 'pages/movie.php');

get('/movieRate', 'pagesMovieRate.php');
get('/Profile', 'pages/profile/profile.php');

get('/signin', 'auth/sign-in.php');
post('/signin', 'auth/sign-in.php');

get('/signup', 'auth/sign-up.php');
post('/signup', 'auth/sign-up.php');



any('/404', './pages/404.php');