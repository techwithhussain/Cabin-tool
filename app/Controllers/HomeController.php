<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\NoteRepository;

/**
 * HomeController – Landing Page
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
            // DB not initialized or connection failed - fallback to empty stats gracefully
        }

        $response->view('landing.index', [
            'stats'     => $stats,
            'pageTitle' => 'Cabin – Secure Notes & Private Sharing',
            'pageDesc'  => 'Create private notes, set auto-destruct timers, and send sensitive information securely. No sign up. No tracking. Just simple, private, and encrypted sharing.',
        ], 'main');
    }

    public function about(Request $request, Response $response): void
    {
        $response->view('landing.about', [
            'pageTitle' => 'About the Creator – Hussain Lone | Tech With Hussain',
            'pageDesc'  => 'Meet Hussain Lone (Tech With Hussain), the developer and creator of Cabin – a fast, secure, AES-256 encrypted private notes platform.',
        ], 'main');
    }
}
