<?php

namespace App\Models;

class StudentEnrolledSiblingModel extends BaseModel
{
    public function saveList(int $studentId, array $siblings): void
    {
        $this->deleteByStudent($studentId);
        $stmt = $this->db->prepare('INSERT INTO student_enrolled_siblings (student_id, nome, escola) VALUES (:student_id, :nome, :escola)');
        foreach ($siblings as $sibling) {
            $name = trim($sibling['nome'] ?? '');
            $school = trim($sibling['escola'] ?? '');
            if ($name === '' && $school === '') {
                continue;
            }
            $stmt->execute([
                'student_id' => $studentId,
                'nome' => $name,
                'escola' => $school,
            ]);
        }
    }

    public function list(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM student_enrolled_siblings WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }

    public function deleteByStudent(int $studentId): void
    {
        $stmt = $this->db->prepare('DELETE FROM student_enrolled_siblings WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
    }
}
