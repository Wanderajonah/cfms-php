<?php $items = $data['items'] ?? $recent ?? []; ?>
<div class="table-responsive">
<table class="table align-middle">
    <thead><tr><th>Ticket</th><th>Customer</th><th>Branch</th><th>Category</th><th>Type</th><th>Status</th><th>Priority</th><th>Created</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>#<?= Security::e((string) $item['ticketNumber']) ?></td>
            <td><strong><?= Security::e($item['name'] ?: 'Anonymous') ?></strong><div class="text-secondary small"><?= Security::e($item['email'] ?: $item['phone']) ?></div></td>
            <td><?= Security::e($item['branchName'] ?? ($item['branch_name'] ?? '-')) ?></td>
            <td><?= Security::e($item['category']) ?></td>
            <td><?= Security::e($item['type']) ?></td>
            <td><span class="badge status-<?= Security::e($item['status']) ?>"><?= Security::e($item['status']) ?></span></td>
            <td><span class="badge priority-<?= Security::e($item['priority']) ?>"><?= Security::e($item['priority']) ?></span></td>
            <td><?= Security::e(substr($item['createdAt'], 0, 10)) ?></td>
            <td><a class="btn btn-sm btn-outline-primary" href="/feedback/<?= (int) $item['id'] ?>"><i class="bi bi-eye"></i></a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
