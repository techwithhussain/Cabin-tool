<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Cabin') ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc ?? 'Secure notes and private sharing.') ?>">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">

    <!-- Fonts (Non-blocking with font-display: swap) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"></noscript>

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/workspace.css">
    <link rel="stylesheet" href="/assets/css/view.css">
    <link rel="stylesheet" href="/assets/css/admin.css">

    <?php if (isset($csrfToken)): ?>
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">
    <?php endif; ?>
</head>
<body class="minimal-body">

    <!-- Minimal Nav -->
    <nav class="minimal-nav">
        <div class="nav-container">
            <a href="/" class="nav-logo">
                <div class="logo-icon logo-icon--sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="white" fill-opacity="0.9"/><path d="M9 12L11 14L15 10" stroke="rgba(79,95,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <span>Cabin</span>
            </a>
            <div class="minimal-nav-actions">
                <a href="/create" class="btn btn-outline btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    New Note
                </a>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="minimal-main">
        <?= $content ?>
    </main>

    <script src="/assets/js/app.js" defer></script>
    <script src="/assets/js/editor.js" defer></script>
    <script src="/assets/js/admin.js" defer></script>
</body>
</html>
