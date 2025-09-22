<?php
// File: views/admin/users.php (Enhanced version)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Manage Users', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/admin_navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title ?? 'Manage Users', ENT_QUOTES, 'UTF-8'); ?></h1>
      </div>

      <!-- Success/Error Messages -->
      <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
          <?php 
          $success = $_GET['success'];
          if ($success === 'user_activated') echo 'User activated successfully.';
          elseif ($success === 'user_deactivated') echo 'User deactivated successfully.';
          elseif ($success === 'password_reset') {
            echo 'Password reset successfully.';
            if (isset($_GET['new_password'])) {
              echo ' New password: <strong>' . htmlentities($_GET['new_password'], ENT_QUOTES, 'UTF-8') . '</strong>';
            }
          }
          else echo 'Action completed successfully.';
          ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          <?php 
          $error = $_GET['error'];
          if ($error === 'cannot_deactivate_self') echo 'You cannot deactivate your own account.';
          elseif ($error === 'activation_failed') echo 'Failed to activate user.';
          elseif ($error === 'deactivation_failed') echo 'Failed to deactivate user.';
          elseif ($error === 'reset_failed') echo 'Failed to reset password.';
          elseif ($error === 'invalid_token') echo 'Invalid security token.';
          else echo 'An error occurred.';
          ?>
        </div>
      <?php endif; ?>

      <?php if (isset($users) && is_array($users) && count($users) > 0): ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php foreach ($users as $user): ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlentities($user['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlentities($user['name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlentities($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo ($user['role'] ?? 'user') === 'admin' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'; ?>">
                    <?php echo htmlentities(ucfirst($user['role'] ?? 'user'), ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo ($user['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo htmlentities(ucfirst($user['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlentities(isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <?php if (($user['status'] ?? 'active') === 'active'): ?>
                    <form method="POST" action="/cpsproject/admin/users/<?php echo urlencode($user['id'] ?? ''); ?>/deactivate" class="inline">
                      <?php echo \core\CSRF::inputField(); ?>
                      <button type="submit" class="text-orange-600 hover:text-orange-800" onclick="return confirm('Deactivate this user?')">Deactivate</button>
                    </form>
                  <?php else: ?>
                    <form method="POST" action="/cpsproject/admin/users/<?php echo urlencode($user['id'] ?? ''); ?>/activate" class="inline">
                      <?php echo \core\CSRF::inputField(); ?>
                      <button type="submit" class="text-green-600 hover:text-green-800" onclick="return confirm('Activate this user?')">Activate</button>
                    </form>
                  <?php endif; ?>
                  
                  <form method="POST" action="/cpsproject/admin/users/<?php echo urlencode($user['id'] ?? ''); ?>/reset-password" class="inline">
                    <?php echo \core\CSRF::inputField(); ?>
                    <button type="submit" class="text-blue-600 hover:text-blue-800" onclick="return confirm('Reset password for this user? A new password will be generated.')">Reset Password</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="text-center py-8">
          <p class="text-gray-500">No users found or unable to load users.</p>
          <p class="text-sm text-gray-400 mt-2">Check your database connection and ensure the users table exists.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html