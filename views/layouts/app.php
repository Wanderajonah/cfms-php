<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Security::e($title ?? 'Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>
<div class="app-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="app-main">
        <?php require __DIR__ . '/../partials/topbar.php'; ?>
        <main class="content-wrap">
            <?= $content ?>
        </main>
    </div>
</div>
<?php require __DIR__ . '/../partials/toasts.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
