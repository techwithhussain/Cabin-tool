<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\BlogRepository;
use App\Services\CsrfService;

/**
 * AdminBlogController – Blog management in Admin Panel
 */
class AdminBlogController
{
    private BlogRepository $blogRepo;
    private CsrfService    $csrf;

    public function __construct()
    {
        $this->blogRepo = new BlogRepository();
        $this->csrf     = new CsrfService();
    }

    /**
     * List all blog posts
     */
    public function list(Request $request, Response $response): void
    {
        $blogs = $this->blogRepo->getAll('all', null, null, 100);

        $response->view('admin.blogs.index', [
            'pageTitle' => 'Manage Blogs – Cabin Admin',
            'blogs'     => $blogs,
            'csrfToken' => $this->csrf->getToken(),
            'message'   => $_SESSION['admin_flash_msg'] ?? null,
        ], 'main');

        unset($_SESSION['admin_flash_msg']);
    }

    /**
     * Show Create Blog Form
     */
    public function createForm(Request $request, Response $response): void
    {
        $response->view('admin.blogs.form', [
            'pageTitle' => 'Create New Blog – Cabin Admin',
            'blog'      => null,
            'csrfToken' => $this->csrf->getToken(),
            'action'    => '/admin/blogs/create',
        ], 'main');
    }

    /**
     * Store new blog
     */
    public function create(Request $request, Response $response): void
    {
        $title   = trim((string)$request->post('title'));
        $content = (string)$request->post('content');

        if (empty($title) || empty($content)) {
            $_SESSION['admin_flash_msg'] = ['type' => 'error', 'text' => 'Title and Content are required.'];
            $response->redirect('/admin/blogs/create');
            return;
        }

        $id = $this->blogRepo->create([
            'title'            => $title,
            'slug'             => $request->post('slug') ?: $title,
            'summary'          => $request->post('summary'),
            'content'          => $content,
            'cover_image'      => $request->post('cover_image'),
            'category'         => $request->post('category') ?: 'Security',
            'author'           => $request->post('author') ?: 'Hussain Lone',
            'read_time'        => $request->post('read_time') ?: '4 min read',
            'status'           => $request->post('status') ?: 'published',
            'meta_title'       => $request->post('meta_title'),
            'meta_description' => $request->post('meta_description'),
            'meta_keywords'    => $request->post('meta_keywords'),
        ]);

        $_SESSION['admin_flash_msg'] = ['type' => 'success', 'text' => 'Blog post published successfully!'];
        $response->redirect('/admin/blogs');
    }

    /**
     * Show Edit Blog Form
     */
    public function editForm(Request $request, Response $response): void
    {
        $id   = (int)$request->param('id');
        $blog = $this->blogRepo->getById($id);

        if (!$blog) {
            $response->redirect('/admin/blogs');
            return;
        }

        $response->view('admin.blogs.form', [
            'pageTitle' => 'Edit Blog – ' . $blog->title,
            'blog'      => $blog,
            'csrfToken' => $this->csrf->getToken(),
            'action'    => '/admin/blogs/edit/' . $blog->id,
        ], 'main');
    }

    /**
     * Update existing blog
     */
    public function update(Request $request, Response $response): void
    {
        $id   = (int)$request->param('id');
        $blog = $this->blogRepo->getById($id);

        if (!$blog) {
            $response->redirect('/admin/blogs');
            return;
        }

        $title   = trim((string)$request->post('title'));
        $content = (string)$request->post('content');

        if (empty($title) || empty($content)) {
            $_SESSION['admin_flash_msg'] = ['type' => 'error', 'text' => 'Title and Content are required.'];
            $response->redirect('/admin/blogs/edit/' . $id);
            return;
        }

        $this->blogRepo->update($id, [
            'title'            => $title,
            'slug'             => $request->post('slug') ?: $title,
            'summary'          => $request->post('summary'),
            'content'          => $content,
            'cover_image'      => $request->post('cover_image'),
            'category'         => $request->post('category') ?: 'Security',
            'author'           => $request->post('author') ?: 'Hussain Lone',
            'read_time'        => $request->post('read_time') ?: '4 min read',
            'status'           => $request->post('status') ?: 'published',
            'meta_title'       => $request->post('meta_title'),
            'meta_description' => $request->post('meta_description'),
            'meta_keywords'    => $request->post('meta_keywords'),
        ]);

        $_SESSION['admin_flash_msg'] = ['type' => 'success', 'text' => 'Blog post updated successfully!'];
        $response->redirect('/admin/blogs');
    }

    /**
     * Delete blog
     */
    public function delete(Request $request, Response $response): void
    {
        $id = (int)$request->param('id');
        $this->blogRepo->delete($id);

        $_SESSION['admin_flash_msg'] = ['type' => 'success', 'text' => 'Blog post deleted.'];
        $response->redirect('/admin/blogs');
    }
}
