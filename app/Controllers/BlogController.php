<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\BlogRepository;

/**
 * BlogController – Public Blog views & articles
 */
class BlogController
{
    private BlogRepository $blogRepo;

    public function __construct()
    {
        $this->blogRepo = new BlogRepository();
    }

    /**
     * Blog Index Page
     */
    public function index(Request $request, Response $response): void
    {
        $appUrl   = rtrim($_ENV['APP_URL'] ?? 'https://cabinn.in', '/');
        $category = $request->query('category');
        $search   = $request->query('q');

        $blogs      = [];
        $categories = [];

        try {
            $blogs      = $this->blogRepo->getAll('published', $category, $search, 30);
            $categories = $this->blogRepo->getCategories();
        } catch (\Throwable $e) {
            error_log('[BlogController] Error: ' . $e->getMessage());
        }

        $response->view('blog.index', [
            'blogs'         => $blogs,
            'categories'    => $categories,
            'activeCategory'=> $category ?? 'all',
            'searchQuery'   => $search ?? '',
            'pageTitle'     => 'Privacy & Security Blog – Cabin',
            'pageDesc'      => 'Insights, guides, and tutorials on data privacy, self-destructing notes, AES-256 encryption, and secure communication by Hussain Lone.',
            'schemaType'    => 'blog',
            'breadcrumbs'   => [
                ['name' => 'Home', 'url' => $appUrl . '/'],
                ['name' => 'Blog', 'url' => $appUrl . '/blog'],
            ],
        ], 'main');
    }

    /**
     * Single Blog Post
     */
    public function show(Request $request, Response $response): void
    {
        $appUrl = rtrim($_ENV['APP_URL'] ?? 'https://cabinn.in', '/');
        $slug   = $request->param('slug') ?? '';

        $blog = null;
        $recentPosts = [];

        try {
            $blog = $this->blogRepo->getBySlug($slug);
            if ($blog) {
                $this->blogRepo->incrementViews($blog->id);
                $recentPosts = $this->blogRepo->getRecent(3, $blog->id);
            }
        } catch (\Throwable $e) {
            error_log('[BlogController] Error: ' . $e->getMessage());
        }

        if (!$blog || (!$blog->isPublished() && !isset($_SESSION['admin_logged_in']))) {
            $response->status(404)->view('errors.404', [
                'pageTitle' => 'Blog Post Not Found – Cabin',
                'noindex'   => true,
            ], 'minimal');
            return;
        }

        $response->view('blog.show', [
            'blog'        => $blog,
            'recentPosts' => $recentPosts,
            'pageTitle'   => ($blog->metaTitle ?: $blog->title) . ' – Cabin Blog',
            'pageDesc'    => $blog->metaDescription ?: $blog->summary,
            'schemaType'  => 'article',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => $appUrl . '/'],
                ['name' => 'Blog', 'url' => $appUrl . '/blog'],
                ['name' => $blog->title, 'url' => $appUrl . '/blog/' . $blog->slug],
            ],
        ], 'main');
    }
}
