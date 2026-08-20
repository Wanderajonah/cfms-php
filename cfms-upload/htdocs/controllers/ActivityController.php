<?php

declare(strict_types=1);

final class ActivityController
{
    public function index(): void
    {
        Auth::require();
        $user = Auth::user();
        if ($user && $user['role_slug'] !== 'admin') {
            $entries = (new AuditLog())->forUser((int) $user['id'], 50);
            View::render('activity/index', ['title' => 'My Activity', 'entries' => $entries, 'personal' => true]);
        } else {
            $entries = (new AuditLog())->latest(50);
            View::render('activity/index', ['title' => 'Activity', 'entries' => $entries]);
        }
    }
}
