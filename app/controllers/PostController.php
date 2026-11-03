<?php
require_once __DIR__ . '/../models/PostModel.php';

class PostController {
    private $postModel;

    public function __construct($db) {
        $this->postModel = new PostModel($db);
    }

    public function feed() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $postId = filter_var($_POST['post_id'] ?? '', FILTER_VALIDATE_INT);

            // Handle Like action
            if ($action === 'like') {
                if ($postId && $postId > 0) {
                    $this->postModel->toggleLike($postId, $userId);
                }
                header('Location: index.php?page=feed#post-' . $postId);
                exit;
            }

            // Handle Comment action
            if ($action === 'comment') {
                $commentText = trim($_POST['comment_text'] ?? '');
                if ($postId && $postId > 0 && $commentText !== '') {
                    $this->postModel->addComment($postId, $userId, $commentText);
                }
                header('Location: index.php?page=feed#post-' . $postId);
                exit;
            }

            if ($action === 'create') {
                $content = trim($_POST['content'] ?? '');
                $filePath = null;

                if (strlen($content) > 2000) {
                    $error = 'Post must be 2000 characters or less.';
                } elseif ($content === '' &&
                    (!isset($_FILES['post_file']) ||
                     $_FILES['post_file']['error'] === UPLOAD_ERR_NO_FILE)) {
                    $error = 'Write something or choose a file.';
                } else {
                    if (isset($_FILES['post_file']) &&
                        $_FILES['post_file']['error'] !== UPLOAD_ERR_NO_FILE) {

                        $file = $_FILES['post_file'];
                        $allowed = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/gif' => 'gif',
                            'image/webp' => 'webp',
                            'application/pdf' => 'pdf',
                            'text/plain' => 'txt'
                        ];

                        if ($file['error'] !== UPLOAD_ERR_OK) {
                            $error = 'File upload failed.';
                        } elseif ($file['size'] > 5 * 1024 * 1024) {
                            $error = 'Maximum file size is 5 MB.';
                        } else {
                            $mime = (new finfo(FILEINFO_MIME_TYPE))
                                ->file($file['tmp_name']);

                            if (!isset($allowed[$mime])) {
                                $error = 'File type not allowed.';
                            } else {
                                $name = bin2hex(random_bytes(16))
                                    . '.' . $allowed[$mime];

                                $folder = __DIR__
                                    . '/../../public/uploads/posts/';

                                if (!is_dir($folder)) {
                                    mkdir($folder, 0755, true);
                                }

                                if (move_uploaded_file(
                                    $file['tmp_name'], $folder . $name
                                )) {
                                    $filePath = 'uploads/posts/' . $name;
                                } else {
                                    $error = 'Could not save the file.';
                                }
                            }
                        }
                    }

                    if ($error === '') {
                        $this->postModel->create(
                            $_SESSION['user_id'], $content, $filePath
                        );

                        header('Location: index.php?page=feed');
                        exit;
                    }
                }
            }

            if ($action === 'delete') {
                if ($postId && $postId > 0) {
                    $this->postModel->delete(
                        $postId, $_SESSION['user_id']
                    );
                }

                header('Location: index.php?page=feed');
                exit;
            }
        }

        $posts = $this->postModel->getAll();

        // Attach likes and comments data to each post for the view
        foreach ($posts as &$post) {
            $post['likes_count'] = $this->postModel->getLikeCount($post['id']);
            $post['is_liked'] = $this->postModel->isLikedByUser($post['id'], $userId);
            $post['comments'] = $this->postModel->getComments($post['id']);
        }
        unset($post);

        require __DIR__ . '/../views/newsfeed.php';
    }
}