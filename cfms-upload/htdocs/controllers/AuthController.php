<?php

declare(strict_types=1);

final class AuthController
{
    public function showLogin(): void
    {
        View::render('auth/login', ['title' => 'Login'], 'auth');
    }

    public function login(): void
    {
        Security::verifyCsrf();
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $requestedRole = ($_POST['role'] ?? 'admin') === 'admin' ? 'admin' : 'staff';
        if ($email === '' || $password === '') {
            Flash::error('Email and password are required');
            Response::redirect('/login');
        }
        $user = (new User())->findByEmail($email);
        if (!$user || (int) $user['is_active'] !== 1 || !password_verify($password, $user['password_hash'])) {
            Flash::error('Invalid credentials');
            Response::redirect('/login');
        }
        if ($user['role_slug'] !== $requestedRole) {
            Flash::error('Invalid credentials for selected role');
            Response::redirect('/login');
        }
        Auth::login($user, isset($_POST['remember']));
        Response::redirect('/dashboard');
    }

    public function showRegister(): void
    {
        View::render('auth/register', ['title' => 'Register'], 'auth');
    }

    public function register(): void
    {
        Security::verifyCsrf();
        $userModel = new User();
        $role = $userModel->count() > 0 ? 'staff' : 'admin';
        try {
            $created = $userModel->create([
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'name' => $_POST['name'] ?? '',
                'role' => $role,
            ]);
            Auth::login($created);
            Response::redirect('/dashboard');
        } catch (Throwable $e) {
            Flash::error($e->getMessage());
            Response::redirect('/register');
        }
    }

    public function logout(): void
    {
        Auth::logout();
        Response::redirect('/login');
    }

    public function profile(): void
    {
        Auth::require();
        View::render('auth/profile', ['title' => 'Profile', 'user' => (new User())->findById(Auth::id())]);
    }

    public function updateProfile(): void
    {
        Auth::require();
        Security::verifyCsrf();
        $model = new User();
        $user = $model->findById(Auth::id());
        $data = ['name' => trim((string) ($_POST['name'] ?? '')) ?: null];
        if (!empty($_FILES['avatar']['name'])) {
            $data['avatar_url'] = $this->uploadAvatar((int) $user['id']);
        }
        $newPassword = (string) ($_POST['newPassword'] ?? '');
        if ($newPassword !== '') {
            $current = (string) ($_POST['currentPassword'] ?? '');
            if ($current === '' || !password_verify($current, $user['password_hash'])) {
                Flash::error($current === '' ? 'Current password is required to set a new password' : 'Current password is incorrect');
                Response::redirect('/profile');
            }
            if (strlen($newPassword) < 6) {
                Flash::error('New password must be at least 6 characters');
                Response::redirect('/profile');
            }
            $data['password'] = $newPassword;
        }
        $model->update((int) $user['id'], $data);
        $_SESSION['user'] = [
            ...$_SESSION['user'],
            'name' => $data['name'],
            'avatar_url' => $data['avatar_url'] ?? $_SESSION['user']['avatar_url'] ?? null,
        ];
        Flash::success('Profile updated.');
        Response::redirect('/profile');
    }

    public function apiLogin(): void
    {
        $body = $this->jsonBody();
        $email = strtolower(trim((string) ($body['email'] ?? '')));
        $password = (string) ($body['password'] ?? '');
        if ($email === '' || $password === '') {
            Response::json(['message' => 'Email and password are required'], 400);
        }
        $user = (new User())->findByEmail($email);
        if (!$user || (int) $user['is_active'] !== 1 || !password_verify($password, $user['password_hash'])) {
            Response::json(['message' => 'Invalid credentials'], 401);
        }
        $token = Auth::login($user, true) ?? bin2hex(random_bytes(32));
        Response::json(['token' => $token, 'user' => (new User())->serialize($user)]);
    }

    public function apiRegister(): void
    {
        $body = $this->jsonBody();
        if (empty($body['email']) || empty($body['password'])) {
            Response::json(['message' => 'Email and password are required'], 400);
        }
        $model = new User();
        $requestedRole = ($body['role'] ?? 'staff') === 'admin' ? 'admin' : 'staff';
        if ($model->count() > 0) {
            Auth::requireApiRole('admin');
        }
        $role = $model->count() === 0 ? ($requestedRole === 'staff' ? 'staff' : 'admin') : $requestedRole;
        try {
            $created = $model->create($body + ['role' => $role]);
            if ($model->count() === 1) {
                $token = Auth::login($created, true);
                Response::json(['token' => $token, 'user' => $model->serialize($created)], 201);
            }
            Response::json($model->serialize($created), 201);
        } catch (Throwable $e) {
            Response::json(['message' => $e->getMessage()], 400);
        }
    }

    public function apiMe(): void
    {
        $user = Auth::apiUser();
        if (!$user) {
            Response::json(['message' => 'Missing Authorization token'], 401);
        }
        Response::json(['user' => (new User())->serialize($user)]);
    }

    public function apiUsers(): void
    {
        Auth::requireApiRole('admin');
        $items = array_map([(new User()), 'serialize'], (new User())->list(['role' => $_GET['role'] ?? '']));
        Response::json(['items' => $items]);
    }

    public function apiProfile(): void
    {
        $apiUser = Auth::requireApiRole('admin', 'staff');
        $model = new User();
        $user = $model->findById((int) $apiUser['id']);
        if (!$user) {
            Response::json(['message' => 'User not found'], 404);
        }

        $body = $_POST ?: $this->jsonBody();
        $data = [];
        if (array_key_exists('name', $body)) {
            $data['name'] = trim((string) $body['name']) ?: null;
        }
        if (!empty($_FILES['avatar']['name'])) {
            $data['avatar_url'] = $this->uploadAvatar((int) $user['id']);
        }

        $newPassword = (string) ($body['newPassword'] ?? '');
        if ($newPassword !== '') {
            $current = (string) ($body['currentPassword'] ?? '');
            if ($current === '') {
                Response::json(['message' => 'Current password is required to set a new password'], 400);
            }
            if (!password_verify($current, $user['password_hash'])) {
                Response::json(['message' => 'Current password is incorrect'], 400);
            }
            if (strlen($newPassword) < 6) {
                Response::json(['message' => 'New password must be at least 6 characters'], 400);
            }
            $data['password'] = $newPassword;
        }

        $model->update((int) $user['id'], $data);
        Response::json(['user' => $model->serialize($model->findById((int) $user['id']))]);
    }

    private function uploadAvatar(int $userId): string
    {
        $file = $_FILES['avatar'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed');
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException('File exceeds 2 MB');
        }
        $mime = mime_content_type($file['tmp_name']) ?: '';
        if (!str_starts_with($mime, 'image/')) {
            throw new RuntimeException('Only image files are allowed');
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $ext = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? $ext : 'jpg';
        $name = $userId . '-' . time() . '.' . $ext;
        $dir = __DIR__ . '/../assets/uploads/avatars';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        move_uploaded_file($file['tmp_name'], $dir . '/' . $name);
        return '/assets/uploads/avatars/' . $name;
    }

    private function jsonBody(): array
    {
        return json_decode(file_get_contents('php://input') ?: '[]', true) ?: [];
    }
}
