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

    public function create(array $data): bool
    {
        if (
            empty($data['user_id']) ||
            empty($data['movie_id']) ||
            empty($data['comment']) ||
            !isset($data['rating'])
        ) {
            return false;
        }

        $this->reviews->user_id = $data['user_id'];
        $this->reviews->movie_id = $data['movie_id'];
        $this->reviews->comment = $data['comment'];
        $this->reviews->rating = $data['rating'];

        return $this->reviews->create();
    }

    public function delete(int $id): bool
    {
        return $this->reviews->delete($id);
    }
    public function getById(int $id): ?array
    {
        return $this->reviews->getById($id);
    }

    public function delete(int $id): bool
    {
        return $this->reviews->delete($id);
    }

    public function update(int $id, int $rating, string $comment): bool
    {
        return $this->reviews->update($id, $rating, $comment);
    }

}
