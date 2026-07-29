<section class="panel mb-4">
    <form class="row g-2">
        <div class="col-md-2"><input type="date" name="date_from" value="<?= Security::e($filters['date_from'] ?? '') ?>" class="form-control"></div>
        <div class="col-md-2"><input type="date" name="date_to" value="<?= Security::e($filters['date_to'] ?? '') ?>" class="form-control"></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">Status</option><?php foreach (Feedback::STATUSES as $s): ?><option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select name="category" class="form-select"><option value="">Category</option><?php foreach (Feedback::CATEGORIES as $c): ?><option <?= ($filters['category'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary">Generate</button><a class="btn btn-outline-success" href="/reports/export/csv?<?= http_build_query($filters) ?>"><i class="bi bi-filetype-csv"></i> CSV</a><a class="btn btn-outline-danger" href="/reports/export/pdf?<?= http_build_query($filters) ?>"><i class="bi bi-filetype-pdf"></i> PDF</a></div>
    </form>
</section>
<div class="stats-grid mb-4">
    <section class="stat-card"><span>Total</span><strong><?= (int) $data['total'] ?></strong></section>
    <section class="stat-card"><span>Avg Response Hours</span><strong><?= Security::e((string) ($summary['avgResponseHours'] ?? 'N/A')) ?></strong></section>
    <section class="stat-card"><span>Resolved</span><strong><?= (int) $summary['totals']['resolved'] ?></strong></section>
    <section class="stat-card"><span>Escalated</span><strong><?= (int) $summary['totals']['escalated'] ?></strong></section>
</div>
<section class="panel"><?php require __DIR__ . '/../feedback/table.php'; ?></section>
