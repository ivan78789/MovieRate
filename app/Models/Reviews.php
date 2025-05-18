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
        return round($result['average_rating'], 1);
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

    public function create()
    {
        $stmt = $this->conn->prepare("INSERT INTO reviews (user_id, movie_id, comment, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$this->user_id, $this->movie_id, $this->comment, $this->rating]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function update($id)
    {
        $stmt = $this->conn->prepare("UPDATE reviews SET comment = ?, rating = ? WHERE id = ?");
        return $stmt->execute([$this->comment, $this->rating, $id]);
    }
    // public function getByMovieI показывает отзывы к конкретному фильму 'все'
    public function getByMovieId(int $movieId): array
    {

        $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE movie_id = ?");
        $stmt->execute([$movieId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function update(int $id, int $rating, string $comment): bool
    {
        $stmt = $this->conn->prepare("UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);
        return $stmt->execute();
    }

}

