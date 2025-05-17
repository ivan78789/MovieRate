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
    <ul class="auth-errors">
        <?php foreach ($errors as $msg): ?>
            <li><?= htmlspecialchars($msg) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

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
            <img id="eye-icon" src="/assets/img/svg/eye.svg" alt="Показать пароль">
        </button>
    </div>
    <button type="submit" name="login" class="btn-primary">Войти</button>
    <p class="auth-link">Нет аккаунта? <a href="/auth/sign-up.php">Зарегистрироваться</a></p>
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

<style>
    .auth-form {
        max-width: 350px;
        margin: 40px auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        padding: 32px 28px 24px 28px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .auth-title {
        text-align: center;
        margin-bottom: 10px;
        font-size: 1.5rem;
        font-weight: 600;
        color: #222;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        position: relative;
    }

    .form-group label {
        font-size: 0.98rem;
        color: #444;
        margin-bottom: 2px;
    }

    .form-group input {
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 1rem;
        background: #f9fafb;
        transition: border 0.2s;
    }

    .form-group input:focus {
        border: 1.5px solid #4f8cff;
        outline: none;
        background: #fff;
    }

    .password-field button {
        position: absolute;
        right: 10px;
        top: 34px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
    }

    .password-field img {
        width: 22px;
        height: 22px;
    }

    .btn-primary {
        background: linear-gradient(90deg, #4f8cff 0%, #2356c7 100%);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 10px 0;
        font-size: 1.08rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 8px;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #2356c7 0%, #4f8cff 100%);
    }

    .auth-link {
        text-align: center;
        font-size: 0.98rem;
        margin-top: 8px;
    }

    .auth-link a {
        color: #4f8cff;
        text-decoration: none;
        font-weight: 500;
    }

    .auth-link a:hover {
        text-decoration: underline;
    }

    .auth-errors {
        max-width: 350px;
        margin: 10px auto 0 auto;
        padding: 0 18px;
        color: #d32f2f;
        font-size: 0.98rem;
        list-style: disc inside;
    }
</style>