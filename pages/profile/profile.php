<?php
session_start();

require_once __DIR__ . '/../../config/db.php';
$titleName = "Личный кабинет";
// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: /signin.php'); // или страница входа
    exit;
}

$userId = $_SESSION['user_id'];

// Получаем данные пользователя, включая роль is_admin
$query = "SELECT username, is_admin FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "Пользователь не найден";
    exit;
}

$username = htmlspecialchars($user['username']);
$isAdmin = (bool) $user['is_admin'];
?>
<?php $titleName = 'Profile' ?>
<?php $titlePage = 'Личный кабинет' ?>
<?php require_once "./layout/header.php"; ?>
<?php require_once "./layout/nav.php"; ?>


</head>
<div class="profile-container">
    <div class="profile-title">Личный кабинет</div>
    <div class="profile-hello">Привет, <?= $username ?>!</div>
    <?php if ($isAdmin): ?>
        <div class="profile-role">Вы — администратор и можете добавлять или редактировать фильмы.</div>
        <div class="profile-actions">
            <a href="/pages/movie/add.php">Добавить фильм</a>
            <a href="/pages/profile/my-movies.php">Мои фильмы</a>
            <a href="/pages/movie/edit.php">Редактировать фильмы</a>
        </div>
    <?php else: ?>
        <div class="profile-role" style="color:#888;">Вы обычный пользователь и можете просматривать фильмы и оставлять
            отзывы.</div>
    <?php endif; ?>
    <div class="profile-links">
        <a href="/">На главную</a>
        <a href="/logout">Выйти</a>
    </div>
</div>
<?php require_once "./layout/footer.php"; ?>