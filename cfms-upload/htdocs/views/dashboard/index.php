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
