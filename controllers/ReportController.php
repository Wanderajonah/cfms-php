<?php

declare(strict_types=1);

final class ReportController
{
    public function index(): void
    {
        Auth::require();
        $filters = $_GET;
        $data = (new Feedback())->paginate($filters, 1, 500);
        View::render('reports/index', ['title' => 'Reports', 'data' => $data, 'filters' => $filters, 'summary' => (new Feedback())->summary()]);
    }

    public function exportCsv(): void
    {
        Auth::require();
        $data = (new Feedback())->paginate($_GET, 1, 5000)['items'];
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="feedback-report.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Ticket', 'Name', 'Email', 'Phone', 'Category', 'Type', 'Rating', 'Status', 'Priority', 'Created']);
        foreach ($data as $row) {
            fputcsv($out, [$row['ticketNumber'], $row['name'], $row['email'], $row['phone'], $row['category'], $row['type'], $row['rating'], $row['status'], $row['priority'], $row['createdAt']]);
        }
        exit;
    }

    public function exportPdf(): void
    {
        Auth::require();
        header('Content-Type: text/html; charset=utf-8');
        echo '<script>window.print()</script>';
        $this->index();
    }
}
