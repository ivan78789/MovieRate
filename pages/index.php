<?php
session_start();
require_once __DIR__ . "/../config/db.php"; 
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username, avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>
<?php $titleName = 'MovieRate' ?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/nav.php'; ?>

<?php require_once __DIR__ . '/../include/movie.php' ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>