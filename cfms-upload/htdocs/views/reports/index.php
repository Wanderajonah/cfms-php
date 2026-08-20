<section class="panel mb-4">
    <form class="row g-2">
        <div class="col-md-2"><input type="date" name="date_from" value="<?= Security::e($filters['date_from'] ?? '') ?>" class="form-control"></div>
        <div class="col-md-2"><input type="date" name="date_to" value="<?= Security::e($filters['date_to'] ?? '') ?>" class="form-control"></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">Status</option><?php foreach (Feedback::STATUSES as $s): ?><option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select name="category" class="form-select"><option value="">Category</option><?php foreach (Feedback::CATEGORIES as $c): ?><option <?= ($filters['category'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4 d-flex gap-2"><button class="btn btn-primary">Generate</button>
            <form method="post" action="/reports/export/csv?<?= http_build_query($filters) ?>" class="d-inline"><input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>"><button class="btn btn-outline-success"><i class="bi bi-filetype-csv"></i> CSV</button></form>
            <form method="post" action="/reports/export/pdf?<?= http_build_query($filters) ?>" class="d-inline"><input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>"><button class="btn btn-outline-danger"><i class="bi bi-filetype-pdf"></i> PDF</button></form>
        </div>
    </form>
</section>
<div class="stats-grid mb-4">
    <section class="stat-card"><span>Total</span><strong><?= (int) $data['total'] ?></strong></section>
    <section class="stat-card"><span>Avg Response Hours</span><strong><?= Security::e((string) ($summary['avgResponseHours'] ?? 'N/A')) ?></strong></section>
    <section class="stat-card"><span>Resolved</span><strong><?= (int) $summary['totals']['resolved'] ?></strong></section>
    <section class="stat-card"><span>Escalated</span><strong><?= (int) $summary['totals']['escalated'] ?></strong></section>
</div>
<section class="panel">
    <?php require __DIR__ . '/../feedback/table.php'; ?>
    <nav><ul class="pagination mb-0 mt-3">
        <?php
        $tp = $data['totalPages'];
        $cp = $data['page'];
        $range = 2;
        $pages = [];
        for ($i = 1; $i <= $tp; $i++) {
            if ($i === 1 || $i === $tp || ($i >= $cp - $range && $i <= $cp + $range)) {
                $pages[] = $i;
            } elseif (end($pages) !== '…') {
                $pages[] = '…';
            }
        }
        foreach ($pages as $p): if ($p === '…'): ?><li class="page-item disabled"><span class="page-link">…</span></li><?php else: ?><li class="page-item <?= $p === $cp ? 'active' : '' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $p])) ?>"><?= $p ?></a></li><?php endif; endforeach; ?>
    </ul></nav>
</section>
