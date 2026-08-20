<div class="row g-4">
    <div class="col-xl-5">
        <section class="panel">
            <h2 class="h5 mb-3">System Settings</h2>
            <form method="post" action="/settings">
                <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
                <label class="form-label">System name</label><input name="system_name" value="<?= Security::e($settings['system_name'] ?? '') ?>" class="form-control mb-3">
                <label class="form-label">Logo URL</label><input name="logo" value="<?= Security::e($settings['logo'] ?? '') ?>" class="form-control mb-3">
                <label class="form-label">Email from</label><input type="email" name="email_from" value="<?= Security::e($settings['email_from'] ?? '') ?>" class="form-control mb-3">
                <label class="form-label">Response threshold hours</label><input type="number" name="response_threshold_hours" value="<?= Security::e($settings['response_threshold_hours'] ?? '24') ?>" class="form-control mb-4">
                <button class="btn btn-primary">Save settings</button>
            </form>
        </section>
    </div>
    <div class="col-xl-7">
        <section class="panel">
            <h2 class="h5 mb-3">Audit Logs</h2>
            <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Action</th><th>User</th><th>Entity</th><th>When</th></tr></thead><tbody>
                <?php foreach ($logs as $log): ?><tr><td><?= Security::e($log['action']) ?></td><td><?= Security::e($log['user_name'] ?: $log['user_email']) ?></td><td><?= Security::e($log['entity_type']) ?> #<?= Security::e((string) $log['entity_id']) ?></td><td><?= Security::e($log['created_at']) ?></td></tr><?php endforeach; ?>
            </tbody></table></div>
        </section>
    </div>
</div>
