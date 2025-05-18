<?php
session_start();
// еслт пльзователь вошел объявляем будевое значени тру или фолс 
function isLoggedIn(): bool
{
    //  проверяет, существует ли ключ 'user_id' в сессии.
    return isset($_SESSION['user_id']);
}
// требуем авторизацию
function requireAuth(): void
{
    // если зараха не вошла то перенаправляем на страницу входа
    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }
}