<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$userId = $_SESSION['user_id'];

// Проверка, загружен ли файл
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['avatar']['tmp_name'];
    $fileName = $_FILES['avatar']['name'];
    $fileSize = $_FILES['avatar']['size'];
    $fileType = $_FILES['avatar']['type'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Разрешённые типы
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExtension, $allowedExtensions)) {
        die("Разрешены только изображения: jpg, jpeg, png, gif.");
    }

    // Уникальное имя файла
    $newFileName = uniqid('avatar_', true) . '.' . $fileExtension;

    // Папка для загрузки
    $uploadDir = __DIR__ . '/../../uploads/avatars/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $destPath = $uploadDir . $newFileName;
    $dbPath = '/uploads/avatars/' . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // Обновим путь в базе данных
        $query = "UPDATE users SET avatar = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$dbPath, $userId]);

        header("Location: /Profile");
        exit;
    } else {
        echo "Ошибка при сохранении файла.";
    }
} else {
    echo "Файл не загружен или есть ошибка.";
}
