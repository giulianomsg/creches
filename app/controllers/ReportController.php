<?php

namespace App\Controllers;

use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Models\StudentModel;
use App\Models\UnitModel;
use App\Services\PriorityService;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends BaseController
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
        $units = $this->units->all();
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['busca'] ?? null,
        ];
        $students = $this->students->all($filters);
        foreach ($students as &$student) {
            $student['pontuacao'] = $this->priorityService->calculateScore($student);
        }

        return $this->render('reports/index', compact('students', 'units', 'user'));
    }

    public function exportCsv(): void
    {
        $students = $this->students->all();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nome', 'Data de Nascimento', 'Status', 'Pontuação']);
        foreach ($students as $student) {
            fputcsv($output, [
                $student['nome'],
                $student['data_nascimento'],
                $student['status'],
                $this->priorityService->calculateScore($student),
            ]);
        }
        fclose($output);
        exit;
    }

    public function exportExcel(): void
    {
        $students = $this->students->all();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['Nome', 'Data de Nascimento', 'Status', 'Pontuação'], null, 'A1');
        $row = 2;
        foreach ($students as $student) {
            $sheet->fromArray([
                $student['nome'],
                $student['data_nascimento'],
                $student['status'],
                $this->priorityService->calculateScore($student),
            ], null, 'A' . $row++);
        }
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="relatorio.xlsx"');
        $writer->save('php://output');
        exit;
    }

    public function exportPdf(): void
    {
        $students = $this->students->all();
        $html = '<h1>Relatório de Lista de Espera</h1><table border="1" width="100%" cellspacing="0" cellpadding="4">';
        $html .= '<tr><th>Nome</th><th>Data de Nascimento</th><th>Status</th><th>Pontuação</th></tr>';
        foreach ($students as $student) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($student['nome']) . '</td>';
            $html .= '<td>' . htmlspecialchars($student['data_nascimento']) . '</td>';
            $html .= '<td>' . htmlspecialchars($student['status']) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)$this->priorityService->calculateScore($student)) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream('relatorio.pdf');
        exit;
    }
}
