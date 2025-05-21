<?php

namespace App\Models;

use PDO;

class Reviews
{
    private $conn;

    public $id;
    public $user_id;
    public $movie_id;
    public $comment;
    public $rating;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function getConnection()
    {
        return $this->conn;
    }
    // метод для получения всех общей оценки
    public function getAverageRatingByMovieId($movieId)
    {
        $stmt = $this->conn->prepare("SELECT AVG(rating) as average_rating FROM reviews WHERE movie_id = :movie_id");
        $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average_rating'] !== null ? round($result['average_rating'], 1) : null;
    }


    public function getAll()
    {
        $sql = "SELECT * FROM reviews";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM reviews WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }
    public function hasUserReviewedMovie($userId, $movieId): bool
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND movie_id = :movie_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':movie_id', $movieId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    public function addReview($userId, $movieId, $rating, $comment): array
    {
        $errors = [];

        if ($rating < 1 || $rating > 10) {
            $errors['rating'] = 'Оценка должна быть от 1 до 10.';
        }

        if (empty($comment)) {
            $errors['comment'] = 'Комментарий не может быть пустым.';
        }

        if ($this->hasUserReviewedMovie($userId, $movieId)) {

            $errors['general'] = 'Вы уже оставляли отзыв для этого фильма.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $conn = $this->getConnection();
            $stmt = $conn->prepare("
            INSERT INTO reviews (movie_id, user_id, rating, comment, created_at)
            VALUES (:movie_id, :user_id, :rating, :comment, NOW())
        ");
            $stmt->bindParam(':movie_id', $movieId, \PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, \PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, \PDO::PARAM_STR);
            $stmt->execute();

            return ['success' => true];
        } catch (\PDOException $e) {
            return ['success' => false, 'errors' => ['general' => 'Ошибка при добавлении отзыва. Попробуйте позже.']];
        }
    }
    // В Reviews.php (модель)
    public function deleteById($id, $userId): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM reviews WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateById($id, $userId, $comment, $rating): bool
    {
        $stmt = $this->conn->prepare("
        UPDATE reviews 
        SET comment = :comment, rating = :rating 
        WHERE id = :id AND user_id = :user_id
    ");
        $stmt->bindParam(':comment', $comment, \PDO::PARAM_STR);
        $stmt->bindParam(':rating', $rating, \PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function getReviewsByUserId($userId): array
    {
        $stmt = $this->conn->prepare("
        SELECT r.*, m.title AS movie_title 
        FROM reviews r
        JOIN movies m ON r.movie_id = m.id
        WHERE r.user_id = :user_id
        ORDER BY r.created_at DESC
    ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

}

