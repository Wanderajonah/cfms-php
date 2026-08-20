<div class="auth-mobile-brand">
    <img src="/assets/uploads/restaurant/logo-dark.png" alt="Feedback Management System" class="auth-mobile-logo">
    <h1>Cafe Javas</h1>
    <p>Customer Feedback Management System</p>
</div>

<div class="auth-form-card">
    <div class="auth-form-header">
        <img src="/assets/uploads/restaurant/logo-dark.png" alt="Cafe Javas" class="auth-form-avatar">
        <h1>Welcome back</h1>
        <p>Sign in to your feedback management portal</p>
    </div>

    <form method="post" action="/login">
        <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
        <input type="hidden" name="role" id="loginRole" value="admin">

        <div class="auth-field">
            <label class="auth-label">Role</label>
            <div class="auth-role-toggle">
                <button type="button" class="auth-role-btn active" data-role="admin">Admin</button>
                <button type="button" class="auth-role-btn" data-role="staff">Staff</button>
            </div>
        </div>

        <div class="auth-field">
            <label class="auth-label">Email Address</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope auth-input-icon"></i>
                <input type="email" name="email" class="auth-input" placeholder="you@cafejavas.com" required autofocus>
            </div>
        </div>

        <div class="auth-field">
            <label class="auth-label">Password</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock auth-input-icon"></i>
                <input type="password" name="password" class="auth-input" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required>
            </div>
        </div>

        <div class="auth-row">
            <label class="auth-checkbox">
                <input type="checkbox" name="remember" class="auth-checkbox-input">
                <span>Remember me</span>
            </label>
            <a href="/register" class="auth-link">Register</a>
        </div>

        <button type="submit" class="auth-submit">
            <span>Sign In</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>

    <div class="auth-footer">
        <i class="bi bi-shield-check"></i>
        Protected by end-to-end encryption
    </div>
</div>