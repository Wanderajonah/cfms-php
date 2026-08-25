<?php $totals = $summary['totals']; ?>
<div class="stats-grid">
    <?php foreach ([['Total', $totals['total'], 'primary'], ['Pending', $totals['pending'], 'warning'], ['Resolved', $totals['resolved'], 'success'], ['Escalated', $totals['escalated'], 'danger']] as $card): ?>
        <section class="stat-card"><span class="text-<?= $card[2] ?>"><?= $card[0] ?></span><strong><?= $card[1] ?></strong></section>
    <?php endforeach; ?>
</div>
<div class="row g-4 mt-1">
    <div class="col-xl-8"><section class="panel"><h2 class="h5">Monthly Feedback</h2><canvas id="monthlyChart" data-chart='<?= json_encode($summary['monthly']) ?>' height="120"></canvas></section></div>
    <div class="col-xl-4"><section class="panel"><h2 class="h5">Categories</h2><canvas id="categoryChart" data-chart='<?= json_encode($summary['categories']) ?>' height="220"></canvas></section></div>
    <div class="col-xl-8"><section class="panel"><div class="d-flex justify-content-between"><h2 class="h5">Recent Feedback</h2><a href="/feedback">View all</a></div><?php require __DIR__ . '/../../views/feedback/table.php'; ?></section></div>
    <div class="col-xl-4"><section class="panel"><h2 class="h5">Recent Activity</h2><div class="timeline"><?php foreach ($activity as $a): ?><div><strong><?= Security::e($a['action']) ?></strong><span><?= Security::e($a['description']) ?></span><small><?= Security::e($a['created_at']) ?></small></div><?php endforeach; ?></div></section></div>
</div>

<?php if (!empty($staffAssignments)): ?>
<div class="row g-4 mt-1">
    <div class="col-12">
        <section class="panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0"><i class="bi bi-people me-2"></i>Staff Task Assignments</h2>
                <a href="/users" class="btn btn-sm btn-outline-primary">Manage staff</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Staff Member</th><th>Total</th><th>Pending</th><th>In Progress</th><th>Resolved</th><th>Escalated</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($staffAssignments as $u):
                        $semail = $u['email'] ?? '';
                        $stats = $u['assignmentStats'] ?? ['total' => 0, 'pending' => 0, 'inProgress' => 0, 'resolved' => 0, 'escalated' => 0];
                    ?>
                        <tr>
                            <td><strong><?= Security::e($u['name'] ?? $semail) ?></strong><br><small class="text-muted"><?= Security::e($semail) ?></small></td>
                            <td><span class="badge bg-primary"><?= $stats['total'] ?></span></td>
                            <td><span class="badge bg-warning text-dark"><?= $stats['pending'] ?></span></td>
                            <td><span class="badge bg-info text-dark"><?= $stats['inProgress'] ?></span></td>
                            <td><span class="badge bg-success"><?= $stats['resolved'] ?></span></td>
                            <td><span class="badge bg-danger"><?= $stats['escalated'] ?></span></td>
                            <td><a href="/feedback?assignedTo=<?= urlencode($semail) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php endif; ?>
