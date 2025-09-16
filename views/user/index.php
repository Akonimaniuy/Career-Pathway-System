<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Users', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>

      <div class="overflow-x-auto mt-4">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($users as $user): ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlentities($user['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlentities($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlentities($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <a href="/cpsproject/user/<?php echo urlencode($user['id']); ?>" class="inline-flex items-center px-3 py-1 rounded-md text-sm bg-blue-600 text-white hover:bg-blue-700">View</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>