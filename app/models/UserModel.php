
<?php

class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($username, $password, $fullName) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, full_name)
                VALUES (:username, :password, :full_name)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':full_name' => $fullName
        ]);
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);

        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare(
            "SELECT id, username, full_name, bio, profile_image
             FROM users WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }
}
