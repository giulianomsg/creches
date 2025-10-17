<?php

namespace App\Models;

use PDO;

class UserModel extends BaseModel
{
    public function findByGoogleId(string $googleId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE google_id = :google_id LIMIT 1');
        $stmt->execute(['google_id' => $googleId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (google_id, name, email, role) VALUES (:google_id, :name, :email, :role)');
        $stmt->execute([
            'google_id' => $data['google_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM users ORDER BY name')->fetchAll();
    }
}
