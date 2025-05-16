<?php
namespace App\Controllers;
use App\Models\User;

class AuthController
{

    private $userModel;
    public function __construct($conn)
    {
        $this->userModel = new User($conn);
    }

    public function signup($data)
    {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        try {
            $userId = $this->userModel->create($data['email'], $data['username'], $hash);
            header('Location: /signin');
            exit();
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors['general'] = 'Email уже занят.';
            }
        }
    }

    public function signin($data)
    {
        $user = $this->userModel->findByEmail($data['email']);
        if ($user && password_verify($data['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: /profile.php?id=' . $user['id']);
            exit();
        } else {
            $errors['general'] = 'Неверные учётные данные.';
        }
    }
}
?>