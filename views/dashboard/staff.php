<?php $totals = $summary['totals']; ?>
<div class="stats-grid">
    <?php foreach ([['My Tasks', $totals['total'], 'primary'], ['Pending', $totals['pending'], 'warning'], ['In Progress', $totals['inProgress'], 'info'], ['Resolved', $totals['resolved'], 'success'], ['Escalated', $totals['escalated'], 'danger']] as $card): ?>
        <section class="stat-card"><span class="text-<?= $card[2] ?>"><?= $card[0] ?></span><strong><?= $card[1] ?></strong></section>
    <?php endforeach; ?>
</div>
<div class="row g-4 mt-1">
    <div class="col-12">
        <section class="panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">My Assigned Feedback</h2>
                <a href="/feedback" class="btn btn-sm btn-outline-primary">View all</a>
            </div>
            <?php if ($recent): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>#</th><th>Customer</th><th>Category</th><th>Type</th><th>Status</th><th>Priority</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach ($recent as $item): ?>
                                <tr>
                                    <td>#<?= $item['ticketNumber'] ?></td>
                                    <td><strong><?= Security::e($item['name'] ?: 'Anonymous') ?></strong></td>
                                    <td><?= Security::e($item['category']) ?></td>
                                    <td><?= Security::e($item['type']) ?></td>
                                    <td><span class="badge status-<?= $item['status'] ?>"><?= $item['status'] ?></span></td>
                                    <td><span class="badge priority-<?= $item['priority'] ?>"><?= $item['priority'] ?></span></td>
                                    <td><?= date('Y-m-d', strtotime($item['created_at'])) ?></td>
                                    <td><a class="btn btn-sm btn-outline-primary" href="/feedback/<?= $item['id'] ?>"><i class="bi bi-eye"></i></a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No feedback assigned yet.</p>
            <?php endif; ?>
        </section>
    </div>
</div>
