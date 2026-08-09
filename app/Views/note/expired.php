<?php
$isDeleted = ($reason ?? 'expired') === 'deleted';
$icon = $isDeleted
    ? '<polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/>'
    : '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>';
?>
<div class="state-container">
    <div class="state-card state-card--<?= $isDeleted ? 'deleted' : 'expired' ?>">
        <div class="state-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <?= $icon ?>
            </svg>
        </div>

        <h1 class="state-title">
            <?= $isDeleted ? 'Note Deleted' : 'Note Expired' ?>
        </h1>

        <p class="state-description">
            <?php if ($isDeleted): ?>
                This note has been permanently deleted and no longer exists.
                All content and images have been securely removed from our servers.
            <?php else: ?>
                This note has expired and has been permanently deleted from our servers.
                The content and any attached images are gone forever.
            <?php endif; ?>
        </p>

        <div class="state-features">
            <div class="state-feature">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Content permanently deleted
            </div>
            <div class="state-feature">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Images securely removed
            </div>
            <div class="state-feature">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Zero data retained
            </div>
        </div>

        <div class="state-actions">
            <a href="/create" class="btn btn-primary btn-lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                Create a New Secure Note
            </a>
            <a href="/" class="btn btn-ghost">← Back to Home</a>
        </div>
    </div>
</div>
