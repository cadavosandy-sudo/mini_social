
<?php

require_once __DIR__ . '/../models/UserModel.php';

class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new UserModel($db);
    }

    public function register() {
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (
                $username === '' ||
                $fullName === '' ||
                $password === ''
            ) {
                $error = "Please fill in all required fields.";
            } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
                $error = "Username must be 3-50 characters using letters, numbers, or underscores.";
            } elseif (strlen($password) < 8) {
                $error = "Password must be at least 8 characters.";
            } elseif ($password !== $confirmPassword) {
                $error = "Passwords do not match.";
            } elseif ($this->userModel->findByUsername($username)) {
                $error = "Username is already taken.";
            } else {
                try {
                    $this->userModel->register(
                        $username,
                        $password,
                        $fullName
                    );

                    header('Location: index.php?page=login&registered=1');
                    exit;
                } catch (PDOException $e) {
                    $error = "Registration failed. Please try again.";
                }
            }
        }

        require __DIR__ . '/../views/register.php';
    }

    public function login() {
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header('Location: index.php?page=feed');
                exit;
            }

            $error = "Invalid username or password.";
        }

        require __DIR__ . '/../views/login.php';
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();

        header('Location: index.php?page=login');
        exit;
    }
}
