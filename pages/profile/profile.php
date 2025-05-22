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


<div class="profile-main">
    <div class="profile-card">
        <img src="<?= htmlspecialchars($user['avatar'] ?? 'assets/img/avatar/default-avatar.png') ?>" alt="Аватар" class="avatar-img">
        <div class="profile-info">
            <h1 class="profile-title">Привет, <?= $username ?>!</h1>
            <div class="profile-email"><span>Email:</span> <b><?= htmlspecialchars($users['email'] ?? '—') ?></b></div>
            <form action="/UploadAvatar" method="post" enctype="multipart/form-data" class="avatar-form">
                <label for="avatar">Сменить аватар:</label>
                <input type="file" name="avatar" accept="image/*" required>
                <button type="submit">Загрузить</button>
            </form>
            <form action="/ChangeProfile" method="post" class="profile-edit-form">
                <label for="username">Имя:</label>
                <input type="text" name="username" id="username" value="<?= $username ?>" required>
                <label for="password">Новый пароль:</label>
                <input type="password" name="password" id="password" placeholder="••••••••">
                <label for="password">Подтвердите пароль:</label>
                <input type="password" name="password" id="password" placeholder="••••••••">
                <button type="submit">Сохранить изменения</button>
            </form>
        </div>
    </div>
    <div class="profile-actions-block">
        <?php if ($isAdmin): ?>
            <div class="profile-role admin">Вы — администратор</div>
            <div class="profile-actions">
                <a href="/addmovie">Добавить фильм</a>
                <a href="/viewmovie">Мои фильмы</a>
                <a href="/editmovie">Редактировать фильмы</a>
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
<style>.profile-main {
    display: flex;
    flex-wrap: wrap;
    gap: 32px;
    align-items: flex-start;
    margin: 40px auto 0 auto;
    max-width: 900px;
}
.profile-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(56,189,248,0.08);
    padding: 32px 28px;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 270px;
    max-width: 320px;
    flex: 1 1 270px;
    gap: 18px;
}
.avatar-img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #38bdf8;
    background: #f3f6fa;
    margin-bottom: 10px;
}
.profile-info {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
}
.profile-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2356c7;
    margin-bottom: 2px;
}
.profile-email {
    font-size: 1rem;
    color: #444;
    margin-bottom: 6px;
}
.profile-email span {
    color: #888;
    font-weight: 500;
}
.avatar-form, .profile-edit-form {
    display: flex;
    flex-direction: column;
    gap: 7px;
    width: 100%;
}
.avatar-form label, .profile-edit-form label {
    font-size: 0.98rem;
    color: #2356c7;
    font-weight: 500;
}
.avatar-form input[type="file"], .profile-edit-form input[type="text"], .profile-edit-form input[type="password"] {
    padding: 7px 10px;
    background: #f1f1f1;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    font-size: 0.98rem;
}
.avatar-form button, .profile-edit-form button {
    background-color: #38bdf8;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s;
    margin-top: 4px;
}
.avatar-form button:hover, .profile-edit-form button:hover {
    background-color: #0ea5e9;
}
.profile-actions-block {
    flex: 2 1 320px;
    min-width: 220px;
    display: flex;
    flex-direction: column;
    gap: 18px;
    background: #f9fafb;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(56,189,248,0.06);
    padding: 28px 22px;
    align-items: flex-start;
}
.profile-role {
    font-style: italic;
    color: #4f8cff;
    font-size: 1.05rem;
    margin-bottom: 8px;
}
.profile-role.admin {
    color: #d35400;
    font-weight: bold;
}
.profile-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}
.profile-actions a {
    padding: 10px 0;
    background: linear-gradient(90deg, #4f8cff 0%, #2356c7 100%);
    border-radius: 8px;
    text-decoration: none;
    color: #fff;
    font-weight: 500;
    font-size: 1.05rem;
    text-align: center;
    transition: background 0.2s, color 0.2s;
}
.profile-actions a:hover {
    background: linear-gradient(90deg, #2356c7 0%, #4f8cff 100%);
    color: #fff;
    text-decoration: underline;
}
.profile-links {
    display: flex;
    gap: 10px;
    margin-top: 10px;
    width: 100%;
}
.profile-links a {
    flex: 1 1 0;
    padding: 8px 0;
    background: #eee;
    border-radius: 8px;
    text-decoration: none;
    color: #2356c7;
    font-weight: 500;
    text-align: center;
    transition: background 0.2s, color 0.2s;
}
.profile-links a:hover {
    background: #4f8cff;
    color: #fff;
}
.profile-history-title {
    margin-top: 48px;
    font-size: 1.25rem;
    color: #2356c7;
    text-align: left;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}
.profile-history-empty {
    color: #888;
    margin: 18px auto 0 auto;
    max-width: 900px;
    text-align: left;
}
.user-reviews {
    list-style: none;
    padding: 0;
    margin: 18px auto 0 auto;
    max-width: 900px;
}
.user-reviews li {
    background: #f8f8f8;
    padding: 15px 18px;
    margin-bottom: 12px;
    border-radius: 10px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
    font-size: 1rem;
    color: #222;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.review-movie {
    font-size: 1.08rem;
    color: #2356c7;
    font-weight: 600;
}
.review-meta {
    display: flex;
    gap: 18px;
    font-size: 0.98rem;
    color: #888;
}
.review-rating {
    color: #facc15;
    font-weight: 600;
}
.review-date {
    color: #b0b0b0;
}
.review-comment {
    color: #222;
    font-style: italic;
    margin-top: 2px;
}
@media (max-width: 900px) {
    .profile-main {
        flex-direction: column;
        gap: 18px;
        align-items: stretch;
    }
    .profile-card, .profile-actions-block {
        max-width: 100%;
        min-width: 0;
        width: 100%;
    }
}
@media (max-width: 600px) {
    .profile-card, .profile-actions-block {
        padding: 16px 8px;
    }
    .profile-title {
        font-size: 1.05rem;
    }
    .avatar-img {
        width: 70px;
        height: 70px;
    }
}
</style>

<?php require_once "./layout/footer.php"; ?>