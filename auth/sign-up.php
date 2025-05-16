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
<?php if (!empty($errors)): ?>
    <ul class="auth-errors">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form action="/signup" method="post" class="auth-form">
    <h2 class="auth-title">Регистрация</h2>
    <div class="form-group">
        <label for="username">Имя пользователя</label>
        <input type="text" name="username" id="username" placeholder="Имя пользователя" required>
    </div>
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
    <button type="submit" class="btn-primary">Зарегистрироваться</button>
    <p class="auth-link">Уже есть аккаунт? <a href="/auth/sign-in.php">Войти</a></p>
</form>

<script>
    // Так как дублируются стили и жс будет это все здесь
    (function () {
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('toggle-button');
        const eyeIcon = document.getElementById('eye-icon');
        let isVisible = false;

        if (toggleBtn && passwordInput && eyeIcon) {
            toggleBtn.addEventListener('click', function (e) {
                e.preventDefault();
                isVisible = !isVisible;
                passwordInput.type = isVisible ? 'text' : 'password';
                eyeIcon.src = isVisible ? '/assets/img/svg/eye.svg' : ' /assets/img/svg/closed-eye.svg';
                eyeIcon.alt = isVisible ? 'Скрыть пароль' : 'Показать пароль';
            });
        }
    })();
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