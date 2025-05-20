<?php
namespace App\Controllers;

use App\Models\Reviews;

class ReviewsController
{
    private Reviews $reviews;

    public function __construct(Reviews $reviews)
    {
        $this->reviews = $reviews;
    }

    public function getAllReviews(): array
    {
        return $this->reviews->getAll();
    }

    public function getByMovieId($movieId)
    {
        $conn = $this->reviews->getConnection();
        $stmt = $conn->prepare("
        SELECT reviews.*, users.username 
        FROM reviews 
        JOIN users ON reviews.user_id = users.id 
        WHERE reviews.movie_id = :movie_id 
        ORDER BY reviews.created_at DESC
    ");
        $stmt->bindParam(':movie_id', $movieId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // для общего рейтинга
    public function getAverageRating($movieId)
    {
        return $this->reviews->getAverageRatingByMovieId($movieId);
    }
    public function getById($id)
    {
        return $this->reviews->getById($id);
    }

    // В ReviewsController.php
    public function delete($id, $userId): bool
    {
        return $this->reviews->deleteById($id, $userId);
    }

    public function update($id, $userId, $comment, $rating): bool
    {
        return $this->reviews->updateById($id, $userId, $comment, $rating);
    }

}
