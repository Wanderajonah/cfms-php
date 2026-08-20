<?php

declare(strict_types=1);

final class UserController
{
    public function index(): void
    {
        Auth::requireRole('admin');
        View::render('users/index', [
            'title' => 'Users',
            'users' => (new User())->list(['role' => $_GET['role'] ?? '', 'search' => $_GET['search'] ?? '']),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        try {
            $created = (new User())->create($_POST);
            (new AuditLog())->record(Auth::id(), 'create', 'users', (int) $created['id'], 'User created');
            Flash::success('User created.');
        } catch (Throwable $e) {
            Flash::error($e->getMessage());
        }
        Response::redirect('/users');
    }

    public function update(int $id): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        (new User())->update($id, [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role' => $_POST['role'] ?? 'staff',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'password' => $_POST['password'] ?? '',
        ]);
        (new AuditLog())->record(Auth::id(), 'update', 'users', $id, 'User updated');
        Flash::success('User updated.');
        Response::redirect('/users');
    }

    public function delete(int $id): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        (new User())->delete($id);
        (new AuditLog())->record(Auth::id(), 'delete', 'users', $id, 'User deactivated');
        Flash::success('User deactivated.');
        Response::redirect('/users');
    }
}
