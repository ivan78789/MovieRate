<?php
session_start();
require_once __DIR__ . '/../../../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /signin');
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT * FROM movies WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$userId]);
$myMovies = $stmt->fetchAll();

?>

<div>
    <h2>Мои фильмы</h2>

    <?php if (count($myMovies) === 0): ?>
        <p>Фильмы не найдены.</p>
    <?php else: ?>
        <?php foreach ($myMovies as $movie): ?>
            <div>
                <h3><?= htmlspecialchars($movie['title']) ?></h3>
                <p><?= htmlspecialchars($movie['description']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
