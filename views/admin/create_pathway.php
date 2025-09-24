<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Create Pathway', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/admin_navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>

      <?php if (!empty($error)): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-800"><?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
      <?php endif; ?>

      <form method="post" action="/cpsproject/admin/pathways/create" enctype="multipart/form-data" class="space-y-6">
        <?php echo \core\CSRF::inputField(); ?>

        <div>
          <label for="name" class="block text-sm font-medium text-gray-700">Pathway Name</label>
          <input type="text" id="name" name="name" required
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
          <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
          <select id="category_id" name="category_id" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">Choose a category...</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo $category['id']; ?>"><?php echo htmlentities($category['name'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea id="description" name="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>

        <div>
          <label for="pathway_image" class="block text-sm font-medium text-gray-700">Pathway Image</label>
          <input type="file" id="pathway_image" name="pathway_image" accept="image/jpeg,image/png,image/gif"
                 class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div class="pt-4">
          <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">Create Pathway</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>