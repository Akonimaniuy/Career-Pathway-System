<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'User', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>

      <div class="mt-4 bg-gray-50 p-4 rounded-md">
        <h2 class="text-lg font-semibold text-gray-800">User Information</h2>
        <dl class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <dt class="text-xs font-medium text-gray-500">ID</dt>
            <dd class="mt-1 text-sm text-gray-700"><?php echo htmlentities($user['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-500">Name</dt>
            <dd class="mt-1 text-sm text-gray-700"><?php echo htmlentities($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-500">Email</dt>
            <dd class="mt-1 text-sm text-gray-700"><?php echo htmlentities($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-gray-500">Created</dt>
            <dd class="mt-1 text-sm text-gray-700"><?php echo htmlentities($user['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
          </div>
        </dl>
      </div>

      <div class="mt-4">
        <a href="/cpsproject/users" class="text-sm text-gray-600 hover:underline">← Back to Users</a>
      </div>
    </div>
  </div>
</body>
</html>