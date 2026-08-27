<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * LegalController – Privacy Policy, Terms of Service, DMCA, Disclaimer
 */
class LegalController
{
    private string $appUrl;

    public function __construct()
    {
        $this->appUrl = rtrim($_ENV['APP_URL'] ?? 'https://cabinn.in', '/');
    }

    public function privacy(Request $request, Response $response): void
    {
        $response->view('legal.privacy', [
            'pageTitle'   => 'Privacy Policy – Cabin | Zero Logs. Zero Tracking.',
            'pageDesc'    => 'Cabin collects zero personal data. Your notes are AES-256 encrypted, auto-deleted after expiry, and never shared with third parties. Complete privacy policy.',
            'legalPage'   => true,
            'breadcrumbs' => [
                ['name' => 'Home',           'url' => $this->appUrl . '/'],
                ['name' => 'Privacy Policy', 'url' => $this->appUrl . '/privacy'],
            ],
        ], 'main');
    }

    public function terms(Request $request, Response $response): void
    {
        $response->view('legal.terms', [
            'pageTitle'   => 'Terms of Service – Cabin | Secure Notes Platform',
            'pageDesc'    => 'Terms of Service for Cabin, the privacy-first encrypted note-sharing platform. No accounts, no tracking. Read our terms before using the service.',
            'legalPage'   => true,
            'breadcrumbs' => [
                ['name' => 'Home',             'url' => $this->appUrl . '/'],
                ['name' => 'Terms of Service', 'url' => $this->appUrl . '/terms'],
            ],
        ], 'main');
    }

    public function dmca(Request $request, Response $response): void
    {
        $response->view('legal.dmca', [
            'pageTitle'   => 'DMCA Policy – Cabin | Copyright & Takedown Requests',
            'pageDesc'    => 'Cabin respects intellectual property rights. Submit a valid DMCA takedown notice if your copyrighted content appears on our platform.',
            'legalPage'   => true,
            'breadcrumbs' => [
                ['name' => 'Home',        'url' => $this->appUrl . '/'],
                ['name' => 'DMCA Policy', 'url' => $this->appUrl . '/dmca'],
            ],
        ], 'main');
    }

    public function disclaimer(Request $request, Response $response): void
    {
        $response->view('legal.disclaimer', [
            'pageTitle'   => 'Disclaimer – Cabin | As-Is Service & Liability Notice',
            'pageDesc'    => 'Cabin is provided as-is without any warranties. We are not liable for any data loss, unauthorized access, or service interruptions. Read full disclaimer.',
            'legalPage'   => true,
            'breadcrumbs' => [
                ['name' => 'Home',       'url' => $this->appUrl . '/'],
                ['name' => 'Disclaimer', 'url' => $this->appUrl . '/disclaimer'],
            ],
        ], 'main');
    }
}
