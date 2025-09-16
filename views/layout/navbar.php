<?php
// Safe navbar that shows login/register when guest, and welcome + logout when authenticated.
// Hides "Register" when on register page and "Login" when on login page.

$isLogged = false;
$username = '';

// determine current path relative to app base
$fullPath = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($fullPath, PHP_URL_PATH);
$basePath = '/cpsproject';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}
$path = trim($path, '/'); // e.g. "", "login", "register", "user/3"
$segments = $path === '' ? [] : explode('/', $path);
$current = $segments[0] ?? ''; // top-level segment

// Attempt to read auth state if available
if (class_exists('\\core\\Auth')) {
    try {
        $auth = new \core\Auth();
        if ($auth->check()) {
            $isLogged = true;
            $u = $auth->user();
            $username = $u['name'] ?? $u['email'] ?? ($u['id'] ?? '');
        }
    } catch (Throwable $e) {
        // silent fallback to guest view
        $isLogged = false;
    }
}
?>
<nav class="bg-white border-b">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex justify-between items-center h-16">
      <div class="flex items-center">
        <a href="/cpsproject" class="text-xl font-semibold text-gray-800 mr-6">Career Path System</a>
        <div class="hidden md:flex space-x-4">
          <a href="/cpsproject" class="text-gray-600 hover:text-gray-900">Home</a>
          <a href="/cpsproject/users" class="text-gray-600 hover:text-gray-900">Users</a>
          <a href="/cpsproject/about" class="text-gray-600 hover:text-gray-900">About</a>
          <a href="/cpsproject/pathway" class="text-gray-600 hover:text-gray-900">Pathways</a>
          <a href="/cpsproject/assessment" class="text-gray-600 hover:text-gray-900">Assessment</a>

        </div>
      </div>

      <div class="flex items-center space-x-4">
        <?php if ($isLogged): ?>
          <span class="text-sm text-gray-700 hidden sm:inline">Welcome, <?php echo htmlentities($username, ENT_QUOTES, 'UTF-8'); ?></span>
          <a href="/cpsproject/logout" class="px-3 py-2 rounded-md text-sm font-medium bg-red-100 text-red-700 hover:bg-red-200">Logout</a>
        <?php else: ?>
          <button id="login-modal-btn" class="px-3 py-2 rounded-md text-sm font-medium bg-blue-600 text-white hover:bg-blue-700">Login</button>
          <button id="register-modal-btn" class="px-3 py-2 rounded-md text-sm font-medium border border-gray-200 text-gray-700 hover:bg-gray-50">Register</button>
        <?php endif; ?>

        <button id="mobile-menu-button" class="md:hidden p-2 rounded-md focus:outline-none focus:ring">
          <svg class="h-6 w-6 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <div id="mobile-menu" class="md:hidden hidden border-t">
    <div class="px-2 py-3 space-y-1">
      <a href="/cpsproject" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Home</a>
      <a href="/cpsproject/users" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Users</a>
      <a href="/cpsproject/about" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">About</a>
      <a href="/cpsproject/what" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">What me</a>
      <?php if ($isLogged): ?>
        <a href="/cpsproject/logout" class="block px-3 py-2 rounded-md text-base font-medium text-red-700 hover:bg-red-50">Logout</a>
      <?php else: ?>
        <button id="mobile-login-btn" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-white bg-blue-600">Login</button>
        <button id="mobile-register-btn" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium border border-gray-200">Register</button>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // simple toggle for mobile menu
    (function () {
      var btn = document.getElementById('mobile-menu-button');
      var menu = document.getElementById('mobile-menu');
      if (!btn || !menu) return;
      btn.addEventListener('click', function () {
        menu.classList.toggle('hidden');
      });
    })();
  </script>
</nav>

