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

// Получаем данные пользователя (добавили avatar)
$query = "SELECT username, is_admin, avatar FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "Пользователь не найден";
    exit;
}

$username = htmlspecialchars($user['username']);
$isAdmin = (bool) $user['is_admin'];
$avatar = $user['avatar'] ?? '/assets/default-avatar.png'; // стандартный аватар

// Получаем отзывы пользователя
$reviewModel = new Reviews($conn);
$userReviews = $reviewModel->getReviewsByUserId($userId);

?>
<?php $titleName = 'Profile' ?>
<?php $titlePage = 'Личный кабинет' ?>
<?php require_once "./layout/header.php"; ?>
<?php require_once "./layout/nav.php"; ?>

<div class="profile-container">
    <div class="profile-header">
        <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets\img\avatar\default-avatar.png') ?>" alt="Аватар"
            class="avatar-img">
        <div>
            <h1 class="profile-title">Привет, <?= $username ?>!</h1>
            <form action="/pages/profile/upload_avatar.php" method="post" enctype="multipart/form-data"
                class="avatar-form">
                <label for="avatar">Сменить аватар:</label>
                <input type="file" name="avatar" accept="image/*" required>
                <button type="submit">Загрузить</button>
            </form>
        </div>
    </div>

    <?php if ($isAdmin): ?>
        <div class="profile-role admin">Вы — администратор и можете добавлять или редактировать фильмы.</div>
        <div class="profile-actions">
            <a href="/pages/movie/AddMovie">Добавить фильм</a>
            <a href="/pages/profile/Mymovies"> Мои фильмы</a>
            <a href="/pages/movie/editMovie"> Редактировать фильмы</a>
        </div>
    <?php else: ?>
        <div class="profile-role">Вы обычный пользователь и можете просматривать фильмы и оставлять отзывы.</div>
    <?php endif; ?>

    <div class="profile-links">
        <a href="/"> На главную</a>
        <a href="/logout"> Выйти</a>
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
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<style>
    .profile-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .avatar-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ddd;
    }

    .profile-title {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .avatar-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .avatar-form input[type="file"] {
        padding: 6px;
        background: #f1f1f1;
        border-radius: 6px;
    }

    .avatar-form button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .avatar-form button:hover {
        background-color: #45a049;
    }

    .profile-role {
        font-style: italic;
        margin-bottom: 12px;
    }

    .profile-role.admin {
        color: #d35400;
        font-weight: bold;
    }

    .profile-actions,
    .profile-links {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
        margin-bottom: 20px;
    }

    .profile-actions a,
    .profile-links a {
        padding: 8px 16px;
        background: #eee;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        transition: all 0.2s ease;
    }

    .profile-actions a:hover,
    .profile-links a:hover {
        background: #ccc;
    }

    h2 {
        margin-top: 40px;
    }

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
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
    }
</style>

<?php require_once "./layout/footer.php"; ?>