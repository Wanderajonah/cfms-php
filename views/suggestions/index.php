<section class="panel mb-4">
    <form class="row g-2">
        <div class="col-md-3"><input name="search" value="<?= Security::e($filters['search'] ?? '') ?>" class="form-control" placeholder="Search suggestions"></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">All statuses</option><?php foreach (Feedback::STATUSES as $s): ?><option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select name="category" class="form-select"><option value="">All categories</option><?php foreach (Feedback::CATEGORIES as $c): ?><option <?= ($filters['category'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select name="priority" class="form-select"><option value="">All priorities</option><?php foreach (Feedback::PRIORITIES as $p): ?><option value="<?= $p ?>" <?= ($filters['priority'] ?? '') === $p ? 'selected' : '' ?>><?= $p ?></option><?php endforeach; ?></select></div>
        <div class="col-md-2"><select name="branch_id" class="form-select"><option value="">All branches</option><?php foreach ($branches as $b): ?><option value="<?= (int) $b['id'] ?>" <?= ($filters['branch_id'] ?? '') == $b['id'] ? 'selected' : '' ?>><?= Security::e($b['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-1"><button class="btn btn-info"><i class="bi bi-search"></i></button></div>
    </form>
</section>

<section class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0"><i class="bi bi-lightbulb text-info"></i> Suggestions</h2>
        <span class="badge bg-info"><?= (int) $data['total'] ?> total</span>
    </div>
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
