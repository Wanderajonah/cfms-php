<header class="topbar">
    <button class="btn btn-outline-secondary d-lg-none" data-toggle-sidebar><i class="bi bi-list"></i></button>
    <div>
        <h1 class="h4 mb-0"><?= Security::e($title ?? 'Dashboard') ?></h1>
        <small class="text-secondary">Customer Feedback Management System</small>
    </div>
    <div class="ms-auto d-flex align-items-center gap-3">
        <a href="/profile" class="text-decoration-none text-dark d-flex align-items-center gap-2">
            <span class="avatar"><?= strtoupper(substr(Auth::user()['name'] ?: Auth::user()['email'], 0, 1)) ?></span>
            <span class="d-none d-md-inline"><?= Security::e(Auth::user()['name'] ?: Auth::user()['email']) ?></span>
        </a>
        <form method="post" action="/logout">
            <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i></button>
        </form>
    </div>
</header>
