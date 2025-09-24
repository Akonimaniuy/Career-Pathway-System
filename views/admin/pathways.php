<?php
// File: views/admin/pathways.php - Complete Version
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Manage Pathways', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/admin_navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>
        <a href="/cpsproject/admin/pathways/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
          Create Pathway
        </a>
      </div>

      <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
          Pathway action completed successfully.
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <!-- Filter Section -->
      <div class="mb-6">
        <label for="category-filter" class="block text-sm font-medium text-gray-700">Filter by Category</label>
        <select id="category-filter" name="category" class="mt-1 block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
          <option value="">All Categories</option>
          <?php
          $uniqueCategories = [];
          foreach ($pathways as $pathway) {
              $uniqueCategories[$pathway['category_name']] = $pathway['category_name'];
          }
          ksort($uniqueCategories);
          foreach ($uniqueCategories as $categoryName): ?>
            <option value="<?php echo htmlentities($categoryName, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlentities($categoryName, ENT_QUOTES, 'UTF-8'); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($pathways as $pathway): ?>
            <tr class="pathway-row" data-category="<?php echo htmlentities($pathway['category_name'], ENT_QUOTES, 'UTF-8'); ?>">
              <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                <?php echo htmlentities($pathway['name'], ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                  <?php echo htmlentities($pathway['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                <?php echo htmlentities($pathway['description'] ?? 'No description', ENT_QUOTES, 'UTF-8'); ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                <a href="/cpsproject/admin/pathways/<?php echo $pathway['id']; ?>/edit" 
                   class="text-blue-600 hover:text-blue-900">Edit</a>
                <form method="POST" action="/cpsproject/admin/pathways/<?php echo $pathway['id']; ?>/delete" 
                      class="inline" onsubmit="return confirm('Delete this pathway?')">
                  <?php echo \core\CSRF::inputField(); ?>
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const categoryFilter = document.getElementById('category-filter');
      const pathwayRows = document.querySelectorAll('.pathway-row');

      if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
          const selectedCategory = this.value;
          let visibleCount = 0;

          pathwayRows.forEach(row => {
            const rowCategory = row.getAttribute('data-category');
            if (selectedCategory === '' || rowCategory === selectedCategory) {
              row.style.display = '';
              visibleCount++;
            } else {
              row.style.display = 'none';
            }
          });

          // Optional: Show a message if no rows are visible
          // You would need to add an element with id="no-pathways-row" for this to work
        });
      }
    });
  </script>

</body>
</html>