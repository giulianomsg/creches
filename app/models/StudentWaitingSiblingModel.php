<?php

namespace App\Models;

class StudentWaitingSiblingModel extends BaseModel
{
    public function saveList(int $studentId, array $siblings): void
    {
        $this->deleteByStudent($studentId);
        $stmt = $this->db->prepare('INSERT INTO student_waiting_siblings (student_id, nome) VALUES (:student_id, :nome)');
        foreach ($siblings as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }
            $stmt->execute([
                'student_id' => $studentId,
                'nome' => $name,
            ]);
        }
    }

    public function list(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM student_waiting_siblings WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function deleteByStudent(int $studentId): void
    {
        $stmt = $this->db->prepare('DELETE FROM student_waiting_siblings WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
    }
}
