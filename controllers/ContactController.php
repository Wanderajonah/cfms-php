<?php

declare(strict_types=1);

final class ContactController
{
    public function index(): void
    {
        Auth::requireRole('admin');
        View::render('contacts/index', ['title' => 'Contacts', 'contacts' => (new Contact())->list((string) ($_GET['search'] ?? ''))]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        try {
            $created = (new Contact())->create($_POST);
            (new AuditLog())->record(Auth::id(), 'create', 'contacts', (int) $created['id'], 'Contact created');
            Flash::success('Contact created.');
        } catch (Throwable $e) {
            Flash::error($e->getMessage());
        }
        Response::redirect('/contacts');
    }

    public function delete(int $id): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        (new Contact())->deactivate($id);
        (new AuditLog())->record(Auth::id(), 'delete', 'contacts', $id, 'Contact deactivated');
        Flash::success('Contact removed.');
        Response::redirect('/contacts');
    }

    public function apiList(): void
    {
        Auth::requireApiRole('admin');
        Response::json(['items' => (new Contact())->list((string) ($_GET['search'] ?? ''))]);
    }

    public function apiCreate(): void
    {
        Auth::requireApiRole('admin');
        try {
            Response::json((new Contact())->create(json_decode(file_get_contents('php://input') ?: '[]', true) ?: []), 201);
        } catch (Throwable $e) {
            Response::json(['message' => $e->getMessage()], 400);
        }
    }

    public function apiDelete(int $id): void
    {
        Auth::requireApiRole('admin');
        (new Contact())->deactivate($id) ? Response::json(['ok' => true]) : Response::json(['message' => 'Contact not found'], 404);
    }
}
