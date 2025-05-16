<?php
session_start();

require_once __DIR__ . '/../../config/db.php';

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

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <title>Личный кабинет</title>
</head>

<body>
    <h2>Личный кабинет</h2>
    <p>Привет, <?= $username ?>!</p>

    <?php if ($isAdmin): ?>
        <p>Вы — администратор и можете добавлять или редактировать фильмы.</p>
        <a href="/add_movie.php">Добавить фильм</a><br>
        <a href="/edit_movies.php">Редактировать фильмы</a><br>
    <?php else: ?>
        <p>Вы обычный пользователь и можете просматривать фильмы и оставлять отзывы.</p>
    <?php endif; ?>

    <a href="/">На главную</a> |
    <a href="/logout.php">Выйти</a>
</body>

</html>