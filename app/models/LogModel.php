<?php

namespace App\Models;

class LogModel extends BaseModel
{
    public function record(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO logs (user_id, acao, descricao, criado_em, ip) VALUES (:user_id, :acao, :descricao, :criado_em, :ip)');
        $stmt->execute([
            'user_id' => $data['user_id'] ?? null,
            'acao' => $data['acao'],
            'descricao' => $data['descricao'],
            'criado_em' => date('Y-m-d H:i:s'),
            'ip' => $data['ip'] ?? ($_SERVER['REMOTE_ADDR'] ?? null),
        ]);
    }

    public function all(int $limit = 200): array
    {
        $stmt = $this->db->prepare('SELECT l.*, u.name as usuario FROM logs l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.criado_em DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
