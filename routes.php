<?php

require_once __DIR__ . '/router.php';

get('/', 'pages/index.php');
get('/movie', 'pages/movie.php');

get('/movieRate', 'pagesMovieRate.php');




any('/404', './views/404.php');