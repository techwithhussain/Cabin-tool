<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\NoteRepository;

/**
 * HomeController – Landing Page & About Page
 */
class HomeController
{
    public function index(Request $request, Response $response): void
    {
        $stats = ['total' => 0, 'active' => 0, 'expired' => 0, 'deleted' => 0];

        try {
            $repo  = new NoteRepository();
            $stats = $repo->getStats();
        } catch (\Throwable $e) {
            // DB not initialized or connection failed — graceful fallback
        }

        $response->view('landing.index', [
            'stats'      => $stats,
            'pageTitle'  => 'Cabin – Secure Notes & Private Sharing | AES-256 Encrypted',
            'pageDesc'   => 'Create AES-256 encrypted private notes, set auto-destruct timers, and share them securely. No sign up. No tracking. No logs. Just pure private sharing.',
            'schemaType' => 'home',
            'breadcrumbs' => [],  // Homepage has no breadcrumbs
        ], 'main');
    }

    public function about(Request $request, Response $response): void
    {
        $appUrl = rtrim($_ENV['APP_URL'] ?? 'https://cabinn.in', '/');

        $response->view('landing.about', [
            'pageTitle'  => 'About the Creator – Hussain Lone | Tech With Hussain',
            'pageDesc'   => 'Meet Hussain Lone (Tech With Hussain), the Web Developer, SEO Expert, and creator of Cabin – a fast, AES-256 encrypted private notes platform trusted by users worldwide.',
            'schemaType' => 'about',
            'breadcrumbs' => [
                ['name' => 'Home',  'url' => $appUrl . '/'],
                ['name' => 'About', 'url' => $appUrl . '/about'],
            ],
        ], 'main');
    }
}
