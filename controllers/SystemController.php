<?php

declare(strict_types=1);

final class SystemController
{
    public function audit(): void
    {
        Auth::requireRole('admin');
        $entries = (new AuditLog())->latest(200);
        View::render('system/audit', ['title' => 'Audit Trail', 'entries' => $entries]);
    }

    public function integrations(): void
    {
        Auth::requireRole('admin');
        View::render('system/integrations', ['title' => 'Integrations']);
    }

    public function maintenance(): void
    {
        Auth::requireRole('admin');
        $db = db();
        $stats = [];
        foreach (['feedback', 'users', 'contacts', 'audit_logs', 'response_templates'] as $table) {
            $result = mysqli_query($db, "SELECT COUNT(*) FROM $table");
            $row = mysqli_fetch_row($result);
            $stats[$table] = (int) $row[0];
        }
        try {
            $result = mysqli_query($db, 'SELECT VERSION()');
            $row = mysqli_fetch_row($result);
            $stats['mysql_version'] = $row[0];
        } catch (Throwable) {
            $stats['mysql_version'] = 'unknown';
        }
        $stats['php_version'] = PHP_VERSION;
        View::render('system/maintenance', ['title' => 'Maintenance', 'stats' => $stats]);
    }

    public function templates(): void
    {
        Auth::requireRole('admin');
        $category = trim((string) ($_GET['category'] ?? ''));
        $templates = (new ResponseTemplate())->all($category);
        $categories = (new ResponseTemplate())->categories();
        View::render('system/templates', ['title' => 'Response Templates', 'templates' => $templates, 'categories' => $categories]);
    }

    public function storeTemplate(): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        try {
            (new ResponseTemplate())->create($_POST);
            Flash::success('Template created.');
        } catch (Throwable $e) {
            Flash::error($e->getMessage());
        }
        Response::redirect('/system/templates');
    }

    public function deleteTemplate(int $id): void
    {
        Auth::requireRole('admin');
        Security::verifyCsrf();
        (new ResponseTemplate())->delete($id);
        Flash::success('Template deleted.');
        Response::redirect('/system/templates');
    }
}
