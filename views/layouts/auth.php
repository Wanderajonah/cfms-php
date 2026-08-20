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
            <img src="/assets/uploads/restaurant/logo-white.png" alt="Cafe Javas" class="auth-brand-logo">
            <div class="auth-brand-text">
                <p class="auth-brand-name">Customer Feedback</p>
                <p class="auth-brand-sub">Management System</p>
            </div>
        </div>

        <div class="auth-brand-center">
            <div class="auth-brand-image-wrap">
                <img src="/assets/uploads/restaurant/login-cover.jpg" alt="Cafe Javas" class="auth-brand-image">
            </div>
            <p class="auth-brand-desc">Collect, track, and respond to customer feedback to deliver exceptional dining experiences.</p>
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