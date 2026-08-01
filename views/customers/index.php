<section class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Customer Directory</h2>
        <form method="get" class="d-flex gap-2">
            <input name="search" value="<?= Security::e($_GET['search'] ?? '') ?>" class="form-control form-control-sm" placeholder="Search customers..." style="width:220px">
            <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <?php if ($customers): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Branch</th><th>Feedbacks</th><th>Last Activity</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><strong><?= Security::e($c['name'] ?: 'Anonymous') ?></strong></td>
                            <td><?= Security::e($c['email'] ?? '-') ?></td>
                            <td><?= Security::e($c['phone'] ?? '-') ?></td>
                            <td><?= Security::e($c['branch'] ?? '-') ?></td>
                            <td><span class="badge bg-secondary"><?= (int) $c['feedback_count'] ?></span></td>
                            <td><?= date('Y-m-d', strtotime($c['last_feedback'])) ?></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="/feedback?search=<?= urlencode($c['name'] ?? $c['email'] ?? '') ?>"><i class="bi bi-chat-square-text"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted mb-0">No customers found.</p>
    <?php endif; ?>
</section>
