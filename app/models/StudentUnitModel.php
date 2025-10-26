<?php

namespace App\Models;

class StudentUnitModel extends BaseModel
{
    public function savePreferences(int $studentId, array $units): void
    {
        $this->deleteByStudent($studentId);
        $stmt = $this->db->prepare('INSERT INTO student_units (student_id, unit_id, priority) VALUES (:student_id, :unit_id, :priority)');
        $priority = 1;
        foreach ($units as $unitId) {
            if (!$unitId) {
                continue;
            }
            $stmt->execute([
                'student_id' => $studentId,
                'unit_id' => $unitId,
                'priority' => $priority++,
            ]);
        }
    }

    public function deleteByStudent(int $studentId): void
    {
        $stmt = $this->db->prepare('DELETE FROM student_units WHERE student_id = :student_id');
        $stmt->execute(['student_id' => $studentId]);
    }

    public function preferences(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT su.*, u.name FROM student_units su INNER JOIN units u ON u.id = su.unit_id WHERE student_id = :student_id ORDER BY priority');
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }
}
