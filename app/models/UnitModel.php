<?php

namespace App\Models;

use PDO;

class UnitModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM units ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO units (name, endereco, bairro, cep, latitude, longitude, capacidade) VALUES (:name, :endereco, :bairro, :cep, :latitude, :longitude, :capacidade)');
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare('UPDATE units SET name = :name, endereco = :endereco, bairro = :bairro, cep = :cep, latitude = :latitude, longitude = :longitude, capacidade = :capacidade WHERE id = :id');
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM units WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
