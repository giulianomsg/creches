<?php

namespace App\Controllers;

use App\Helpers\SessionHelper;
use App\Models\StudentModel;
use App\Models\UnitModel;
use App\Services\PriorityService;

class DashboardController extends BaseController
{
    private StudentModel $students;
    private UnitModel $units;
    private PriorityService $priorityService;

    public function __construct()
    {
        parent::__construct();
        $this->students = new StudentModel();
        $this->units = new UnitModel();
        $this->priorityService = new PriorityService();
        $this->ensureAuthenticated();
    }

    private function ensureAuthenticated(): void
    {
        if (!SessionHelper::get('user')) {
            $this->redirect('/login.php');
        }
    }

    public function index(): string
    {
        $user = SessionHelper::get('user');
        $students = $this->students->all();
        $total = count($students);
        $emEspera = count(array_filter($students, fn($s) => $s['status'] === 'lista_espera'));
        $emAnalise = count(array_filter($students, fn($s) => $s['status'] === 'analise'));
        $comVaga = count(array_filter($students, fn($s) => $s['status'] === 'vaga_concedida'));
        $units = $this->units->all();

        $vulnerabilidadeAlta = count(array_filter($students, fn($s) => (int)$s['alta_vulnerabilidade'] === 1));
        $vulnerabilidadeMedia = count(array_filter($students, fn($s) => (int)$s['media_vulnerabilidade'] === 1));
        $necessidadesEspeciais = count(array_filter($students, fn($s) => (int)$s['necessidades_especiais'] === 1));

        return $this->render('dashboard/index', compact(
            'user',
            'total',
            'emEspera',
            'emAnalise',
            'comVaga',
            'units',
            'vulnerabilidadeAlta',
            'vulnerabilidadeMedia',
            'necessidadesEspeciais'
        ));
    }
}
