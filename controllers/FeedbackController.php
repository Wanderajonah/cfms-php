<?php

declare(strict_types=1);

final class FeedbackController
{
    public function publicForm(): void
    {
        View::render('feedback/public_form', [
            'title' => 'Submit Feedback',
            'branches' => (new Branch())->list(),
        ], 'public');
    }

    public function publicCreate(): void
    {
        Security::verifyCsrf();
        try {
            $created = (new Feedback())->create($_POST);
            (new AuditLog())->record(Auth::id(), 'create', 'feedback', (int) $created['id'], 'Feedback submitted');

            $this->autoRespond((int) $created['id'], $created);

            $_SESSION['_feedback_thanks'] = [
                'ticket' => (int) $created['ticketNumber'],
                'name' => trim((string) ($created['name'] ?? '')),
            ];
            Response::redirect('/');
        } catch (Throwable $e) {
            Flash::error($e->getMessage());
            Response::redirect('/');
        }
    }

    private function autoRespond(int $id, array $created): void
    {
        $phone = trim((string) ($created['phone'] ?? ''));

        if ($phone === '') {
            (new Feedback())->update($id, ['automated_sms_skipped' => 'no_phone']);
            return;
        }

        if (!Sms::enabled()) {
            (new Feedback())->update($id, ['automated_sms_skipped' => 'disabled']);
            return;
        }

        $normalized = Sms::normalizePhone($phone);
        if (!$normalized || strlen($normalized) < 10) {
            (new Feedback())->update($id, ['automated_sms_skipped' => 'invalid_phone']);
            return;
        }

        $smsBody = null;
        $aiFailed = false;

        if (GroqAi::enabled()) {
            try {
                $smsBody = GroqAi::generateSmsReply($created);
            } catch (Throwable $e) {
                $aiFailed = true;
                error_log('[auto-sms] feedback#' . $id . ' Groq error: ' . $e->getMessage());
            }
        }

        if ($smsBody === null) {
            $smsBody = GroqAi::buildFallbackSms($created);
            if ($aiFailed) {
                (new Feedback())->update($id, ['automated_sms_error' => 'AI failed, using fallback']);
            }
        }

        try {
            $result = Sms::send($phone, $smsBody);
            if ($result['success']) {
                (new Feedback())->update($id, [
                    'automated_sms_at' => date('Y-m-d H:i:s'),
                    'automated_sms_body' => $smsBody,
                    'response' => $smsBody,
                    'responded_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                (new Feedback())->update($id, [
                    'automated_sms_body' => $smsBody,
                    'automated_sms_error' => $result['error'],
                ]);
            }
        } catch (Throwable $e) {
            (new Feedback())->update($id, [
                'automated_sms_body' => $smsBody,
                'automated_sms_error' => $e->getMessage(),
            ]);
        }
    }

    public function track(): void
    {
        $item = null;
        $ticket = !empty($_GET['ticket']) ? (int) $_GET['ticket'] : 0;
        $phone = trim((string) ($_GET['phone'] ?? ''));
        if ($ticket > 0 && $phone !== '') {
            $item = (new Feedback())->findByTicketAndPhone($ticket, $phone);
        }
        View::render('feedback/track', ['title' => 'Track Feedback', 'item' => $item], 'public');
    }

    public function index(): void
    {
        Auth::require();
        $filters = $_GET;
        $user = Auth::user();
        if ($user && $user['role_slug'] !== 'admin') {
            $filters['assignedTo'] = $user['email'];
        }
        $page = max(1, (int) ($filters['page'] ?? 1));
        $paginated = (new Feedback())->paginate($filters, $page, 10, (string) ($_GET['sort'] ?? '-createdAt'));
        View::render('feedback/index', [
            'title' => 'Feedback',
            'data' => $paginated,
            'filters' => $filters,
            'branches' => (new Branch())->list(),
        ]);
    }

