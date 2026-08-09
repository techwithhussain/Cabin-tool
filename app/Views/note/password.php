<div class="auth-container">
    <div class="auth-card" id="passwordCard">
        <div class="auth-card__icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        </div>

        <h1 class="auth-card__title">Protected Note</h1>
        <p class="auth-card__subtitle">This note is password protected. Enter the password to view it.</p>

        <form class="auth-form" id="passwordForm" novalidate>
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-field">
                <label for="notePasswordInput" class="form-label">Password</label>
                <div class="password-input-wrap">
                    <input
                        type="password"
                        id="notePasswordInput"
                        name="password"
                        class="form-input"
                        placeholder="Enter password..."
                        autocomplete="current-password"
                        autofocus
                        required
                    >
                    <button type="button" class="password-toggle" id="pwToggle" aria-label="Toggle password visibility">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div class="form-error" id="passwordError" role="alert" style="display:none;"></div>
            </div>

            <button type="submit" class="btn btn-primary btn-full" id="verifyBtn">
                <span id="verifyBtnText">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Unlock Note
                </span>
                <span id="verifyBtnLoading" style="display:none;"><span class="spinner"></span> Verifying...</span>
            </button>
        </form>

        <div class="auth-card__footer">
            <a href="/create">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Create Your Own Secure Note
            </a>
        </div>
    </div>
</div>

<script>
window.CABIN_SLUG = <?= json_encode($slug) ?>;
</script>
