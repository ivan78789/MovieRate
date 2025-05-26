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
$query = "SELECT username, email, is_admin, avatar FROM users WHERE id = ?";
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


<div class="profile-main">
    <div class="profile-card">
        <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets/img/avatar/default-avatar.png') ?>" alt="Аватар" class="avatar-img">

        <div class="profile-info">
            <h1 class="profile-title">Привет, <?= $username ?>!</h1>
            <div class="profile-email"><span>Email:</span> <?= htmlspecialchars($user['email'] ?? '—') ?></div>


            <form action="/UploadAvatar" method="post" enctype="multipart/form-data" class="avatar-form">
                <form action="/UploadAvatar" method="post" enctype="multipart/form-data">
    <label for="avatar">Выберите аватар:</label>
    <input type="file" id="avatar" name="avatar" accept="image/*" autocomplete="off" required>
    <button type="submit">Загрузить</button>
</form>
                <!-- <input type="file" name="avatar" accept="image/*" required>
                <button type="submit">Загрузить</button>
            </form> -->
            <form action="/ChangeProfile" method="post" class="profile-edit-form">
 
                <label for="username">Имя:</label>
                <input type="text" name="username" id="username" value="<?= $username ?>" required>
                <label for="password">Новый пароль:</label>
                <input type="password" name="password" id="password" placeholder="••••••••">

                <label for="password">Подтвердите пароль:</label>
                <input type="password" name="password_confirm" id="password_confirm" placeholder="••••••••">
                <button type="submit">Сохранить изменения</button>
            </form>
        </div>
    </div>
    <div class="profile-actions-block">
        <?php if ($isAdmin): ?>
            <div class="profile-role admin">Вы — администратор</div>
            <div class="profile-actions">
                <a href="/addmovie">Добавить фильм</a>
                <a href="/viewmovie">Все фильмы</a>
                <a href="/myfilms">Мои фильмы</a>
            </div>
        <?php else: ?>
            <div class="profile-role">Обычный пользователь</div>
        <?php endif; ?>
        <div class="profile-links">
            <a href="/">На главную</a>
            <a href="/logout">Выйти</a>
        </div>
    </div>
</div>

<h2 class="profile-history-title">История моих отзывов</h2>
<?php if (empty($userReviews)): ?>
    <p class="profile-history-empty">Вы ещё не оставили ни одного отзыва.</p>
<?php else: ?>
    <ul class="user-reviews">
        <?php foreach ($userReviews as $review): ?>
            <li>
                <div class="review-movie"><b><?= htmlspecialchars($review['movie_title']) ?></b></div>
                <div class="review-meta">
                    <span class="review-rating">Оценка: <b><?= $review['rating'] ?>/10</b></span>
                    <span class="review-date"><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></span>
                </div>
                <div class="review-comment">"<?= htmlspecialchars($review['comment']) ?>"</div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>


<?php require_once "./layout/footer.php"; ?>