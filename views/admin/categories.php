<?php
// File: views/admin/categories.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Manage Categories', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/admin_navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>
        <a href="/cpsproject/admin/categories/create"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
          Create Category
        </a>
      </div>

      <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
          Category action completed successfully.
        </div>
      <?php endif; ?>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pathways</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($categories as $category): ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                  <?php echo htmlentities($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  <?php echo htmlentities($category['description'] ?? 'No description', ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <?php echo $category['pathway_count']; ?> pathway(s)
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <a href="/cpsproject/admin/categories/<?php echo $category['id']; ?>/edit"
                    class="text-blue-600 hover:text-blue-900">Edit</a>
                  <form method="POST" action="/cpsproject/admin/categories/<?php echo $category['id']; ?>/delete"
                    class="inline" onsubmit="return confirm('Delete this category and all its pathways?')">
                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                  </form>
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