<div class="toast-container position-fixed top-0 end-0 p-3">
    <?php foreach (Flash::all() as $flash): ?>
        <div class="toast align-items-center text-bg-<?= Security::e($flash['type']) ?> border-0" role="alert" data-bs-delay="5000">
            <div class="d-flex">
                <div class="toast-body"><?= Security::e($flash['message']) ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php endforeach; ?>
</div>
