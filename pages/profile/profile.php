<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../app/Models/Reviews.php';

use App\Models\Reviews;

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$userId = $_SESSION['user_id'];

// Получаем данные пользователя
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

// Получаем отзывы пользователя
$reviewModel = new Reviews($conn);
$userReviews = $reviewModel->getReviewsByUserId($userId);
?>

<?php $titleName = 'Profile' ?>
<?php $titlePage = 'Личный кабинет' ?>
<?php require_once "./layout/header.php"; ?>
<?php require_once "./layout/nav.php"; ?>

<div class="profile-container">
    <div class="profile-title">Личный кабинет</div>
    <div class="profile-hello">Привет, <?= $username ?>!</div>

    <?php if ($isAdmin): ?>
        <div class="profile-role">Вы — администратор и можете добавлять или редактировать фильмы.</div>
        <div class="profile-actions">
            <a href="/pages/movie/AddMovie">Добавить фильм</a>
            <a href="/pages/profile/Mymovies">Мои фильмы</a>
            <a href="/pages/movie/editMovie">Редактировать фильмы</a>
        </div>
    <?php else: ?>
        <div class="profile-role" style="color:#888;">
            Вы обычный пользователь и можете просматривать фильмы и оставлять отзывы.
        </div>
    <?php endif; ?>

    <div class="profile-links">
        <a href="/">На главную</a>
        <a href="/logout">Выйти</a>
    </div>
</div>

<h2>Мои отзывы</h2>

<?php if (empty($userReviews)): ?>
    <p>Вы ещё не оставили ни одного отзыва.</p>
<?php else: ?>
    <ul class="user-reviews">
        <?php foreach ($userReviews as $review): ?>
            <li>
                <strong>Фильм:</strong> <?= htmlspecialchars($review['movie_title']) ?><br>
                <strong>Оценка:</strong> <?= $review['rating'] ?>/10<br>
                <strong>Комментарий:</strong> <?= htmlspecialchars($review['comment']) ?><br>
                <em>Дата:</em> <?= date('d.m.Y H:i', strtotime($review['created_at'])) ?>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<style>
    .user-reviews {
        list-style: none;
        padding: 0;
        margin-top: 20px;
    }

    .user-reviews li {
        background: #f8f8f8;
        padding: 15px;
        margin-bottom: 12px;
        border-radius: 10px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
</style>

<?php require_once "./layout/footer.php"; ?>