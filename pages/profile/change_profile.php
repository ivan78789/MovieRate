<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];

    if ($username === '') {
        echo "Имя не может быть пустым.";
        exit;
    }

    if ($password !== '' && $password !== $passwordConfirm) {
        echo "Пароли не совпадают.";
        exit;
    }

    if ($password !== '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $hashedPassword, $userId]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$username, $userId]);
    }

    header('Location: /Profile');
    exit;
}
