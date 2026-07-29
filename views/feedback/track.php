<div id="page_content_wrapper" class="sub-page">

<section class="page-header" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/assets/uploads/restaurant/delicious-burger-P8VTY5Y-683x1024.jpg');">
    <div class="page-header-content">
        <h1>Track Feedback</h1>
        <p>Enter your ticket number and phone number to check your feedback status.</p>
    </div>
</section>

<section class="form-section">
    <div class="standard_wrapper">
        <div class="track-card">
            <form method="get" class="track-form">
                <input name="ticket" value="<?= Security::e($_GET['ticket'] ?? '') ?>" class="form-input" placeholder="Ticket number">
                <input name="phone" value="<?= Security::e($_GET['phone'] ?? '') ?>" class="form-input" placeholder="Phone number">
                <button class="button-primary">Track</button>
            </form>

            <?php if ($item): ?>
            <div class="track-result">
                <div class="track-header">
                    <div>
                        <strong>Ticket #<?= Security::e((string) $item['ticketNumber']) ?></strong>
                        <div class="track-meta"><?= Security::e($item['category']) ?> &middot; <?= Security::e($item['type']) ?></div>
                    </div>
                    <span class="status-badge status-<?= $item['status'] ?>"><?= Security::e($item['status']) ?></span>
                </div>
                <?php if ($item['response']): ?>
                <div class="track-response">
                    <strong>Our Response:</strong>
                    <p><?= Security::e($item['response']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php elseif (isset($_GET['ticket']) || isset($_GET['phone'])): ?>
            <div class="track-none">No feedback found for that ticket and phone number.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

</div>