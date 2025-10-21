<?php

namespace App\Models;

use PDO;

class StudentModel extends BaseModel
{
    public function all(array $filters = []): array
    {
        $query = 'SELECT s.*, u.name AS unidade_preferida FROM students s LEFT JOIN student_units su ON su.student_id = s.id AND su.priority = 1 LEFT JOIN units u ON u.id = su.unit_id';
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 's.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = '(s.nome LIKE :search OR s.nis LIKE :search OR s.cpf_pai LIKE :search OR s.cpf_mae LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if ($conditions) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' ORDER BY s.created_at DESC';

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        return $student ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO students (
            nome, data_nascimento, sexo, nome_mae, nome_pai, telefone, requerente,
            possui_gemeo, nome_gemeo, possui_irmao_lista, necessidades_especiais,
            mae_trabalha, mae_adolescente, sob_guarda_avo, pais_deficientes,
            filho_servidor, servidor_municipal, bolsa_familia, nis,
            alta_vulnerabilidade, media_vulnerabilidade, endereco, numero,
            complemento, bairro, cep, latitude, longitude, status, created_by
        ) VALUES (
            :nome, :data_nascimento, :sexo, :nome_mae, :nome_pai, :telefone, :requerente,
            :possui_gemeo, :nome_gemeo, :possui_irmao_lista, :necessidades_especiais,
            :mae_trabalha, :mae_adolescente, :sob_guarda_avo, :pais_deficientes,
            :filho_servidor, :servidor_municipal, :bolsa_familia, :nis,
            :alta_vulnerabilidade, :media_vulnerabilidade, :endereco, :numero,
            :complemento, :bairro, :cep, :latitude, :longitude, :status, :created_by
        )');

        $stmt->execute($data);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare('UPDATE students SET
            nome = :nome,
            data_nascimento = :data_nascimento,
            sexo = :sexo,
            nome_mae = :nome_mae,
            nome_pai = :nome_pai,
            telefone = :telefone,
            requerente = :requerente,
            possui_gemeo = :possui_gemeo,
            nome_gemeo = :nome_gemeo,
            possui_irmao_lista = :possui_irmao_lista,
            necessidades_especiais = :necessidades_especiais,
            mae_trabalha = :mae_trabalha,
            mae_adolescente = :mae_adolescente,
            sob_guarda_avo = :sob_guarda_avo,
            pais_deficientes = :pais_deficientes,
            filho_servidor = :filho_servidor,
            servidor_municipal = :servidor_municipal,
            bolsa_familia = :bolsa_familia,
            nis = :nis,
            alta_vulnerabilidade = :alta_vulnerabilidade,
            media_vulnerabilidade = :media_vulnerabilidade,
            endereco = :endereco,
            numero = :numero,
            complemento = :complemento,
            bairro = :bairro,
            cep = :cep,
            latitude = :latitude,
            longitude = :longitude,
            status = :status
        WHERE id = :id');

        $stmt->execute($data);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE students SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM students WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function demandByMacrorregiao(): array
    {
        $sql = "SELECT u.macrorregiao, COUNT(*) AS total FROM student_units su INNER JOIN units u ON u.id = su.unit_id INNER JOIN students s ON s.id = su.student_id WHERE su.priority = 1 GROUP BY u.macrorregiao ORDER BY total DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function heatmapPoints(): array
    {
        $sql = "SELECT latitude, longitude, COUNT(*) AS total FROM students WHERE latitude IS NOT NULL AND latitude <> '' AND longitude IS NOT NULL AND longitude <> '' AND status IN ('analise','lista_espera') GROUP BY latitude, longitude";
        $stmt = $this->db->query($sql);
        $points = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lat = (float) $row['latitude'];
            $lng = (float) $row['longitude'];
            if ($lat === 0.0 && $lng === 0.0) {
                continue;
            }
            $points[] = [
                'lat' => $lat,
                'lng' => $lng,
                'weight' => (int) $row['total'],
            ];
        }

        return $points;
    }
}
