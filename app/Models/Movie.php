<?php

namespace App\Models;

use PDO;

class Movie
{
    private $conn;

    public $id;
    public $title;
    public $genre;
    public $year;
    public $description;
    public $poster_path;
    public $created_by;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM movies";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM movies WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function create()
    {
        $stmt = $this->conn->prepare("INSERT INTO movies (title, genre, year, description, poster_path, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([$this->title, $this->genre, $this->year, $this->description, $this->poster_path, $this->created_by]);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM movies WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function update($id)
    {
        $stmt = $this->conn->prepare("UPDATE movies SET title = ?, genre = ?, year = ?, description = ?, poster_path = ? WHERE id = ?");
        return $stmt->execute([$this->title, $this->genre, $this->year, $this->description, $this->poster_path, $id]);
    }
public function getByGenre($genre)
{
    $stmt = $this->conn->prepare("SELECT * FROM movies WHERE genre = ? ORDER BY created_at DESC");
    $stmt->execute([$genre]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}

