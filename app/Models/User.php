<?php
namespace App\Models;

class User
{
    private $conn;
    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create($email, $username, $hash)
    {
        $sql = "INSERT INTO users (email, username, password) VALUES (:email, :username, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hash);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
