<?php
namespace App\Controllers;
use App\Models\Movie;
class MovieController
{
    private Movie $movie;

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
    }

    public function getAllMovies(): array
    {
        return $this->movie->getAll();
    }

    public function getById(int $id): array
    {
        return $this->movie->getById($id);
    }

    public function create(array $data): bool
    {
        $this->movie->title = $data['title'];
        $this->movie->genre = $data['genre'];
        $this->movie->year = $data['year'];
        $this->movie->description = $data['description'];
        $this->movie->poster_path = $data['poster_path'] ?? null;
        $this->movie->created_by = $data['created_by'];

        return $this->movie->create();
    }

    public function delete(int $id): bool
    {
        return $this->movie->delete($id);
    }
}