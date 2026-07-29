<div class="row g-4">
    <div class="col-md-6">
        <section class="panel">
            <h2 class="h5 mb-3">Database Tables</h2>
            <div class="table-responsive">
                <table class="table table-sm small mb-0">
                    <thead><tr><th>Table</th><th>Records</th></tr></thead>
                    <tbody>
                        <?php foreach (['feedback', 'users', 'contacts', 'audit_logs', 'response_templates'] as $t): ?>
                            <tr><td><?= $t ?></td><td><strong><?= $stats[$t] ?? 0 ?></strong></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <div class="col-md-6">
        <section class="panel">
            <h2 class="h5 mb-3">System Info</h2>
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">PHP Version</dt>
                <dd class="col-7"><?= Security::e($stats['php_version'] ?? '') ?></dd>
                <dt class="col-5 text-muted">MySQL Version</dt>
                <dd class="col-7"><?= Security::e($stats['mysql_version'] ?? '') ?></dd>
                <dt class="col-5 text-muted">Environment</dt>
                <dd class="col-7">Development</dd>
                <dt class="col-5 text-muted">Timezone</dt>
                <dd class="col-7"><?= Security::e(date_default_timezone_get()) ?></dd>
            </dl>
        </section>
        <section class="panel mt-3">
            <h2 class="h5 mb-2">Actions</h2>
            <p class="small text-muted mb-3">Maintenance operations for the system.</p>
            <form method="post" action="/system/maintenance/optimize" class="d-inline">
                <button class="btn btn-sm btn-outline-warning" onclick="return confirm('Optimize database tables?')"><i class="bi bi-database-gear"></i> Optimize Tables</button>
            </form>
        </section>
    </div>
</div>
