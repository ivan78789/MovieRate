<?php
require_once __DIR__ . '/../../config/auth.php';

requireAuth(); // Не пускаем неавторизованных

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<h2>Личный кабинет</h2>
<p>Привет, <?= htmlspecialchars($username) ?>!</p>
<a href="/">На Главный</a>
<a href="/logout">Выйти</a>