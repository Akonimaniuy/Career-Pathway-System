<?php
// File: views/layout/admin_navbar.php (Updated)
// Admin-specific navbar with admin links including questions

$isLogged = false;
$username = '';
$isAdmin = false;

// determine current path relative to app base
$fullPath = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($fullPath, PHP_URL_PATH);
$basePath = '/cpsproject';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = trim($path, '/');
$segments = $path === '' ? [] : explode('/', $path);
$current = $segments[0] ?? '';

// Attempt to read auth state if available
if (class_exists('\\core\\Auth')) {
    try {
        $auth = new \core\Auth();
        if ($auth->check()) {
            $isLogged = true;
            $u = $auth->user();
            $username = $u['name'] ?? $u['email'] ?? ($u['id'] ?? '');
            $isAdmin = ($u['role'] ?? 'user') === 'admin';
        }
    } catch (Throwable $e) {
        // silent fallback to guest view
        $isLogged = false;
    }
}
?>
<nav class="bg-red-800 border-b border-red-900">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex justify-between items-center h-16">
      <div class="flex items-center">
        <a href="/cpsproject/admin" class="text-xl font-semibold text-white mr-6">
          <span class="bg-red-600 px-2 py-1 rounded text-sm mr-2">ADMIN</span>
          Career Path System
        </a>
        <div class="hidden md:flex space-x-4">
          <a href="/cpsproject/admin" class="text-red-100 hover:text-white">Dashboard</a>
          <a href="/cpsproject/admin/users" class="text-red-100 hover:text-white">Users</a>
          <a href="/cpsproject/admin/categories" class="text-red-100 hover:text-white">Categories</a>
          <a href="/cpsproject/admin/pathways" class="text-red-100 hover:text-white">Pathways</a>
          <a href="/cpsproject/admin/questions" class="text-red-100 hover:text-white">Questions</a>
          <a href="/cpsproject" class="text-red-200 hover:text-red-100 text-sm">← Back to Site</a>
        </div>
      </div>

      <div class="flex items-center space-x-4">
        <?php if ($isLogged): ?>
          <span class="text-sm text-red-100 hidden sm:inline">
            <?php echo htmlentities($username, ENT_QUOTES, 'UTF-8'); ?>
            <?php if ($isAdmin): ?>
              <span class="ml-1 text-xs bg-red-600 px-1 py-0.5 rounded">Admin</span>
            <?php endif; ?>
          </span>
          <a href="/cpsproject/logout" class="px-3 py-2 rounded-md text-sm font-medium bg-red-600 text-white hover:bg-red-500">Logout</a>
        <?php else: ?>
          <a href="/cpsproject/login" class="px-3 py-2 rounded-md text-sm font-medium bg-red-600 text-white hover:bg-red-500">Login</a>
        <?php endif; ?>

        <button id="admin-mobile-menu-button" class="md:hidden p-2 rounded-md focus:outline-none focus:ring text-red-100">
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <div id="admin-mobile-menu" class="md:hidden hidden border-t border-red-700">
    <div class="px-2 py-3 space-y-1">
      <a href="/cpsproject/admin" class="block px-3 py-2 rounded-md text-base font-medium text-red-100 hover:bg-red-700">Dashboard</a>
      <a href="/cpsproject/admin/users" class="block px-3 py-2 rounded-md text-base font-medium text-red-100 hover:bg-red-700">Users</a>
      <a href="/cpsproject/admin/categories" class="block px-3 py-2 rounded-md text-base font-medium text-red-100 hover:bg-red-700">Categories</a>
      <a href="/cpsproject/admin/pathways" class="block px-3 py-2 rounded-md text-base font-medium text-red-100 hover:bg-red-700">Pathways</a>
      <a href="/cpsproject/admin/questions" class="block px-3 py-2 rounded-md text-base font-medium text-red-100 hover:bg-red-700">Questions</a>
      <a href="/cpsproject" class="block px-3 py-2 rounded-md text-base font-medium text-red-200 hover:bg-red-700">← Back to Site</a>
      <?php if ($isLogged): ?>
        <a href="/cpsproject/logout" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-red-600">Logout</a>
      <?php else: ?>
        <a href="/cpsproject/login" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-red-600">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Mobile menu toggle for admin navbar
    (function () {
      var btn = document.getElementById('admin-mobile-menu-button');
      var menu = document.getElementById('admin-mobile-menu');
      if (!btn || !menu) return;
      btn.addEventListener('click', function () {
        menu.classList.toggle('hidden');
      });
    })();
  </script>
</nav>