<?php
session_start();
require_once __DIR__ . "/../config/db.php";

$errors = [];

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errors['general'] = 'Пожалуйста, заполните все поля';
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];

            header('Location: /Profile?id=' . $user['id']);
            exit();
        } else {
            $errors['general'] = 'Неверный email или пароль';
        }
    }
}
?>
<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $msg): ?>
        <p style="color:red;"><?= htmlspecialchars($msg) ?></p>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Форма входа -->
<form action="/signin" method="post">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Sign In</button>
</form>