    public function complaints(): void
    {
        Auth::require();
        $filters = $_GET;
        $filters['type'] = 'complaint';
        $user = Auth::user();
        if ($user && $user['role_slug'] !== 'admin') {
            $filters['assignedTo'] = $user['email'];
        }
        $page = max(1, (int) ($filters['page'] ?? 1));
        $paginated = (new Feedback())->paginate($filters, $page, 10, (string) ($_GET['sort'] ?? '-createdAt'));
        View::render('complaints/index', [
            'title' => 'Complaints',
            'data' => $paginated,
            'filters' => $filters,
            'branches' => (new Branch())->list(),
        ]);
    }

    public function suggestions(): void
    {
        Auth::require();
        $filters = $_GET;
        $filters['type'] = 'suggestion';
        $user = Auth::user();
        if ($user && $user['role_slug'] !== 'admin') {
            $filters['assignedTo'] = $user['email'];
        }
        $page = max(1, (int) ($filters['page'] ?? 1));
        $paginated = (new Feedback())->paginate($filters, $page, 10, (string) ($_GET['sort'] ?? '-createdAt'));
        View::render('suggestions/index', [
            'title' => 'Suggestions',
            'data' => $paginated,
            'filters' => $filters,
            'branches' => (new Branch())->list(),
        ]);
    }

    public function compliments(): void
    {
        Auth::require();
        $filters = $_GET;
        $filters['type'] = 'compliment';
        $user = Auth::user();
        if ($user && $user['role_slug'] !== 'admin') {
            $filters['assignedTo'] = $user['email'];
        }
        $page = max(1, (int) ($filters['page'] ?? 1));
        $paginated = (new Feedback())->paginate($filters, $page, 10, (string) ($_GET['sort'] ?? '-createdAt'));
        View::render('compliments/index', [
            'title' => 'Compliments',
            'data' => $paginated,
            'filters' => $filters,
            'branches' => (new Branch())->list(),
        ]);
    }

