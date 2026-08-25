<?php

declare(strict_types=1);

final class DashboardController
{
    public function index(): void
    {
        Auth::require();
        $user = Auth::user();
        $feedback = new Feedback();
        if ($user && $user['role_slug'] !== 'admin') {
            $email = $user['email'];
            $summary = $feedback->staffSummary($email);
            $recent = $feedback->paginate(['assignedTo' => $email], 1, 8)['items'];
            View::render('dashboard/staff', [
                'title' => 'My Dashboard',
                'summary' => $summary,
                'recent' => $recent,
            ]);
        } else {
            View::render('dashboard/index', [
                'title' => 'Dashboard',
                'summary' => $feedback->summary(),
                'recent' => $feedback->paginate([], 1, 8)['items'],
                'activity' => (new AuditLog())->latest(8),
                'staffAssignments' => (new User())->listWithAssignments(),
            ]);
        }
    }
}
