<?php
class PostModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($userId, $content, $filePath = null) {
        $sql = "INSERT INTO posts (user_id, content, file_path)
                VALUES (:user_id, :content, :file_path)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':user_id' => $userId,
            ':content' => $content,
            ':file_path' => $filePath
        ]);
    }

    public function getAll() {
        $sql = "SELECT posts.*, users.username, users.full_name,
                       users.profile_image
                FROM posts
                JOIN users ON posts.user_id = users.id
                ORDER BY posts.created_at DESC, posts.id DESC";

        return $this->db->query($sql)->fetchAll();
    }

    public function delete($postId, $userId) {
        $stmt = $this->db->prepare(
            "DELETE FROM posts WHERE id = :id AND user_id = :user_id"
        );

        return $stmt->execute([
            ':id' => $postId,
            ':user_id' => $userId
        ]);
    }

    /* ==================== LIKES & COMMENTS METHODS ==================== */

    // Toggle Like (Heart)
    public function toggleLike($postId, $userId) {
        $stmt = $this->db->prepare("SELECT id FROM likes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute([
            ':post_id' => $postId,
            ':user_id' => $userId
        ]);

        if ($stmt->fetch()) {
            // Unlike if already liked
            $delete = $this->db->prepare("DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id");
            return $delete->execute([
                ':post_id' => $postId,
                ':user_id' => $userId
            ]);
        } else {
            // Like post
            $insert = $this->db->prepare("INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)");
            return $insert->execute([
                ':post_id' => $postId,
                ':user_id' => $userId
            ]);
        }
    }

    // Check if user liked post
    public function isLikedByUser($postId, $userId) {
        $stmt = $this->db->prepare("SELECT id FROM likes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->execute([
            ':post_id' => $postId,
            ':user_id' => $userId
        ]);
        return (bool)$stmt->fetch();
    }

    // Get Total Likes Count
    public function getLikeCount($postId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM likes WHERE post_id = :post_id");
        $stmt->execute([':post_id' => $postId]);
        return $stmt->fetchColumn();
    }

    // Add Comment
    public function addComment($postId, $userId, $text) {
        $stmt = $this->db->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (:post_id, :user_id, :comment_text)");
        return $stmt->execute([
            ':post_id' => $postId,
            ':user_id' => $userId,
            ':comment_text' => $text
        ]);
    }

    // Get Comments for a Post
    public function getComments($postId) {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.username, u.full_name, u.profile_image 
             FROM comments c 
             JOIN users u ON c.user_id = u.id 
             WHERE c.post_id = :post_id 
             ORDER BY c.created_at ASC"
        );
        $stmt->execute([':post_id' => $postId]);
        return $stmt->fetchAll();
    }
}