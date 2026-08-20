<div class="auth-mobile-brand">
    <div class="auth-mobile-icon">
        <i class="bi bi-cup-hot-fill"></i>
    </div>
    <h1>Cafe Javas</h1>
    <p>Create your account</p>
</div>

<div class="auth-form-card">
    <div class="auth-form-header">
        <h1>Create Account</h1>
        <p>The first account becomes administrator.</p>
    </div>

    <form method="post" action="/register">
        <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">

        <div class="auth-field">
            <label class="auth-label">Name</label>
            <div class="auth-input-wrap">
                <i class="bi bi-person auth-input-icon"></i>
                <input type="text" name="name" class="auth-input" placeholder="Your name" required>
            </div>
        </div>

        <div class="auth-field">
            <label class="auth-label">Email Address</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" name="email" class="auth-input" placeholder="you@cafejavas.com" required>
            </div>
        </div>

        <div class="auth-field">
            <label class="auth-label">Password</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock auth-input-icon"></i>
                <input type="password" name="password" class="auth-input" placeholder="Min 6 characters" minlength="6" required>
            </div>
        </div>

        <button type="submit" class="auth-submit">
            <span>Create Account</span>
            <i class="bi bi-person-plus"></i>
        </button>

        <div style="text-align:center;margin-top:16px;font-size:13px">
            <a href="/login" class="auth-link">Back to login</a>
        </div>
    </form>
</div>