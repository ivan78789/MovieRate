<?php
session_start();

require_once __DIR__ . '/../../../../config/db.php';

// Проверяем, авторизован ли пользователь и является ли он админом
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

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header('Location: /');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $genre = trim($_POST['genre'] ?? '');

    // Валидация
    if ($title === '') {
        $errors[] = "Название обязательно.";
    }
    if ($description === '') {
        $errors[] = "Описание обязательно.";
    }
    if ($genre === '') {
        $errors[] = "Жанр обязателен.";
    }

    // Приводим год к int и проверяем диапазон
    $year = (int)$year;
    $currentYear = (int)date('Y');
    if ($year < 1900 || $year > $currentYear + 1) {
        $errors[] = "Введите корректный год от 1900 до " . ($currentYear + 1);
    }

    // Обработка постера
    $posterPath = null;
    if (!empty($_FILES['poster']['tmp_name'])) {
        $uploadDir = '/uploads/posters/';
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Для уникальности имени файла можно добавить время или уникальный ID
        $fileExt = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('poster_', true) . '.' . $fileExt;
        $targetFile = $uploadPath . $fileName;

        if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
            $posterPath = $uploadDir . $fileName;
        } else {
            $errors[] = "Ошибка загрузки файла постера.";
        }
    }

    if (empty($errors)) {
$stmt = $conn->prepare("INSERT INTO movies (title, description, year, genre, poster_path, user_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$title, $description, $year, $genre, $posterPath, $_SESSION['user_id']]);


        header('Location: /addmovie');
        exit;
    }
}
?>

<!-- Вывод ошибок, если есть -->
<?php if (!empty($errors)): ?>
    <div style="color: red; margin-bottom: 16px;">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="add-movie-container">
    <h2 class="add-movie-title">Добавить фильм</h2>
    <div class="link_add-movie">   
        <a href="/" class="add-movie-back">Вернуться на главную</a>
        <span>или</span>
        <a href="/Profile" class="add-movie-back"> В профиль</a>
    </div>
    <form action="/addmovie" method="post" enctype="multipart/form-data" class="add-movie-form">
        <label>Название:
            <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
        </label>
        <label>Описание:
            <textarea name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </label>
        <label>Год:
            <input type="number" name="year" min="1900" max="<?= date('Y') + 1 ?>" value="<?= htmlspecialchars($_POST['year'] ?? '') ?>" required>
        </label>
        <label>Жанр:
            <input type="text" name="genre" value="<?= htmlspecialchars($_POST['genre'] ?? '') ?>" required>
        </label>
        <label>Постер:
            <input type="file" name="poster" accept="image/*">
        </label>
        <button type="submit">Добавить</button>
    </form>
</div>

<!-- CSS стили ты можешь оставить без изменений -->
<style>
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
.link_add-movie {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-bottom: 18px;
}
.add-movie-back {
  color: #38bdf8;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
  border-radius: 6px;
  padding: 6px 12px;
}
.add-movie-back:hover {
  color: #2356c7;
  background: #f3f6fa;
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
  background: linear-gradient(90deg, #4f8cff 0%, #2356c7 100%);
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
  background: linear-gradient(90deg, #2356c7 0%, #4f8cff 100%);
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