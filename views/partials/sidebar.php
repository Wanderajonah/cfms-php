<?php $user = Auth::user(); $role = $user['role_slug'] ?? ''; $name = $user['name'] ?? $user['email'] ?? ''; $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); $is = static fn (string $p) => str_starts_with($path, $p); ?>
<aside class="sidebar">
    <div class="sidebar-top">
        <a href="/dashboard" class="brand"><span class="brand-mark">CJ</span><span class="brand-text">Cafe Javas</span></a>
        <div class="sidebar-divider"></div>
        <nav class="nav flex-column gap-1">
            <div class="nav-section-title">Main</div>
            <a class="nav-link <?= $path === '/dashboard' ? 'active' : '' ?>" href="/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="nav-link <?= $is('/inbox') ? 'active' : '' ?>" href="/inbox"><i class="bi bi-inbox"></i> Inbox</a>
            <a class="nav-link <?= $is('/feedback') && !$is('/feedback/submit') && !$is('/feedback/track') ? 'active' : '' ?>" href="/feedback"><i class="bi bi-chat-square-text"></i> Feedback</a>
            <a class="nav-link <?= $is('/customers') ? 'active' : '' ?>" href="/customers"><i class="bi bi-people"></i> Customers</a>

            <div class="nav-section-title">Insights</div>
            <a class="nav-link <?= $is('/analytics') ? 'active' : '' ?>" href="/analytics"><i class="bi bi-bar-chart-line"></i> Analytics</a>
            <a class="nav-link <?= $is('/activity') ? 'active' : '' ?>" href="/activity"><i class="bi bi-activity"></i> Activity</a>

            <?php if ($role === 'admin'): ?>
                <div class="nav-section-title">Management</div>
                <a class="nav-link <?= $is('/reports') ? 'active' : '' ?>" href="/reports"><i class="bi bi-bar-chart"></i> Reports</a>
                <a class="nav-link <?= $is('/users') ? 'active' : '' ?>" href="/users"><i class="bi bi-person-badge"></i> Users</a>
                <a class="nav-link <?= $is('/contacts') ? 'active' : '' ?>" href="/contacts"><i class="bi bi-person-lines-fill"></i> Contacts</a>
                <a class="nav-link <?= $is('/system/templates') ? 'active' : '' ?>" href="/system/templates"><i class="bi bi-file-text"></i> Templates</a>
                <a class="nav-link <?= $is('/system/audit') ? 'active' : '' ?>" href="/system/audit"><i class="bi bi-journal-text"></i> Audit Trail</a>

                <div class="nav-section-title">System</div>
                <a class="nav-link <?= $is('/system/integrations') ? 'active' : '' ?>" href="/system/integrations"><i class="bi bi-plug"></i> Integrations</a>
                <a class="nav-link <?= $is('/system/maintenance') ? 'active' : '' ?>" href="/system/maintenance"><i class="bi bi-gear-wide-connected"></i> Maintenance</a>
                <a class="nav-link <?= $is('/settings') ? 'active' : '' ?>" href="/settings"><i class="bi bi-gear"></i> Settings</a>
            <?php endif; ?>
        </nav>
    </div>
    <div class="sidebar-profile">
        <div class="sidebar-divider"></div>
        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= strtoupper(substr($name, 0, 1)) ?></div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?= Security::e($name) ?></span>
                <span class="sidebar-user-role"><?= Security::e(ucfirst($role)) ?></span>
            </div>
        </div>
        <form method="post" action="/logout" class="sidebar-logout-form">
            <button class="sidebar-logout" title="Sign out"><i class="bi bi-box-arrow-right"></i></button>
        </form>
    </div>
</aside>
