<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Security::e($title ?? 'Auth') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body style="margin:0;background:#faf8f6">
<div class="auth-split">
    <div class="auth-brand-panel">
        <div class="auth-brand-header">
            <div class="auth-brand-icon">
                <i class="bi bi-cup-hot-fill"></i>
            </div>
            <div>
                <p class="auth-brand-name">Cafe Javas</p>
                <p class="auth-brand-location">Kampala, Uganda</p>
            </div>
        </div>

        <div class="auth-brand-center">
            <div class="auth-brand-image-wrap">
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&h=350&fit=crop&auto=format" alt="Restaurant food" class="auth-brand-image">
                <div class="auth-badge auth-badge-rating">
                    <i class="bi bi-star-fill" style="color:#f59e0b;font-size:14px"></i>
                    <span>4.8 Rating</span>
                </div>
                <div class="auth-badge auth-badge-reviews">
                    <i class="bi bi-chat-dots" style="color:var(--brand-600);font-size:14px"></i>
                    <span>2.4K Reviews</span>
                </div>
            </div>
            <h2 class="auth-brand-title">Customer Feedback</h2>
            <p class="auth-brand-desc">Management Dashboard — Collect, track, and respond to customer feedback to deliver exceptional dining experiences.</p>
            <div class="auth-stats">
                <div><strong>2</strong><span>Roles</span></div>
                <div><strong>3</strong><span>Modules</span></div>
                <div><strong>98%</strong><span>Uptime</span></div>
            </div>
        </div>

        <p class="auth-brand-footer">Cafe Javas &middot; Kampala, Uganda</p>
    </div>

    <div class="auth-form-panel">
        <?= $content ?>
    </div>
</div>
<?php require __DIR__ . '/../partials/toasts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>