<?php

declare(strict_types=1);

final class ReportController
{
    public function index(): void
    {
        Auth::require();
        $filters = $_GET;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $data = (new Feedback())->paginate($filters, $page, 10);
        View::render('reports/index', ['title' => 'Reports', 'data' => $data, 'filters' => $filters, 'summary' => (new Feedback())->summary()]);
    }

    public function exportCsv(): void
    {
        Auth::require();
        Security::verifyCsrf();
        $data = (new Feedback())->paginate($_GET, 1, 5000)['items'];
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="feedback-report.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Ticket', 'Name', 'Email', 'Phone', 'Branch', 'Category', 'Type', 'Rating', 'Status', 'Priority', 'Created']);
        foreach ($data as $row) {
            fputcsv($out, [$row['ticketNumber'], $row['name'], $row['email'], $row['phone'], $row['branchName'] ?? '', $row['category'], $row['type'], $row['rating'], $row['status'], $row['priority'], $row['createdAt']]);
        }
        exit;
    }

    public function exportPdf(): void
    {
        Auth::require();
        Security::verifyCsrf();
        $filters = $_GET;
        $data = (new Feedback())->paginate($filters, 1, 5000);
        $summary = (new Feedback())->summary();

        header('Content-Type: text/html; charset=utf-8');
        $logoPath = '/assets/uploads/restaurant/logo-dark.png';
        extract(['data' => $data, 'summary' => $summary, 'filters' => $filters, 'logoPath' => $logoPath], EXTR_OVERWRITE);
        require __DIR__ . '/../views/reports/pdf.php';
        exit;
    }
}
