<?php use App\Services\ExpiryService; ?>

<div class="workspace-layout" id="workspaceLayout">

    <!-- ── Editor Panel ──────────────────────── -->
    <div class="workspace-editor" id="workspaceEditor">
        <div class="workspace-editor__header">
            <h1 class="workspace-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="#4F5FFF"/></svg>
                Create Secure Note
            </h1>
            <div class="workspace-meta" style="display:flex; align-items:center; gap:8px;">
                <span class="save-status" id="saveStatus" style="font-size:12px; font-weight:600; padding:3px 8px; border-radius:12px; display:none; transition: all 0.2s ease;"></span>
                <span class="char-counter" id="charCounter">0 / <?= number_format($maxNoteLen) ?></span>
            </div>
        </div>

        <textarea
            id="noteContent"
            class="note-textarea"
            placeholder="Write your note here..."
            maxlength="<?= $maxNoteLen ?>"
            aria-label="Note content"
            autofocus
        ></textarea>

        <!-- Image Preview Strip -->
        <div class="image-strip" id="imageStrip" aria-live="polite" style="display:none;">
            <div class="image-strip__inner" id="imageStripInner"></div>
        </div>
    </div>

    <!-- ── Settings Panel ─────────────────────── -->
    <div class="workspace-settings" id="workspaceSettings">
        <div class="settings-card">
            <div class="settings-card__header">
                <h2>Note Settings</h2>
                <button class="settings-toggle-btn" id="settingsToggle" aria-label="Toggle settings">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                </button>
            </div>

            <!-- Expiry -->
            <div class="settings-field" id="expiryField">
                <label for="expirySelect" class="settings-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    Set Expiry
                </label>
                <select id="expirySelect" name="expiry" class="settings-select">
                    <?php foreach ($expiryOptions as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>" <?= $value === '24h' ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Custom URL Slug -->
            <div class="settings-field" id="customSlugField">
                <label for="customSlug" class="settings-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                    Custom URL <span class="settings-label--optional">(Optional)</span>
                </label>
                <div class="custom-slug-wrap">
                    <input
                        type="text"
                        id="customSlug"
                        name="custom_slug"
                        class="settings-input"
                        placeholder="e.g. my-secret-note"
                        value="<?= htmlspecialchars($customSlug ?? '') ?>"
                        maxlength="24"
                        pattern="[a-zA-Z0-9_-]+"
                        autocomplete="off"
                    >
                </div>
                <p class="settings-hint">Letters, numbers &amp; hyphens (3–24 chars)</p>
            </div>

            <!-- Password -->
            <div class="settings-field" id="passwordField">
                <label for="notePassword" class="settings-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Password <span class="settings-label--optional">(Optional)</span>
                </label>
                <div class="password-input-wrap">
                    <input
                        type="password"
                        id="notePassword"
                        name="password"
                        class="settings-input"
                        placeholder="Enter password..."
                        maxlength="128"
                        autocomplete="new-password"
                    >
                    <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                        <svg id="eyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="settings-field" id="uploadField">
                <label class="settings-label">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    Upload Image <span class="settings-label--optional">(Optional)</span>
                </label>
                <div class="dropzone" id="dropzone" role="button" tabindex="0" aria-label="Upload image">
                    <input type="file" id="imageInput" accept="image/jpeg,image/png,image/gif,image/webp" multiple style="display:none;">
                    <div class="dropzone__content">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <p>Click or drag &amp; drop</p>
                        <span>JPG, PNG, GIF, WebP up to <?= $maxUploadMB ?>MB<br>Max <?= $maxImages ?> images</span>
                    </div>
                </div>
                <div class="upload-progress" id="uploadProgress" style="display:none;">
                    <div class="upload-progress__bar" id="uploadProgressBar"></div>
                </div>
                <div class="upload-list" id="uploadList"></div>
            </div>

            <!-- Burn After Read -->
            <div class="settings-field">
                <label class="settings-label settings-label--toggle" for="burnAfterRead">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2c0 0-8 4-8 12s8 10 8 10 8-4 8-10-8-12-8-12z"/></svg>
                    Burn After Read
                    <span class="toggle-wrap">
                        <input type="checkbox" id="burnAfterRead" class="toggle-input" role="switch">
                        <span class="toggle-slider"></span>
                    </span>
                </label>
                <p class="settings-hint">Note will be permanently deleted after the first view.</p>
            </div>

            <!-- Create Button -->
            <button class="btn btn-primary btn-full btn-lg" id="createNoteBtn" type="button">
                <span id="createBtnText">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z"/></svg>
                    Create Note
                </span>
                <span id="createBtnLoading" style="display:none;">
                    <span class="spinner"></span>
                    Creating...
                </span>
            </button>
        </div>

        <!-- Draft indicator -->
        <div class="draft-indicator" id="draftIndicator" style="display:none;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 14.66V20a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2h5.34"/><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/></svg>
            Draft saved
        </div>
    </div>
</div>

<!-- ── Success Modal (shown after note creation) ── -->
<div class="modal-overlay" id="successModal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="successModalTitle">
    <div class="modal" id="successModalContent">
        <div class="modal__header">
            <div class="modal__icon modal__icon--success">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <h2 class="modal__title" id="successModalTitle">Your Note is Ready! 🎉</h2>
            <p class="modal__subtitle">Share the link below with anyone you want.</p>
        </div>

        <div class="modal__body">
            <div class="url-display">
                <div class="url-display__label">Secure Note URL</div>
                <div class="url-display__inner">
                    <input type="text" id="noteUrl" class="url-input" readonly aria-label="Note URL">
                    <button class="btn btn-primary btn-sm" id="copyUrlBtn" type="button">
                        <svg id="copyIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                        Copy
                    </button>
                </div>
            </div>

            <div class="note-meta-display" id="noteMetaDisplay"></div>
        </div>

        <div class="modal__footer">
            <a id="viewNoteLink" href="#" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                View Note
            </a>
            <button class="btn btn-ghost btn-sm" id="createAnotherBtn" type="button">Create Another</button>
        </div>
    </div>
</div>

<input type="hidden" id="csrfToken" value="<?= htmlspecialchars($csrfToken) ?>">
<input type="hidden" id="uploadSession" value="<?= htmlspecialchars(bin2hex(random_bytes(16))) ?>">
