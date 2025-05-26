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
<?php $titleName = 'Редактирование'?>

<?php require_once "./layout/header.php"; ?>
<?php require_once __DIR__ . '/../../../../layout/nav.php'?>

<a class="back " href="/Profile">Назад</a>

<div class="edit-movie-container">
    <h2 class="edit-movie-title">Редактировать фильм</h2>
    <form action="/editmovieApi" method="post" enctype="multipart/form-data" class="edit-movie-form">
        <input type="hidden" name="id" value="<?= $movie['id'] ?>">
        <label>
            Название:
            <input type="text" name="title" value="<?= htmlspecialchars($movie['title']) ?>" required>
        </label>
        <label>
            Описание:
            <textarea name="description" required><?= htmlspecialchars($movie['description']) ?></textarea>
        </label>
        <label>
            Жанр:
            <input type="text" name="genre" value="<?= htmlspecialchars($movie['genre']) ?>" required>
        </label>
        <label>
            Год:
            <input type="number" name="year" value="<?= $movie['year'] ?>" required>
        </label>

        <?php if (!empty($movie['poster_path'])): ?>
            <p>Текущий постер:</p>
            <img src="<?= htmlspecialchars($movie['poster_path']) ?>" width="100" alt="Постер фильма">
        <?php endif; ?>

        <label>
            Новый постер:
            <input type="file" name="poster" accept="image/*">
        </label>

        <button type="submit">Сохранить изменения</button>
    </form>
</div>


<?php require_once "./layout/footer.php"; ?>
