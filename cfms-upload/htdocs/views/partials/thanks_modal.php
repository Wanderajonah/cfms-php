<?php
$thanks = $_SESSION['_feedback_thanks'] ?? null;
if (!$thanks) {
    return;
}
unset($_SESSION['_feedback_thanks']);
$name = trim((string) ($thanks['name'] ?? ''));
?>
<div class="thanks-modal-overlay" id="thanksModal">
    <div class="thanks-modal" role="dialog" aria-modal="true" aria-labelledby="thanksTitle">
        <button type="button" class="thanks-modal-close" data-thanks-close aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="thanks-modal-icon">
            <i class="bi bi-check-lg"></i>
        </div>
        <h2 id="thanksTitle">Thank you<?= $name !== '' ? ', ' . Security::e($name) : '' ?>!</h2>
        <p class="thanks-modal-sub">We appreciate you taking the time to share your feedback with Cafe Javas. It helps us serve you better.</p>
        <div class="thanks-modal-ticket">
            <span class="ticket-label">Your ticket number</span>
            <span class="ticket-value">#<?= (int) $thanks['ticket'] ?></span>
        </div>
        <p class="thanks-modal-hint">Use this ticket with your phone number on the <a href="/feedback/track">Track</a> page to check its status.</p>
        <button type="button" class="thanks-modal-btn" data-thanks-close>Done</button>
    </div>
</div>