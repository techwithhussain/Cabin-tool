<div class="note-view-container" id="noteViewContainer">

    <!-- Burn After Read Banner -->
    <?php if ($burnAfterRead): ?>
    <div class="burn-banner">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2c0 0-8 4-8 12s8 10 8 10 8-4 8-10-8-12-8-12z"/></svg>
        <strong>Burn After Read:</strong> This note has been permanently deleted after this view.
    </div>
    <?php endif; ?>

    <!-- Expiry Countdown -->
    <?php if ($remainingSeconds > 0): ?>
    <div class="expiry-banner" id="expiryBanner">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        Auto-deletes in <strong id="countdownDisplay"><?= htmlspecialchars($humanRemaining) ?></strong>
    </div>
    <?php endif; ?>

    <!-- Note Card -->
    <div class="note-card">
        <div class="note-card__header">
            <div class="note-card__meta">
                <div class="note-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="#4F5FFF"/></svg>
                    Secure Note
                </div>
                <?php if ($note->isPasswordProtected()): ?>
                <div class="note-badge note-badge--purple">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Protected
                </div>
                <?php endif; ?>
            </div>
            <div class="note-card__views">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <?= number_format($note->viewCount + 1) ?> view<?= ($note->viewCount + 1) !== 1 ? 's' : '' ?>
            </div>
        </div>

        <!-- Note Content -->
        <div class="note-content" id="noteContent">
            <?= nl2br(htmlspecialchars($content)) ?>
        </div>

        <!-- Images Gallery -->
        <?php if (!empty($images)): ?>
        <div class="note-images" id="noteImages">
            <div class="note-images__header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                <?= count($images) ?> Image<?= count($images) !== 1 ? 's' : '' ?>
            </div>
            <div class="images-gallery" id="imagesGallery">
                <?php foreach ($images as $image): ?>
                <div class="gallery-item" data-lightbox>
                    <img
                        src="/image/<?= htmlspecialchars($slug) ?>/<?= htmlspecialchars($image->filename) ?>"
                        alt="<?= htmlspecialchars($image->originalName) ?>"
                        loading="lazy"
                        width="<?= $image->width ?>"
                        height="<?= $image->height ?>"
                    >
                    <div class="gallery-item__overlay">
                        <button class="gallery-download-btn" data-src="/image/<?= htmlspecialchars($slug) ?>/<?= htmlspecialchars($image->filename) ?>" data-name="<?= htmlspecialchars($image->originalName) ?>" aria-label="Download image">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </button>
                        <span class="gallery-item__size"><?= $image->humanSize() ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Inline Editor Form (Hidden by default) -->
        <div class="note-edit-box" id="noteEditBox" style="display:none; margin: 15px 0;">
            <textarea id="noteEditTextarea" class="note-textarea" style="width: 100%; min-height: 180px; font-family: inherit; font-size: 1rem; padding: 15px; border-radius: 10px; border: 1px solid rgba(79, 95, 255, 0.3); background: #fff; box-sizing: border-box; resize: vertical;"></textarea>
            <div style="display: flex; gap: 10px; margin-top: 12px;">
                <button class="btn btn-primary btn-sm" id="saveNoteEditBtn" type="button">Save Changes</button>
                <button class="btn btn-ghost btn-sm" id="cancelNoteEditBtn" type="button">Cancel</button>
            </div>
        </div>

        <!-- Actions -->
        <div class="note-actions">
            <?php if (!$burnAfterRead): ?>
            <button class="action-btn action-btn--edit" id="editNoteBtn" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Note
            </button>
            <?php endif; ?>
            <button class="action-btn action-btn--copy" id="copyContentBtn" type="button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                Copy Text
            </button>
            <button class="action-btn action-btn--share" id="shareNoteBtn" type="button" data-url="<?= htmlspecialchars($appUrl . '/' . $slug) ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                Share URL
            </button>
            <button class="action-btn action-btn--link" id="copyLinkBtn" type="button" data-url="<?= htmlspecialchars($appUrl . '/' . $slug) ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                Copy Link
            </button>
        </div>
    </div>

    <div class="note-footer-cta">
        <a href="/create" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Create Your Own Secure Note
        </a>
        <span class="note-footer-brand">Powered by <a href="/">Cabin</a> by <a href="https://techwithhussain.online/" target="_blank" rel="noopener noreferrer" style="color:inherit; font-weight:600; text-decoration:none;">Tech With Hussain</a></span>
    </div>
</div>

<!-- Lightbox Modal -->
<div class="lightbox" id="lightbox" style="display:none;" role="dialog" aria-modal="true" aria-label="Image viewer">
    <button class="lightbox__close" id="lightboxClose" aria-label="Close">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <img src="" alt="" id="lightboxImg" class="lightbox__img">
</div>

<?php if ($remainingSeconds > 0): ?>
<script>window.CABIN_COUNTDOWN = <?= $remainingSeconds ?>;</script>
<?php endif; ?>
<script>window.CABIN_NOTE_CONTENT = <?= json_encode($content) ?>;</script>
