<?php

namespace App\Models;

class DocumentModel extends BaseModel
{
    public function byStudent(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM documents WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function saveMany(int $studentId, array $documents): void
    {
        $stmt = $this->db->prepare('INSERT INTO documents (student_id, tipo, arquivo) VALUES (:student_id, :tipo, :arquivo)');

        foreach ($documents as $type => $file) {
            if (!$file) {
                continue;
            }

            $stmt->execute([
                'student_id' => $studentId,
                'tipo' => $type,
                'arquivo' => $file,
            ]);
        }
    }

    public function deleteByStudent(int $studentId): void
    {
        $stmt = $this->db->prepare('DELETE FROM documents WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
    }
}
