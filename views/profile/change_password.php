<?php
// File: views/profile/change_password.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Change Password', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-2xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>
        <a href="/cpsproject/profile" class="text-gray-600 hover:text-gray-800">← Back to Profile</a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form method="post" class="space-y-6">
        <?php echo \core\CSRF::inputField(); ?>

        <div>
          <label class="block text-sm font-medium text-gray-700">Current Password</label>
          <input type="password" name="current_password" required
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">New Password</label>
          <input type="password" name="new_password" required minlength="8"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
          <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters long</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
          <input type="password" name="confirm_password" required minlength="8"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex space-x-4">
          <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Change Password
          </button>
          <a href="/cpsproject/profile" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>