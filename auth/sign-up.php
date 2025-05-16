<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем и очищаем данные из формы
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Валидация
    if ($email === '' || $username === '' || $password === '') {
        $errors[] = 'Пожалуйста, заполните все поля.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Неверный формат email.';
    } elseif (mb_strlen($password, 'UTF-8') < 8) {
        $errors[] = 'Пароль должен содержать не менее 8 символов.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Подготавливаем и выполняем запрос
        $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
        try {
            $stmt->execute([
                ':email' => $email,
                ':username' => $username,
                ':password' => $hash
            ]);
            // Сохраняем ID пользователя в сессии и перенаправляем
            $_SESSION['user_id'] = $conn->lastInsertId();
            header('Location: /signin');
            exit();
        } catch (PDOException $e) {

        }
    }
}
?>
<form action="/signup" method="post">
    <input type="username" name="username" placeholder="Имя пользователя" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Зарегистрироваться</button>
</form>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li style="color:red;"><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>