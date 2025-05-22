<?php
session_start();

// Подключение к БД
require_once __DIR__ . '/../../../../config/db.php';

// Проверяем, авторизован ли пользователь и является ли он админом
// Предполагаем, что $user нужно получить из базы данных
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $stmt = $conn->prepare("SELECT id, is_admin FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_admin'] = (int)$user['is_admin'];
    }
}

// Проверка авторизации
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header('Location: /');
    exit;
}

// Логика обработки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $year = $_POST['year'] ?? '';
    $genre = $_POST['genre'] ?? '';

    // Обработка постера
    $posterPath = null;
    if (!empty($_FILES['poster']['tmp_name'])) {
        $uploadDir = '/uploads/posters/';
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $fileName = basename($_FILES['poster']['name']);
        $targetFile = $uploadPath . $fileName;

        if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
            $posterPath = $uploadDir . $fileName;
        }
    }

    // Добавление в базу
    $stmt = $conn->prepare("INSERT INTO movies (title, description, year, genre, poster_path, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $year, $genre, $posterPath, $_SESSION['user_id']]);

    header('Location: /addmovie');
    exit;
}
?>


<div class="add-movie-container">
    <h2 class="add-movie-title">Добавить фильм</h2>
<div class="link_add-movie">   
     <a href="/" class="add-movie-back">Вернуться на главную</a>
    <span>или</span>
    <a href="/Profile" class="add-movie-back"> В профиль</a></div>
    <form action="/addmovie" method="post" enctype="multipart/form-data" class="add-movie-form">
        <label>Название:
            <input type="text" name="title" required>
        </label>
        <label>Описание:
            <textarea name="description" required></textarea>
        </label>
        <label>Год:
            <input type="number" name="year" required>
        </label>
        <label>Жанр:
            <input type="text" name="genre" required>
        </label>
        <label>Постер:
            <input type="file" name="poster">
        </label>
        <button type="submit">Добавить</button>
    </form>
</div>


<style>
    .link_add-movie{
        display: flex;
        flex-direction: row;
        gap: 5px;
    }
.add-movie-container {
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
.add-movie-title {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: #2356c7;
    margin-bottom: 18px;
}
.add-movie-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.add-movie-form label {
    font-size: 1rem;
    color: #2356c7;
    font-weight: 500;
    margin-bottom: 4px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.add-movie-form input[type="text"],
.add-movie-form input[type="number"],
.add-movie-form input[type="file"],
.add-movie-form textarea {
    padding: 8px 10px;
    background: #f9fafb;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    font-size: 1rem;
    margin-top: 2px;
}
.add-movie-form textarea {
    min-height: 70px;
    resize: vertical;
}
.add-movie-form button {
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
.add-movie-form button:hover {
    background-color: #0ea5e9;
}
.add-movie-back {
    display: inline-block;
    margin-bottom: 18px;
    color: #2356c7;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}
.add-movie-back:hover {
    color: #38bdf8;
    text-decoration: underline;
}
@media (max-width: 600px) {
    .add-movie-container {
        padding: 16px 6px 18px 6px;
    }
    .add-movie-title {
        font-size: 1.1rem;
    }
}
</style>