<?php
require_once __DIR__ . '/../../../../config/db.php';
session_start();

// Проверка авторизации и прав администратора
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: /signin');
    exit;
}

// Проверка корректности ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Некорректный ID фильма.";
    exit;
}

$id = (int)$_GET['id']; // безопасное преобразование

// Получение фильма
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    echo "Фильм с указанным ID не найден.";
    exit;
}

?>
<h2>Редактировать фильм</h2>

<div class="edit-movie-container">
    <h2 class="edit-movie-title">Редактировать фильм</h2>
<form action="/editmovieApi" method="post" enctype="multipart/form-data" class="edit-movie-form">

        <input type="hidden" name="id" value="<?= $movie['id'] ?>">
        <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
        <textarea name="description" required><?= htmlspecialchars($movie['description']) ?></textarea>
        <input type="text" name="genre" value="<?= htmlspecialchars($movie['genre']) ?>" required>
        <input type="number" name="year" value="<?= $movie['year'] ?>" required>
        <?php if (!empty($movie['poster'])): ?>
            <p>Текущий постер:</p>
            <img src="<?= htmlspecialchars($movie['poster_path']) ?>" width="100" alt="Постер фильма">
        <?php else: ?>
            <p>Постер не загружен</p>
        <?php endif; ?>
        <input type="file" name="poster" accept="image/*">
        <button type="submit">Сохранить изменения</button>
    </form>
</div>
<style>
.edit-movie-container {
    max-width: 480px;
    margin: 48px auto 0 auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(56,189,248,0.08);
    padding: 36px 32px 28px 32px;
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.edit-movie-title {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: #2356c7;
    margin-bottom: 18px;
}
.edit-movie-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.edit-movie-form input[type="text"],
.edit-movie-form input[type="number"],
.edit-movie-form input[type="file"],
.edit-movie-form textarea {
    padding: 8px 10px;
    background: #f9fafb;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    font-size: 1rem;
    margin-top: 2px;
}
.edit-movie-form textarea {
    min-height: 70px;
    resize: vertical;
}
.edit-movie-form button {
    background-color: #38bdf8;
    color: white;
    border: none;
    padding: 10px 0;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 1.08rem;
    margin-top: 8px;
    transition: background 0.2s;
}
.edit-movie-form button:hover {
    background-color: #0ea5e9;
}
.edit-movie-form img {
    border-radius: 8px;
    margin: 8px 0;
    box-shadow: 0 2px 8px rgba(56,189,248,0.08);
}
@media (max-width: 600px) {
    .edit-movie-container {
        padding: 16px 6px 18px 6px;
    }
    .edit-movie-title {
        font-size: 1.1rem;
    }
}
</style>
