<?php

declare(strict_types=1);

final class SettingController
{
    public function index(): void
    {
        Auth::requireRole('admin');
        View::render('settings/index', [
            'title' => 'Settings',
            'settings' => (new Setting())->all(),
            'logs' => (new AuditLog())->latest(100),
        ]);
    }

    public function save(): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        (new Setting())->save([
            'system_name' => $_POST['system_name'] ?? '',
            'email_from' => $_POST['email_from'] ?? '',
            'response_threshold_hours' => $_POST['response_threshold_hours'] ?? '24',
            'logo' => $_POST['logo'] ?? '',
        ]);
        (new AuditLog())->record(Auth::id(), 'update', 'settings', null, 'Settings updated');
        Flash::success('Settings saved.');
        Response::redirect('/settings');
    }
}
