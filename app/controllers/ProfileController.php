<?php
require_once __DIR__ . '/../models/PostModel.php';

class ProfileController {
    private $db;
    private $postModel;

    public function __construct($db) {
        $this->db = $db;
        $this->postModel = new PostModel($db);
    }

    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $userId = filter_var(
            $_GET['id'] ?? $_SESSION['user_id'],
            FILTER_VALIDATE_INT
        );

        if (!$userId || $userId < 1) {
            http_response_code(400);
            exit('Invalid profile.');
        }

        $stmt = $this->db->prepare(
            "SELECT id, username, full_name, bio, profile_image, created_at
             FROM users WHERE id = ?"
        );
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();

        if (!$profile) {
            http_response_code(404);
            exit('Profile not found.');
        }

        $error = '';
        $success = '';

        if ((int)$userId === (int)$_SESSION['user_id']
            && $_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? '';

            if ($action === 'update_profile') {
                $fullName = trim($_POST['full_name'] ?? '');
                $bio = trim($_POST['bio'] ?? '');

                if ($fullName === '' || strlen($fullName) > 100) {
                    $error = 'Name is required and must be under 100 characters.';
                } elseif (strlen($bio) > 500) {
                    $error = 'Bio must be 500 characters or less.';
                } else {
                    $stmt = $this->db->prepare(
                        "UPDATE users SET full_name = ?, bio = ? WHERE id = ?"
                    );
                    $stmt->execute([$fullName, $bio, $userId]);
                    $success = 'Profile updated!';
                }
            }

            if ($action === 'upload_pfp') {
                if (!isset($_FILES['profile_image'])
                    || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Choose an image to upload.';
                } else {
                    $file = $_FILES['profile_image'];
                    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

                    $allowed = [
                        'image/jpeg' => 'jpg',
                        'image/png'  => 'png',
                        'image/webp' => 'webp'
                    ];

                    if ($file['size'] > 3 * 1024 * 1024) {
                        $error = 'Profile picture must be 3 MB or less.';
                    } elseif (!isset($allowed[$mime])) {
                        $error = 'Use a JPG, PNG, or WebP image.';
                    } else {
                        $folder = __DIR__ . '/../../public/uploads/profiles/';

                        if (!is_dir($folder)) {
                            mkdir($folder, 0755, true);
                        }

                        $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];

                        if (move_uploaded_file($file['tmp_name'], $folder . $filename)) {
                            $path = 'uploads/profiles/' . $filename;

                            $stmt = $this->db->prepare(
                                "UPDATE users SET profile_image = ? WHERE id = ?"
                            );
                            $stmt->execute([$path, $userId]);
                            $success = 'Profile picture updated!';
                        } else {
                            $error = 'Could not save the image.';
                        }
                    }
                }
            }

            $stmt = $this->db->prepare(
                "SELECT id, username, full_name, bio, profile_image, created_at
                 FROM users WHERE id = ?"
            );
            $stmt->execute([$userId]);
            $profile = $stmt->fetch();
        }

        $stmt = $this->db->prepare(
            "SELECT * FROM posts WHERE user_id = ?
             ORDER BY created_at DESC, id DESC"
        );
        $stmt->execute([$userId]);
        $posts = $stmt->fetchAll();

        // Pointing to profile.php instead of pfp.php
        require __DIR__ . '/../views/profile.php';
    }
}