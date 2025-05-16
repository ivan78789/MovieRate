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

<form action="/signin" method="post" class="auth-form">
    <h2 class="auth-title">Вход в аккаунт</h2>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" required>
    </div>
    <div class="form-group password-field">
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <button type="button" id="toggle-button">
            <img id="eye-icon" src="/assets/img/svg/closed-eye.svg" alt="Показать пароль">
        </button>
    </div>
    <button type="submit" name="login" class="btn-primary">Войти</button>
    <p class="auth-link">Нет аккаунта? <a href="/signup">Зарегистрироваться</a></p>
</form>