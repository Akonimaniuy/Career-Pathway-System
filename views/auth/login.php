<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-md mx-auto py-12 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <h1 class="text-xl font-semibold text-gray-800">Login</h1>

      <?php if (!empty($error)): ?>
        <div class="mt-4 text-sm text-red-700 bg-red-50 border border-red-100 p-3 rounded">
          <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="" class="mt-4 space-y-4">
        <?php echo \core\CSRF::inputField(); ?>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex items-center justify-between">
          <label class="inline-flex items-center text-sm">
            <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
            <span class="ml-2 text-gray-700">Remember me</span>
          </label>
          <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
        </div>

        <div>
          <button type="submit" class="w-full inline-flex justify-center py-2 px-4 rounded-md bg-blue-600 text-white hover:bg-blue-700">Login</button>
        </div>
      </form>

      <p class="mt-4 text-sm text-gray-600">Don't have an account? <a href="/cpsproject/register" class="text-blue-600 hover:underline">Register</a></p>
    </div>
  </div>
</body>
</html>