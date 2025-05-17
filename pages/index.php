<?php
session_start();

?>
<?php $titleName = 'MovieRate' ?>
<?php require_once __DIR__ . "/../config/db.php"; ?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/nav.php'; ?>

<?php require_once __DIR__ . '/../include/movie.php' ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>