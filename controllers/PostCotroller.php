<?php
// filepath: c:\wamp64\www\cpsproject\controllers\PostController.php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use core\CSRF;
use models\PostModel;
use models\CommentModel;

class PostController extends Controller
{
    protected $postModel;
    protected $commentModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        Session::start();
        $this->postModel = new PostModel();
        $this->commentModel = new CommentModel();
        $this->auth = new Auth();
    }

    /**
     * List all published posts
     */
    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $filters = [
            'category' => $_GET['category'] ?? '',
            'search' => trim($_GET['search'] ?? ''),
            'featured' => !empty($_GET['featured'])
        ];

        $posts = $this->postModel->getAll($filters, $limit, $offset);
        $totalPosts = $this->postModel->getCount($filters);
        $totalPages = ceil($totalPosts / $limit);

        // Get filter options
        $categories = $this->postModel->getCategories();

        $this->render('index', [
            'title' => 'Blog Posts',
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalPosts' => $totalPosts,
            'filters' => $filters,
            'categories' => $categories
        ]);
    }

    /**
     * Show single post with comments
     */
    public function show($id)
    {
        $post = $this->postModel->getById($id);
        
        if (!$post || $post['status'] !== 'published') {
            echo "Post not found";
            return;
        }

        // Increment view count
        $this->postModel->incrementViews($id);

        // Get approved comments
        $comments = $this->commentModel->getByPost($id);

        $this->render('show', [
            'title' => $post['title'],
            'post' => $post,
            'comments' => $comments
        ]);
    }

    /**
     * Create post form (admin/author only)
     */
    public function create()
    {
        $this->auth->requireAuth();
        
        // Only admins can create posts for now
        if (!$this->auth->isAdmin()) {
            Session::setFlash('error', 'Access denied');
            header('Location: /cpsproject/posts');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }

        $categories = $this->postModel->getCategories();

        $this->render('create', [
            'title' => 'Create Post',
            'categories' => $categories
        ]);
    }

    /**
     * Store new post
     */
    public function store()
    {
        $this->auth->requireAuth();

        if (!$this->auth->isAdmin()) {
            Session::setFlash('error', 'Access denied');
            header('Location: /cpsproject/posts');
            exit;
        }

        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/posts/create');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'body' => trim($_POST['body'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'status' => $_POST['status'] ?? 'draft',
            'featured' => isset($_POST['featured']),
            'user_id' => $this->auth->id()
        ];

        // Handle tags (comma-separated to array)
        $tagsInput = trim($_POST['tags'] ?? '');
        if (!empty($tagsInput)) {
            $data['tags'] = array_map('trim', explode(',', $tagsInput));
        }

        // Validation
        if (empty($data['title']) || empty($data['body'])) {
            Session::setFlash('error', 'Title and body are required');
            header('Location: /cpsproject/posts/create');
            exit;
        }

        $postId = $this->postModel->create($data);
        if ($postId) {
            Session::setFlash('success', 'Post created successfully');
            header('Location: /cpsproject/post/' . $postId);
        } else {
            Session::setFlash('error', 'Failed to create post');
            header('Location: /cpsproject/posts/create');
        }
        exit;
    }

    /**
     * Edit post form
     */
    public function edit($id)
    {
        $this->auth->requireAuth();

        $post = $this->postModel->getById($id);
        if (!$post) {
            Session::setFlash('error', 'Post not found');
            header('Location: /cpsproject/posts');
            exit;
        }

        // Check permissions
        if (!$this->auth->isAdmin() && $post['user_id'] !== $this->auth->id()) {
            Session::setFlash('error', 'Access denied');
            header('Location: /cpsproject/posts');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }

        $categories = $this->postModel->getCategories();

        // Convert tags array back to comma-separated string for form
        $post['tags_string'] = is_array($post['tags']) ? implode(', ', $post['tags']) : '';

        $this->render('edit', [
            'title' => 'Edit Post',
            'post' => $post,
            'categories' => $categories
        ]);
    }

    /**
     * Update post
     */
    public function update($id)
    {
        $this->auth->requireAuth();

        $post = $this->postModel->getById($id);
        if (!$post) {
            Session::setFlash('error', 'Post not found');
            header('Location: /cpsproject/posts');
            exit;
        }

        // Check permissions
        if (!$this->auth->isAdmin() && $post['user_id'] !== $this->auth->id()) {
            Session::setFlash('error', 'Access denied');
            header('Location: /cpsproject/posts');
            exit;
        }

        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/post/' . $id . '/edit');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'body' => trim($_POST['body'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'status' => $_POST['status'] ?? 'draft',
            'featured' => isset($_POST['featured'])
        ];

        // Handle tags
        $tagsInput = trim($_POST['tags'] ?? '');
        if (!empty($tagsInput)) {
            $data['tags'] = array_map('trim', explode(',', $tagsInput));
        } else {
            $data['tags'] = [];
        }

        // Validation
        if (empty($data['title']) || empty($data['body'])) {
            Session::setFlash('error', 'Title and body are required');
            header('Location: /cpsproject/post/' . $id . '/edit');
            exit;
        }

        if ($this->postModel->update($id, $data)) {
            Session::setFlash('success', 'Post updated successfully');
            header('Location: /cpsproject/post/' . $id);
        } else {
            Session::setFlash('error', 'Failed to update post');
            header('Location: /cpsproject/post/' . $id . '/edit');
        }
        exit;
    }

    /**
     * Delete post
     */
    public function delete($id)
    {
        $this->auth->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/posts');
            exit;
        }

        $post = $this->postModel->getById($id);
        if (!$post) {
            Session::setFlash('error', 'Post not found');
            header('Location: /cpsproject/posts');
            exit;
        }

        // Check permissions
        if (!$this->auth->isAdmin() && $post['user_id'] !== $this->auth->id()) {
            Session::setFlash('error', 'Access denied');
            header('Location: /cpsproject/posts');
            exit;
        }

        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/posts');
            exit;
        }

        if ($this->postModel->delete($id)) {
            Session::setFlash('success', 'Post deleted successfully');
        } else {
            Session::setFlash('error', 'Failed to delete post');
        }

        header('Location: /cpsproject/posts');
        exit;
    }

    /**
     * Add comment to post
     */
    public function addComment()
    {
        $this->auth->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/posts');
            exit;
        }

        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/posts');
            exit;
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (empty($content)) {
            Session::setFlash('error', 'Comment content is required');
            header('Location: /cpsproject/post/' . $postId);
            exit;
        }

        // Check if post exists
        $post = $this->postModel->getById($postId);
        if (!$post) {
            Session::setFlash('error', 'Post not found');
            header('Location: /cpsproject/posts');
            exit;
        }

        if ($this->commentModel->create($postId, $this->auth->id(), $content, $parentId)) {
            Session::setFlash('success', 'Comment added successfully and is pending approval');
        } else {
            Session::setFlash('error', 'Failed to add comment');
        }

        header('Location: /cpsproject/post/' . $postId);
        exit;
    }
}