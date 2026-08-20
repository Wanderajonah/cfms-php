<?php $items = $data['items'] ?? []; ?>
<section class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Pending Feedback</h2>
        <span class="badge bg-warning text-dark"><?= count($items) ?> items</span>
    </div>
    <?php if ($items): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>#</th><th>Customer</th><th>Category</th><th>Type</th><th>Priority</th><th>Date</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>#<?= $item['ticketNumber'] ?></td>
                            <td><strong><?= Security::e($item['name'] ?: 'Anonymous') ?></strong></td>
                            <td><?= Security::e($item['category']) ?></td>
                            <td><?= Security::e($item['type']) ?></td>
                            <td><span class="badge priority-<?= $item['priority'] ?>"><?= $item['priority'] ?></span></td>
                            <td><?= date('Y-m-d', strtotime($item['created_at'])) ?></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="/feedback/<?= $item['id'] ?>"><i class="bi bi-eye"></i> Respond</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($data['totalPages'] > 1): ?>
            <nav><ul class="pagination mb-0 mt-3">
                <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                    <li class="page-item <?= $i === $data['page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul></nav>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5"><i class="bi bi-inbox" style="font-size:48px;color:#cbd5e1"></i><p class="text-muted mt-2 mb-0">All caught up! No pending feedback.</p></div>
    <?php endif; ?>
</section>
