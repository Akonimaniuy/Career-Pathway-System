<?php
// File: views/admin/questions.php - Complete Version
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Manage Questions', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/admin_navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>
        <a href="/cpsproject/admin/questions/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
          Create Question
        </a>
      </div>

      <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
          Question action completed successfully.
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <!-- Question Statistics by Pathway -->
      <?php if (isset($questionStats) && is_array($questionStats) && count($questionStats) > 0): ?>
        <div class="mb-8">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Question Statistics by Pathway</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($questionStats as $stat): ?>
              <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-medium text-gray-800"><?php echo htmlentities($stat['pathway_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="text-2xl font-bold text-blue-600"><?php echo $stat['question_count']; ?></p>
                <p class="text-sm text-gray-500">questions</p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Pathways List -->
      <?php if (isset($pathways) && is_array($pathways) && count($pathways) > 0): ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pathway</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Questions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php foreach ($pathways as $pathway): ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                  <?php echo htmlentities($pathway['name'], ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    <?php echo htmlentities($pathway['category_name'] ?? 'No Category', ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <?php 
                  $questionCount = 0;
                  if (isset($questionStats)) {
                    foreach ($questionStats as $stat) {
                      if ($stat['pathway_name'] === $pathway['name']) {
                        $questionCount = $stat['question_count'];
                        break;
                      }
                    }
                  }
                  echo $questionCount;
                  ?> questions
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                  <a href="/cpsproject/admin/questions/create?pathway=<?php echo $pathway['id']; ?>" 
                     class="text-green-600 hover:text-green-900">Add Question</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="text-center py-8">
          <p class="text-gray-500">No pathways found. Create pathways first to add questions.</p>
          <a href="/cpsproject/admin/pathways" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Manage Pathways →</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>