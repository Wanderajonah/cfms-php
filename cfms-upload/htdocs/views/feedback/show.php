<div class="row g-4">
    <div class="col-xl-8">
        <section class="panel">
            <div class="d-flex justify-content-between align-items-start">
                <div><h2 class="h4">Ticket #<?= Security::e((string) $item['ticketNumber']) ?></h2><p class="text-secondary mb-0"><?= Security::e($item['name'] ?: 'Anonymous') ?> · <?= Security::e($item['email'] ?: $item['phone']) ?></p></div>
                <span class="badge status-<?= Security::e($item['status']) ?>"><?= Security::e($item['status']) ?></span>
            </div>
            <hr>
            <p><?= nl2br(Security::e($item['message'])) ?></p>
            <?php if ($item['response']): ?><div class="alert alert-success"><strong>Response:</strong><br><?= nl2br(Security::e($item['response'])) ?></div><?php endif; ?>
            <div class="row g-2 text-secondary small">
                <div class="col-md-3">Branch: <?= Security::e($item['branchName'] ?? '-') ?></div>
                <div class="col-md-3">Category: <?= Security::e($item['category']) ?></div>
                <div class="col-md-3">Type: <?= Security::e($item['type']) ?></div>
                <div class="col-md-3">Priority: <?= Security::e($item['priority']) ?></div>
                <div class="col-md-3">Rating: <?= Security::e((string) $item['rating']) ?></div>
            </div>
        </section>
    </div>
    <div class="col-xl-4">
        <?php if ((Auth::user()['role_slug'] ?? '') === 'admin'): ?>
        <section class="panel mb-4">
            <h3 class="h5">Assign</h3>
            <form method="post" action="/feedback/<?= (int) $item['id'] ?>/assign">
                <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
                <select name="assignedTo" class="form-select mb-3"><option value="">Unassigned</option><?php foreach ($staff as $s): ?><option value="<?= Security::e($s['email']) ?>" <?= $item['assignedTo'] === $s['email'] ? 'selected' : '' ?>><?= Security::e($s['name'] ?: $s['email']) ?></option><?php endforeach; ?></select>
                <button class="btn btn-primary w-100">Assign</button>
            </form>
        </section>
        <?php endif; ?>
        <section class="panel mb-4">
            <h3 class="h5">Respond</h3>
            <form method="post" action="/feedback/<?= (int) $item['id'] ?>/respond">
                <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
                <textarea name="response" class="form-control mb-3" rows="4" required><?= Security::e($item['response']) ?></textarea>
                <button class="btn btn-success w-100">Save response</button>
            </form>
        </section>
        <section class="panel">
            <h3 class="h5">Workflow</h3>
            <form method="post" action="/feedback/<?= (int) $item['id'] ?>/status" class="d-grid gap-2">
                <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
                <button name="action" value="resolve" class="btn btn-outline-success">Resolve</button>
                <button name="action" value="escalate" class="btn btn-outline-danger">Escalate</button>
                <button name="action" value="reopen" class="btn btn-outline-warning">Reopen</button>
            </form>
            <?php if ((Auth::user()['role_slug'] ?? '') === 'admin'): ?>
                <form method="post" action="/feedback/<?= (int) $item['id'] ?>/delete" class="mt-3" data-confirm="Delete this feedback?"><input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>"><button class="btn btn-danger w-100">Delete</button></form>
            <?php endif; ?>
        </section>
    </div>
</div>
