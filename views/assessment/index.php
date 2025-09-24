<?php
// File: views/assessment/index.php (Assessment Selection)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Assessment', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <main class="mt-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo htmlentities($title ?? 'Assessment', ENT_QUOTES, 'UTF-8'); ?></h1>
        
        <p class="text-gray-600 mb-6"><?php echo htmlentities($message ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

        <?php if (!empty($error)): ?>
          <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800"><?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?></p>
          </div>
        <?php endif; ?>

        <form method="post" action="/cpsproject/assessment/start" class="space-y-6">
          <?php echo \core\CSRF::inputField(); ?>

          <!-- Category Selection -->
          <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
              Select a Category
            </label>
            <select id="category_id" name="category_id" required 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="">Choose a category...</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>">
                  <?php echo htmlentities($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Pathway Selection -->
          <div id="pathway-selection" style="display: none;">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Select at least 2 pathways to compare
            </label>
            <div id="pathway-checkboxes" class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <!-- Pathways will be loaded via JavaScript -->
            </div>
            <p class="text-sm text-gray-500 mt-2">
              Select 2 or more pathways for a comprehensive assessment comparison.
            </p>
          </div>

          <div class="pt-4">
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 font-medium text-lg transition-colors">
              Start Assessment
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const categorySelect = document.getElementById('category_id');
      const pathwaySelection = document.getElementById('pathway-selection');
      const pathwayContainer = document.getElementById('pathway-checkboxes');

      categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        if (categoryId) {
          // Show pathway selection
          pathwaySelection.style.display = 'block';
          
          // Clear existing pathways
          pathwayContainer.innerHTML = '<div class="col-span-full text-center py-4">Loading pathways...</div>';
          
          // Fetch pathways for this category
          fetch('/cpsproject/assessment/pathways-by-category/' + categoryId)
            .then(response => response.json())
            .then(data => {
              pathwayContainer.innerHTML = '';
              
              if (data.success && data.pathways && data.pathways.length > 0) {
                data.pathways.forEach(function(pathway) {
                  const checkboxDiv = document.createElement('div');
                  checkboxDiv.className = 'flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50';
                  
                  checkboxDiv.innerHTML = `
                    <input type="checkbox" id="pathway_${pathway.id}" name="pathways[]" value="${pathway.id}" 
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="pathway_${pathway.id}" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">
                      ${escapeHtml(pathway.name)}
                      ${pathway.description ? `<div class="text-xs text-gray-500 mt-1">${escapeHtml(pathway.description)}</div>` : ''}
                    </label>
                  `;
                  
                  pathwayContainer.appendChild(checkboxDiv);
                });
              } else {
                pathwayContainer.innerHTML = '<div class="col-span-full text-center py-4 text-gray-500">No pathways available for this category.</div>';
              }
            })
            .catch(error => {
              console.error('Error loading pathways:', error);
              pathwayContainer.innerHTML = '<div class="col-span-full text-center py-4 text-red-500">Error loading pathways. Please try again.</div>';
            });
        } else {
          pathwaySelection.style.display = 'none';
        }
      });

      function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }
    });
  </script>
</body>
</html>
