<?php
// ─────────────────────────────────────────────
// CANONICAL URL HELPER
// Strips query strings, normalizes slashes
// ─────────────────────────────────────────────
$_appUrl    = rtrim($_ENV['APP_URL'] ?? 'https://cabinn.in', '/');
$_reqPath   = strtolower(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
$_reqPath   = '/' . trim($_reqPath, '/');
$_canonical = $_appUrl . ($_reqPath === '/' ? '/' : rtrim($_reqPath, '/'));

// ─────────────────────────────────────────────
// PAGE META DEFAULTS
// ─────────────────────────────────────────────
$_title       = htmlspecialchars($pageTitle   ?? 'Cabin – Secure Notes & Private Sharing');
$_desc        = htmlspecialchars($pageDesc    ?? 'Create encrypted private notes, set auto-destruct timers, and share securely. No sign up. No tracking. AES-256 encrypted.');
$_ogImage     = $_appUrl . '/assets/img/og-image.jpg';
$_ogImageAlt  = 'Cabin – Secure Notes & Private Sharing';
$_noindex     = $noindex ?? false;
$_breadcrumbs = $breadcrumbs ?? [];
$_schemaType  = $schemaType ?? 'default';

// ─────────────────────────────────────────────
// CURRENT PATH for conditional CSS
// ─────────────────────────────────────────────
$_currentPath = $_reqPath;
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <!-- ═══════════════════════════════════════
         CHARSET & VIEWPORT (must be first)
    ═══════════════════════════════════════ -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- ═══════════════════════════════════════
         PRIMARY META TAGS
    ═══════════════════════════════════════ -->
    <title><?= $_title ?></title>
    <meta name="title"       content="<?= $_title ?>">
    <meta name="description" content="<?= $_desc ?>">
    <meta name="keywords"    content="secure notes, private notes, encrypted notes, burn after read, self destructing notes, no signup notes, AES-256, cabin notes">
    <meta name="author"      content="Hussain Lone – Tech With Hussain">
    <meta name="publisher"   content="Tech With Hussain">
    <meta name="language"    content="en">
    <meta name="theme-color" content="#7C3AED">
    <meta name="application-name" content="Cabin">

    <!-- ═══════════════════════════════════════
         ROBOTS CONTROL
         Note pages are noindex to protect privacy
    ═══════════════════════════════════════ -->
<?php if ($_noindex): ?>
    <meta name="robots" content="noindex,nofollow">
<?php else: ?>
    <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
<?php endif; ?>

    <!-- ═══════════════════════════════════════
         CANONICAL URL
    ═══════════════════════════════════════ -->
    <link rel="canonical" href="<?= htmlspecialchars($_canonical) ?>">

    <!-- ═══════════════════════════════════════
         GOOGLE SITE VERIFICATION
    ═══════════════════════════════════════ -->
    <meta name="google-site-verification" content="n0A2A14Y3i2VsR4DUTEY4OfDTPMaXXlWvYwPsPuTvlo">

    <!-- ═══════════════════════════════════════
         OPEN GRAPH / FACEBOOK
    ═══════════════════════════════════════ -->
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="Cabin">
    <meta property="og:locale"      content="en_IN">
    <meta property="og:title"       content="<?= $_title ?>">
    <meta property="og:description" content="<?= $_desc ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($_canonical) ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($_ogImage) ?>">
    <meta property="og:image:alt"   content="<?= htmlspecialchars($_ogImageAlt) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type"  content="image/jpeg">

    <!-- ═══════════════════════════════════════
         TWITTER CARDS
    ═══════════════════════════════════════ -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:site"        content="@techwithhussain">
    <meta name="twitter:creator"     content="@techwithhussain">
    <meta name="twitter:title"       content="<?= $_title ?>">
    <meta name="twitter:description" content="<?= $_desc ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($_ogImage) ?>">
    <meta name="twitter:image:alt"   content="<?= htmlspecialchars($_ogImageAlt) ?>">

    <!-- ═══════════════════════════════════════
         FAVICON & PWA MANIFEST
    ═══════════════════════════════════════ -->
    <link rel="icon"             type="image/svg+xml"  href="/assets/img/favicon.svg">
    <link rel="icon"             type="image/png"       href="/assets/img/favicon-32.png" sizes="32x32">
    <link rel="apple-touch-icon"                        href="/assets/img/apple-touch-icon.png" sizes="180x180">
    <link rel="manifest"                                href="/site.webmanifest">

    <!-- ═══════════════════════════════════════
         PERFORMANCE — Preconnect & DNS Prefetch
    ═══════════════════════════════════════ -->
    <link rel="preconnect"    href="https://fonts.googleapis.com">
    <link rel="preconnect"    href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch"  href="https://fonts.googleapis.com">
    <link rel="dns-prefetch"  href="https://fonts.gstatic.com">

    <!-- ═══════════════════════════════════════
         FONTS — Non-blocking load (font-display:swap)
    ═══════════════════════════════════════ -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" fetchpriority="low">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"></noscript>

    <!-- ═══════════════════════════════════════
         CSS — Global (always loaded)
    ═══════════════════════════════════════ -->
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/landing.css">

    <?php if ($_currentPath === '/about'): ?>
    <link rel="stylesheet" href="/assets/css/about.css">
    <?php endif; ?>

    <?php
    $legalPaths = ['/privacy', '/terms', '/dmca', '/disclaimer'];
    if (in_array($_currentPath, $legalPaths, true)):
    ?>
    <link rel="stylesheet" href="/assets/css/legal.css">
    <?php endif; ?>

    <?php if (isset($csrfToken)): ?>
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">
    <?php endif; ?>

    <!-- ═══════════════════════════════════════
         SCHEMA.ORG — JSON-LD Structured Data
    ═══════════════════════════════════════ -->
    <?php
    // Build BreadcrumbList schema
    $breadcrumbSchema = '';
    if (!empty($_breadcrumbs)) {
        $items = [];
        foreach ($_breadcrumbs as $pos => $crumb) {
            $items[] = json_encode([
                '@type'    => 'ListItem',
                'position' => $pos + 1,
                'name'     => $crumb['name'],
                'item'     => $crumb['url'],
            ]);
        }
        $breadcrumbSchema = ',{
            "@type": "BreadcrumbList",
            "itemListElement": [' . implode(',', $items) . ']
        }';
    }
    ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Organization",
                "@id": "<?= $_appUrl ?>/#organization",
                "name": "Tech With Hussain",
                "url": "https://techwithhussain.online/",
                "logo": {
                    "@type": "ImageObject",
                    "url": "<?= $_appUrl ?>/assets/img/favicon.svg"
                },
                "sameAs": [
                    "https://github.com/techwithhussain",
                    "https://www.instagram.com/tech.withhussain",
                    "https://youtube.com/@Tech.WithHussain",
                    "https://www.facebook.com/TechWithHussain/"
                ],
                "founder": {
                    "@type": "Person",
                    "name": "Hussain Lone",
                    "url": "https://techwithhussain.online/about/"
                }
            },
            {
                "@type": "WebSite",
                "@id": "<?= $_appUrl ?>/#website",
                "url": "<?= $_appUrl ?>/",
                "name": "Cabin",
                "description": "Secure encrypted notes with auto-destruct. No sign up required.",
                "publisher": { "@id": "<?= $_appUrl ?>/#organization" },
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": {
                        "@type": "EntryPoint",
                        "urlTemplate": "<?= $_appUrl ?>/{search_term_string}"
                    },
                    "query-input": "required name=search_term_string"
                }
            },
            {
                "@type": "WebApplication",
                "@id": "<?= $_appUrl ?>/#webapplication",
                "name": "Cabin – Secure Notes",
                "url": "<?= $_appUrl ?>/",
                "applicationCategory": "Productivity",
                "operatingSystem": "Any",
                "offers": {
                    "@type": "Offer",
                    "price": "0",
                    "priceCurrency": "USD"
                },
                "featureList": [
                    "AES-256 Encryption",
                    "No Sign Up Required",
                    "Auto-Delete / Burn After Read",
                    "Custom URLs",
                    "Password Protection"
                ],
                "creator": { "@id": "<?= $_appUrl ?>/#organization" }
            }
            <?= $breadcrumbSchema ?>
        ]
    }
    </script>

