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
        <div class="flex space-x-2">
          <a href="/cpsproject/admin/questions/create" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Create Question
          </a>
          <a href="/cpsproject/admin/questions/bulk-import" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Bulk Import
          </a>
        </div>
      </div>

      <?php if (!empty($error)): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-800"><?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
      <?php endif; ?>

      <!-- Pathways & Questions -->
      <div class="space-y-8">
        <?php if (empty($groupedQuestions)): ?>
          <div id="no-questions-message" class="text-center py-12 text-gray-500">
            <p>Please select a pathway above to view its questions.</p>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php foreach ($categories as $category): ?>
              <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4"><?php echo htmlentities($category['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <select class="pathway-selector w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition" data-category-id="<?php echo $category['id']; ?>">
                  <option value="">-- Select a Pathway --</option>
                  <?php foreach ($pathways as $pathway): ?>
                    <?php if ($pathway['category_id'] == $category['id']): ?>
                      <option value="<?php echo $pathway['id']; ?>"><?php echo htmlentities($pathway['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endforeach; ?>
          </div>

          <div id="questions-display-area" class="mt-8">
            <?php foreach ($groupedQuestions as $categoryName => $pathwaysInCategory): ?>
              <?php foreach ($pathwaysInCategory as $pathwayName => $pathwayData): ?>
                <div id="pathway-<?php echo $pathwayData['id']; ?>" class="pathway-container hidden bg-white rounded-lg border border-gray-200 shadow-sm">
                  <div class="w-full flex justify-between items-center p-5 text-left border-b border-gray-200">
                  <span class="font-bold text-lg text-gray-800"><?php echo htmlentities($pathwayName, ENT_QUOTES, 'UTF-8'); ?></span>
                  <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                    <?php echo count($pathwayData['questions']); ?> Questions
                  </span>
                </div>
                <div class="questions-list p-4">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                      <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                      <?php foreach ($pathwayData['questions'] as $index => $question): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                          <td class="px-4 py-3">
                            <div class="text-sm text-gray-900 font-medium"><?php echo htmlentities(substr($question['question_text'], 0, 100), ENT_QUOTES, 'UTF-8'); ?><?php echo strlen($question['question_text']) > 100 ? '...' : ''; ?></div>
                          </td>
                          <td class="px-4 py-3">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                              <?php 
                                switch($question['difficulty_level']) {
                                  case 'easy': echo 'bg-green-100 text-green-800'; break;
                                  case 'medium': echo 'bg-yellow-100 text-yellow-800'; break;
                                  case 'hard': echo 'bg-red-100 text-red-800'; break;
                                }
                              ?>">
                              <?php echo htmlentities(ucfirst($question['difficulty_level']), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                          </td>
                          <td class="px-4 py-3 text-right text-sm font-medium">
                            <a href="/cpsproject/admin/questions/<?php echo $question['id']; ?>/edit" class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</a>
                            <!-- Add delete form/button here if needed -->
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const pathwaySelectors = document.querySelectorAll('.pathway-selector');
      const pathwayContainers = document.querySelectorAll('.pathway-container');
      const noQuestionsMessage = document.getElementById('no-questions-message');
      const questionsDisplayArea = document.getElementById('questions-display-area');

      pathwaySelectors.forEach(selector => {
        selector.addEventListener('change', function() {
          const selectedPathwayId = this.value;

          // Reset other dropdowns
          pathwaySelectors.forEach(otherSelector => {
            if (otherSelector !== this) {
              otherSelector.value = '';
            }
          });

          // Hide all pathway containers
          pathwayContainers.forEach(container => {
            container.classList.add('hidden');
          });

          if (selectedPathwayId) {
            const targetContainer = document.getElementById('pathway-' + selectedPathwayId);
            if (targetContainer) {
              questionsDisplayArea.appendChild(targetContainer); // Move it to the display area
              targetContainer.classList.remove('hidden');
              if(noQuestionsMessage) noQuestionsMessage.classList.add('hidden');
            } else if (noQuestionsMessage) {
              noQuestionsMessage.classList.remove('hidden');
              noQuestionsMessage.querySelector('p').textContent = 'This pathway currently has no questions.';
            }
          } else {
            if(noQuestionsMessage) noQuestionsMessage.classList.remove('hidden');
          }
        });
      });
    });
  </script>
</body>
</html>


<!--
[PROMPT_SUGGESTION]Now, can you make the "Edit" button work on the questions list page?[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Add a delete button for each question in the list.[/PROMPT_SUGGESTION]
-->