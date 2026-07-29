<section class="panel">
    <h2 class="h5 mb-3"><?= !empty($personal) ? 'My Account Activity' : 'Recent Activity' ?></h2>
    <?php if ($entries): ?>
        <div class="timeline">
            <?php foreach ($entries as $e): ?>
                <div class="activity-item">
                    <div class="activity-dot"></div>
                    <div class="activity-body">
                        <strong><?= Security::e($e['user_name'] ?? 'System') ?></strong>
                        <span><?= Security::e($e['description'] ?? $e['action']) ?></span>
                        <small><?= Security::e($e['created_at']) ?></small>
                        <?php if (!empty($personal)): ?>
                            <div class="activity-meta">
                                <span class="activity-ip"><?= Security::e($e['ip_address'] ?? '—') ?></span>
                                <span class="activity-device"><?= Security::e($e['user_agent'] ? preg_replace('/\s+/', ' ', substr($e['user_agent'], 0, 80)) : '—') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted mb-0">No activity recorded.</p>
    <?php endif; ?>
</section>