</head>
<body>
    <!-- Skip Navigation Link (Accessibility / Core Web Vitals) -->
    <a href="#main-content" class="skip-link" aria-label="Skip to main content">Skip to content</a>

    <!-- ═══════════════════════════════════════
         NAVIGATION
    ═══════════════════════════════════════ -->
    <nav class="navbar" id="navbar" role="navigation" aria-label="Main navigation">
        <div class="nav-container">
            <div class="nav-brand-wrap">
                <a href="/" class="nav-logo" aria-label="Cabin – Go to homepage">
                    <div class="logo-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="white" fill-opacity="0.9"/>
                            <path d="M9 12L11 14L15 10" stroke="rgba(79,95,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="logo-text">Cabin</span>
                </a>
                <a href="https://techwithhussain.online/" target="_blank" rel="noopener noreferrer" class="logo-sub-badge" title="Created by Tech With Hussain – visit portfolio">
                    <span class="logo-sub-by">by</span>
                    <span class="logo-sub-name">Tech With Hussain</span>
                </a>
            </div>

            <ul class="nav-links" id="navLinks" role="menubar" aria-label="Site navigation">
                <li role="none"><a href="/#features"    role="menuitem">Features</a></li>
                <li role="none"><a href="/#how-it-works" role="menuitem">How It Works</a></li>
                <li role="none"><a href="/#use-cases"   role="menuitem">Use Cases</a></li>
                <li role="none"><a href="/about"         role="menuitem">About</a></li>
                <li role="none"><a href="/#faq"          role="menuitem">FAQ</a></li>
                <li role="none"><a href="/#contact"      role="menuitem">Contact</a></li>
            </ul>

            <div class="nav-actions">
                <a href="/create" class="btn btn-primary btn-sm" aria-label="Create a new secure note">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Create Note</span>
                </a>
                <button class="nav-mobile-toggle" id="mobileToggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="navLinks">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ═══════════════════════════════════════
         PAGE CONTENT
    ═══════════════════════════════════════ -->
    <main id="main-content" tabindex="-1">
        <?= $content ?>
    </main>

    <!-- ═══════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════ -->
    <footer class="footer" role="contentinfo" aria-label="Site footer">
        <div class="footer-container">
            <div class="footer-brand">
                <a href="/" class="footer-logo" aria-label="Cabin homepage">
                    <div class="logo-icon logo-icon--sm" aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z" fill="white" fill-opacity="0.9"/><path d="M9 12L11 14L15 10" stroke="rgba(79,95,255,0.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <span>Cabin</span>
                </a>
                <p class="footer-tagline">The most secure way to create private notes and share them with anyone. AES-256 encrypted. Zero logs.</p>
                <nav class="footer-social" aria-label="Social media links">
                    <a href="https://github.com/techwithhussain" target="_blank" rel="noopener noreferrer" aria-label="GitHub – Tech With Hussain">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/tech.withhussain" target="_blank" rel="noopener noreferrer" aria-label="Instagram – Tech With Hussain">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="https://www.youtube.com/@Tech.WithHussain" target="_blank" rel="noopener noreferrer" aria-label="YouTube – Tech With Hussain">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/TechWithHussain/" target="_blank" rel="noopener noreferrer" aria-label="Facebook – Tech With Hussain">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </nav>
            </div>

            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="/#features">Features</a></li>
                        <li><a href="/#how-it-works">How It Works</a></li>
                        <li><a href="/#use-cases">Use Cases</a></li>
                        <li><a href="/about">About Creator</a></li>
                        <li><a href="/#faq">FAQ</a></li>
                        <li><a href="/create">Create a Note</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="/privacy">Privacy Policy</a></li>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/dmca">DMCA</a></li>
                        <li><a href="/disclaimer">Disclaimer</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Made with ❤️ by<br><a href="https://techwithhussain.online/" target="_blank" rel="noopener noreferrer" style="color:var(--color-primary-light, #7c8bff); text-decoration:none; font-weight:700;">Tech With Hussain</a></h4>
                    <p class="footer-col-desc">Building simple, useful and powerful tools for everyone.</p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© <?= date('Y') ?> Cabin by <a href="https://techwithhussain.online/" target="_blank" rel="noopener noreferrer" style="color:inherit; font-weight:600; text-decoration:none;">Tech With Hussain</a>. All rights reserved. &nbsp;|&nbsp; <a href="/privacy" style="color:inherit; text-decoration:none;">Privacy</a> &nbsp;|&nbsp; <a href="/terms" style="color:inherit; text-decoration:none;">Terms</a></p>
        </div>
    </footer>

    <!-- ═══════════════════════════════════════
         JAVASCRIPT — deferred, non-blocking
    ═══════════════════════════════════════ -->
    <script src="/assets/js/app.js" defer></script>
    <script src="/assets/js/landing.js" defer></script>
</body>
</html>
