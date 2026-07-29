<div id="page_content_wrapper" class="sub-page">

<section class="page-header" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/assets/uploads/restaurant/delicious-burger-P8VTY5Y-683x1024.jpg');">
    <div class="page-header-content">
        <h1>Share Your Feedback</h1>
        <p>We appreciate you taking the time to help us improve our service.</p>
    </div>
</section>

<section class="form-section">
    <div class="standard_wrapper">
        <form method="post" action="/feedback/submit" class="feedback-form" id="feedbackForm">
            <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
            <input type="hidden" name="type" id="feedbackType" value="complaint">
            <input type="hidden" name="rating" id="feedbackRating" value="0">

            <div class="form-row">
                <label>Feedback Type <span class="required">*</span></label>
                <div class="type-grid">
                    <?php foreach (['Complaint', 'Suggestion', 'Compliment'] as $t): ?>
                    <button type="button" class="type-btn<?= $t === 'Complaint' ? ' active' : '' ?>" data-type="<?= strtolower($t) ?>"><?= $t ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-row">
                <label>Category</label>
                <select name="category" class="form-select">
                    <?php foreach (Feedback::CATEGORIES as $c): ?>
                    <option value="<?= Security::e($c) ?>"><?= Security::e($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <label>Rating <span class="required">*</span></label>
                <div class="star-rating" id="starContainer">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <button type="button" class="star-btn" data-value="<?= $i ?>" aria-label="<?= $i ?> star">&#9733;</button>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-row">
                <label for="message">Your Message <span class="required">*</span></label>
                <textarea name="message" id="message" class="form-textarea" rows="5" required placeholder="Tell us about your experience..."></textarea>
            </div>

            <div class="form-divider"></div>

            <p class="form-section-title">Contact Information (Optional)</p>
            <p class="form-section-desc">We may use this to follow up on your feedback. Add a phone number to receive an SMS acknowledgment.</p>

            <div class="form-grid">
                <div>
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-input" placeholder="John Doe">
                </div>
                <div>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-input" placeholder="john@example.com">
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <input type="tel" name="phone" id="phone" class="form-input" placeholder="+256 700 000 000">
                </div>
            </div>

            <div class="form-action">
                <button type="submit" class="button-primary" id="submitBtn">
                    <i class="bi bi-send"></i>
                    <span>Submit Feedback</span>
                </button>
            </div>
        </form>
    </div>
</section>

</div>