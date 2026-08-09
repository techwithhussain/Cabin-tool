<div class="auth-container">
    <div class="auth-card" id="adminLoginCard">
        <div class="auth-card__brand">
            <div class="logo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="white" fill-opacity="0.9"/><path d="M9 12L11 14L15 10" stroke="rgba(79,95,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <span class="auth-card__brand-name">Cabin Admin</span>
                <span class="auth-card__brand-sub">by Tech With Hussain</span>
            </div>
        </div>

        <h1 class="auth-card__title">Admin Login</h1>
        <p class="auth-card__subtitle">Enter your admin password to access the dashboard.</p>

        <?php if (!empty($error)): ?>
        <div class="alert alert--error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="/admin/login" id="adminLoginForm">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-field">
                <label for="adminPassword" class="form-label">Admin Password</label>
                <div class="password-input-wrap">
                    <input
                        type="password"
                        id="adminPassword"
                        name="password"
                        class="form-input"
                        placeholder="Enter admin password..."
                        autocomplete="current-password"
                        autofocus
                        required
                    >
                    <button type="button" class="password-toggle" id="adminPwToggle" aria-label="Toggle password">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Login to Admin Panel
            </button>
        </form>

        <div class="auth-card__footer">
            <a href="/">← Back to Cabin</a>
        </div>
    </div>
</div>
