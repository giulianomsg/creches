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
        $stmt = $this->db->prepare('INSERT INTO users (google_id, name, email, role, password_hash) VALUES (:google_id, :name, :email, :role, :password_hash)');
        $stmt->execute([
            'google_id' => $data['google_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'cadastro',
            'password_hash' => $data['password_hash'] ?? null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM users ORDER BY name')->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function attachGoogleAccount(int $userId, string $googleId): void
    {
        $stmt = $this->db->prepare('UPDATE users SET google_id = :google_id WHERE id = :id');
        $stmt->execute([
            'google_id' => $googleId,
            'id' => $userId,
        ]);
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
        $stmt->execute([
            'password_hash' => $passwordHash,
            'id' => $userId,
        ]);
    }

    public function hasLocalUsers(): bool
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM users WHERE password_hash IS NOT NULL');
        return (bool)$stmt->fetchColumn();
    }
}