    public function show(int $id): void
    {
        Auth::require();
        $item = (new Feedback())->find($id);
        if (!$item) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Not Found']);
            return;
        }
        View::render('feedback/show', ['title' => 'Feedback #' . $item['ticketNumber'], 'item' => $item, 'staff' => (new User())->list(['role' => 'staff'])]);
    }

    public function create(): void
    {
        Auth::require();
        Security::verifyCsrf();
        try {
            $created = (new Feedback())->create($_POST);
            (new AuditLog())->record(Auth::id(), 'create', 'feedback', (int) $created['id'], 'Feedback created');
            Flash::success('Feedback created.');
            Response::redirect('/feedback/' . $created['id']);
        } catch (Throwable $e) {
            Flash::error($e->getMessage());
            Response::redirect('/feedback');
        }
    }

    public function assign(int $id): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        $updated = (new Feedback())->update($id, ['assignedTo' => $_POST['assignedTo'] ?? null, 'status' => 'in-progress']);
        (new AuditLog())->record(Auth::id(), 'assignment', 'feedback', $id, 'Feedback assigned');
        Flash::success('Feedback assigned.');
        Response::redirect('/feedback/' . $id);
    }

    public function respond(int $id): void
    {
        Auth::requireRole('admin', 'staff');
        Security::verifyCsrf();
        $response = trim((string) ($_POST['response'] ?? ''));
        if ($response === '') {
            Flash::error('Response is required');
            Response::redirect('/feedback/' . $id);
        }
        (new Feedback())->update($id, ['response' => $response]);
        (new AuditLog())->record(Auth::id(), 'response', 'feedback', $id, 'Feedback response added');
        Flash::success('Response saved.');
        Response::redirect('/feedback/' . $id);
    }

    public function status(int $id): void
    {
        Auth::requireRole('admin', 'staff');
        Security::verifyCsrf();
        $action = $_POST['action'] ?? '';
        $payload = match ($action) {
            'resolve' => ['status' => 'resolved', 'staffNotes' => $_POST['staffNotes'] ?? null],
            'escalate' => ['status' => 'escalated', 'priority' => 'high', 'escalationNote' => trim((string) ($_POST['note'] ?? ''))],
            'reopen' => ['status' => 'pending'],
            'close' => ['status' => 'resolved'],
            default => ['status' => $_POST['status'] ?? 'pending'],
        };
        (new Feedback())->update($id, $payload);
        (new AuditLog())->record(Auth::id(), 'status_change', 'feedback', $id, 'Feedback status changed');
        Flash::success('Status updated.');
        Response::redirect('/feedback/' . $id);
    }

    public function delete(int $id): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        (new Feedback())->delete($id);
        (new AuditLog())->record(Auth::id(), 'delete', 'feedback', $id, 'Feedback deleted');
        Flash::success('Feedback deleted.');
        Response::redirect('/feedback');
    }

    public function apiSummary(): void
    {
        Auth::requireApiRole('admin');
        Response::json((new Feedback())->summary((int) ($_GET['months'] ?? 6), (int) ($_GET['days'] ?? 30), $_GET['startDate'] ?? null));
    }

    public function apiList(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        Response::json((new Feedback())->paginate($_GET, $page, max(1, (int) ($_GET['limit'] ?? 20)), (string) ($_GET['sort'] ?? '-createdAt')));
    }

    public function apiGet(int $id): void
    {
        $item = (new Feedback())->find($id);
        $item ? Response::json($item) : Response::json(['message' => 'Feedback not found'], 404);
    }

    public function apiCreate(): void
    {
        try {
            Response::json((new Feedback())->create($this->body()), 201);
        } catch (Throwable $e) {
            Response::json(['message' => $e->getMessage()], 400);
        }
    }

    public function apiAssign(int $id): void
    {
        Auth::requireApiRole('admin');
        $body = $this->body();
        $item = (new Feedback())->update($id, ['assignedTo' => $body['assignedTo'] ?? null, 'status' => 'in-progress']);
        $item ? Response::json($item) : Response::json(['message' => 'Feedback not found'], 404);
    }

    public function apiRespond(int $id): void
    {
        Auth::requireApiRole('admin', 'staff');
        $body = $this->body();
        $response = trim((string) ($body['response'] ?? ''));
        if ($response === '') {
            Response::json(['message' => 'Response is required'], 400);
        }
        $item = (new Feedback())->update($id, ['response' => $response]);
        $item ? Response::json(['feedback' => $item, 'smsResult' => null, 'smsError' => null]) : Response::json(['message' => 'Feedback not found'], 404);
    }

    public function apiResolve(int $id): void
    {
        Auth::requireApiRole('admin', 'staff');
        $body = $this->body();
        $item = (new Feedback())->update($id, ['status' => 'resolved', 'staffNotes' => $body['staffNotes'] ?? null]);
        $item ? Response::json($item) : Response::json(['message' => 'Feedback not found'], 404);
    }

    public function apiEscalate(int $id): void
    {
        Auth::requireApiRole('admin', 'staff');
        $body = $this->body();
        $item = (new Feedback())->update($id, ['status' => 'escalated', 'priority' => 'high', 'escalationNote' => $body['note'] ?? null]);
        $item ? Response::json($item) : Response::json(['message' => 'Feedback not found'], 404);
    }

    public function apiUpdate(int $id): void
    {
        Auth::requireApiRole('admin', 'staff');
        $item = (new Feedback())->update($id, $this->body());
        $item ? Response::json($item) : Response::json(['message' => 'Feedback not found'], 404);
    }

    public function apiDelete(int $id): void
    {
        Auth::requireApiRole('admin');
        (new Feedback())->delete($id) ? Response::json(['ok' => true]) : Response::json(['message' => 'Feedback not found'], 404);
    }

    private function body(): array
    {
        return json_decode(file_get_contents('php://input') ?: '[]', true) ?: $_POST;
    }
}
