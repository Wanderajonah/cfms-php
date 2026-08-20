<?php

declare(strict_types=1);

final class AnalyticsController
{
    public function index(): void
    {
        Auth::require();
        $user = Auth::user();
        $feedback = new Feedback();
        if ($user && $user['role_slug'] !== 'admin') {
            $email = $user['email'];
            View::render('analytics/staff', [
                'title' => 'My Analytics',
                'summary' => $feedback->staffSummary($email),
                'monthly' => $feedback->staffMonthly($email),
                'categories' => $feedback->staffCategories($email),
                'types' => $feedback->staffTypes($email),
                'priorities' => $feedback->staffPriorities($email),
                'weekdays' => $feedback->staffWeekdays($email),
                'hours' => $feedback->staffHours($email),
                'recent' => $feedback->paginate(['assignedTo' => $email], 1, 5)['items'],
            ]);
        } else {
            View::render('analytics/index', [
                'title' => 'Analytics',
                'summary' => $feedback->summary(),
            ]);
        }
    }
}
