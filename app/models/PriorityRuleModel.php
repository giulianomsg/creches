<?php

namespace App\Models;

class PriorityRuleModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM priority_rules ORDER BY ordem')->fetchAll();
    }

    public function updateWeights(array $weights): void
    {
        $stmt = $this->db->prepare('UPDATE priority_rules SET peso = :peso WHERE chave = :chave');
        foreach ($weights as $key => $weight) {
            $stmt->execute([
                'peso' => (int)$weight,
                'chave' => $key,
            ]);
        }
    }
}
