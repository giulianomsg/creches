<?php

namespace App\Services;

use App\Models\PriorityRuleModel;
use App\Models\StudentModel;
use App\Models\StudentUnitModel;

class PriorityService
{
    private PriorityRuleModel $rules;
    private StudentUnitModel $studentUnits;

    public function __construct()
    {
        $this->rules = new PriorityRuleModel();
        $this->studentUnits = new StudentUnitModel();
    }

    public function calculateScore(array $student): int
    {
        $rules = $this->rules->all();
        $weights = array_column($rules, 'peso', 'chave');

        $score = 0;
        $score += !empty($student['mae_trabalha']) ? ($weights['mae_trabalha'] ?? 0) : 0;
        $score += !empty($student['alta_vulnerabilidade']) ? ($weights['alta_vulnerabilidade'] ?? 0) : 0;
        $score += !empty($student['media_vulnerabilidade']) ? ($weights['media_vulnerabilidade'] ?? 0) : 0;
        $score += !empty($student['necessidades_especiais']) ? ($weights['necessidades_especiais'] ?? 0) : 0;
        $score += !empty($student['possui_irmao_lista']) ? ($weights['irmao_lista'] ?? 0) : 0;
        $score += !empty($student['filho_servidor']) ? ($weights['filho_servidor'] ?? 0) : 0;
        $score += !empty($student['servidor_municipal']) ? ($weights['servidor_municipal'] ?? 0) : 0;
        $score += !empty($student['bolsa_familia']) ? ($weights['bolsa_familia'] ?? 0) : 0;
        $score += !empty($student['mae_adolescente']) ? ($weights['mae_adolescente'] ?? 0) : 0;
        $score += !empty($student['pais_deficientes']) ? ($weights['pais_deficientes'] ?? 0) : 0;

        return $score;
    }

    public function rankingByUnit(int $unitId): array
    {
        $studentModel = new StudentModel();
        $students = $studentModel->all();
        $ranking = [];

        foreach ($students as $student) {
            $preferences = $this->studentUnits->preferences($student['id']);
            $position = array_search($unitId, array_column($preferences, 'unit_id'));
            if ($position === false) {
                continue;
            }

            $student['pontuacao'] = $this->calculateScore($student);
            $student['preferencia'] = $position + 1;
            $ranking[] = $student;
        }

        usort($ranking, function ($a, $b) {
            if ($a['pontuacao'] === $b['pontuacao']) {
                return $a['preferencia'] <=> $b['preferencia'];
            }
            return $b['pontuacao'] <=> $a['pontuacao'];
        });

        return $ranking;
    }
}
