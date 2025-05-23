<?php 
session_start();
require_once __DIR__ . '/../../../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$userId = $_SESSION['user_id'];
$isAdmin = $_SESSION['is_admin'] ?? 0;

// Проверка ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    die("ID фильма не передан или некорректен.");
}

// Получаем фильм
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    die("Фильм не найден.");
}

// Проверка прав: либо админ, либо автор
if ($movie['user_id'] != $userId && !$isAdmin) {
    die("У вас нет прав на редактирование этого фильма.");
}
?>

<!-- HTML и CSS как у тебя — всё ок -->

<div class="edit-movie-container">
    <h2 class="edit-movie-title">Редактировать фильм</h2>
    <form action="/editmovieApi" method="post" enctype="multipart/form-data" class="edit-movie-form">
        <input type="hidden" name="id" value="<?= $movie['id'] ?>">
        <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
        <textarea name="description" required><?= htmlspecialchars($movie['description']) ?></textarea>
        <input type="text" name="genre" value="<?= htmlspecialchars($movie['genre']) ?>" required>
        <input type="number" name="year" value="<?= $movie['year'] ?>" required>
        <?php if (!empty($movie['poster_path'])): ?>
            <p>Текущий постер:</p>
            <img src="<?= htmlspecialchars($movie['poster_path']) ?>" width="100" alt="Постер фильма">
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