<section class="panel">
    <h2 class="h5 mb-3">Audit Trail</h2>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Entity</th><th>Description</th><th>IP</th></tr></thead>
            <tbody>
                <?php foreach ($entries as $e): ?>
                    <tr>
                        <td class="text-nowrap"><?= Security::e($e['created_at']) ?></td>
                        <td><?= Security::e($e['user_name'] ?? 'System') ?></td>
                        <td><span class="badge bg-secondary"><?= Security::e($e['action']) ?></span></td>
                        <td><?= Security::e($e['entity_type'] ?? '-') ?> #<?= $e['entity_id'] ?? '-' ?></td>
                        <td><?= Security::e($e['description'] ?? '') ?></td>
                        <td style="font-size:12px;color:var(--muted)"><?= Security::e($e['ip_address'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
