<?php
// File: views/admin/create_category.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Create Category', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/admin_navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>

      <?php if (!empty($error)): ?>
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-100 p-3 rounded">
          <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form method="post" class="space-y-6">
        <?php echo \core\CSRF::inputField(); ?>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Category Name</label>
          <input type="text" name="name" required
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea name="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>

        <div class="flex space-x-4">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Create Category
          </button>
          <a href="/cpsproject/admin/categories" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>