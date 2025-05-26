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
            $_SESSION['is_admin'] = $user['is_admin']; 
            header('Location: /Profile?id=' . $user['id']);
            exit();
        } else {
            $errors['general'] = 'Неверный email или пароль';
        }
    }
}
?>
<?php if (!empty($errors)): ?>
    <ul class="auth-errors">
        <?php foreach ($errors as $msg): ?>
            <li><?= htmlspecialchars($msg) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<?php $titleName = 'Вход'?>
<?php require_once __DIR__ . '/../layout/header.php'?>

<form action="/signin" method="post" class="auth-form">
    <h2 class="auth-title">Вход в аккаунт</h2>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" required>
    </div>
    <div class="form-group password-field">
        <label for="password">Пароль</label>
        <input type="password" name="password" id="password" placeholder="Пароль" required>
        <button type="button" id="toggle-button" tabindex="-1">
            <img id="eye-icon" src="/assets/img/svg/closed-eye.svg" alt="Показать пароль">
        </button>
    </div>
    <button type="submit" name="login" class="btn-primary">Войти</button>
    <p class="auth-link">Нет аккаунта? <a href="/signup">Зарегистрироваться</a></p>
</form>

<script>
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggle-button');
    const eyeIcon = document.getElementById('eye-icon');
    let isVisible = false;

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            isVisible = !isVisible;
            passwordInput.type = isVisible ? 'text' : 'password';
            eyeIcon.src = isVisible ? '/assets/img/svg/eye.svg' : '/assets/img/svg/closed-eye.svg';
            eyeIcon.alt = isVisible ? 'Скрыть пароль' : 'Показать пароль';
        });
    }

</script>
