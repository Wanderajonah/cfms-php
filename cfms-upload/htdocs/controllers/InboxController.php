<?php

declare(strict_types=1);

final class InboxController
{
    public function index(): void
    {
        Auth::require();
        $user = Auth::user();
        $filters = ['status' => 'pending'];
        if ($user && $user['role_slug'] !== 'admin') {
            $filters['assignedTo'] = $user['email'];
        }
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $paginated = (new Feedback())->paginate($filters, $page, 10, '-createdAt');
        View::render('inbox/index', ['title' => 'Inbox', 'data' => $paginated]);
    }
}
