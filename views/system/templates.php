<div class="row g-4">
    <div class="col-md-5">
        <section class="panel">
            <h2 class="h5 mb-3">New Template</h2>
            <form method="post" action="/system/templates">
                <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
                <div class="mb-3">
                    <label class="form-label small fw-medium">Title</label>
                    <input name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Category</label>
                    <select name="category" class="form-select">
                        <option value="">General</option>
                        <option value="complaint">Complaint</option>
                        <option value="compliment">Compliment</option>
                        <option value="suggestion">Suggestion</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-medium">Body</label>
                    <textarea name="body" class="form-control" rows="5" required></textarea>
                </div>
                <button class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Create Template</button>
            </form>
        </section>
    </div>
    <div class="col-md-7">
        <section class="panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Saved Templates</h2>
                <form method="get" class="d-flex gap-2">
                    <select name="category" class="form-select form-select-sm" style="width:140px" onchange="this.form.submit()">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= Security::e($c) ?>" <?= ($_GET['category'] ?? '') === $c ? 'selected' : '' ?>><?= Security::e(ucfirst($c)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <?php if ($templates): ?>
                <div class="accordion accordion-flush" id="templatesAccordion">
                    <?php foreach ($templates as $i => $t): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#t<?= $t['id'] ?>">
                                    <span><?= Security::e($t['title']) ?></span>
                                    <?php if ($t['category']): ?><span class="badge bg-secondary ms-2"><?= Security::e($t['category']) ?></span><?php endif; ?>
                                </button>
                            </h2>
                            <div id="t<?= $t['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#templatesAccordion">
                                <div class="accordion-body">
                                    <pre style="white-space:pre-wrap;font-size:13px;background:#f8fafc;padding:12px;border-radius:6px"><?= Security::e($t['body']) ?></pre>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted">by <?= Security::e($t['created_by_name'] ?? '—') ?></small>
                                        <form method="post" action="/system/templates/<?= $t['id'] ?>/delete" onsubmit="return confirm('Delete this template?')">
                                            <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No templates yet.</p>
            <?php endif; ?>
        </section>
    </div>
</div>
