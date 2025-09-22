<?php
// File: views/admin/create_question.php - Complete Version
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Create Question', ENT_QUOTES, 'UTF-8'); ?></title>
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
          <label class="block text-sm font-medium text-gray-700">Pathway</label>
          <select name="pathway_id" required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">Select a pathway...</option>
            <?php if (isset($pathways) && is_array($pathways)): ?>
              <?php foreach ($pathways as $pathway): ?>
                <option value="<?php echo $pathway['id']; ?>" <?php echo (isset($selectedPathway) && $selectedPathway == $pathway['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlentities(($pathway['category_name'] ?? 'Category') . ' - ' . $pathway['name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Question Text</label>
          <textarea name="question_text" required rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter your question here..."></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Question Type</label>
            <select name="question_type"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="multiple_choice">Multiple Choice</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Difficulty Level</label>
            <select name="difficulty_level"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="easy">Easy</option>
              <option value="medium" selected>Medium</option>
              <option value="hard">Hard</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options</label>
          <div id="options-container" class="space-y-3">
            <div class="flex items-center space-x-3">
              <input type="radio" name="correct_answer" value="0" class="h-4 w-4 text-blue-600" required>
              <input type="text" name="options[]" required
                     class="flex-1 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                     placeholder="Option 1">
              <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800">Remove</button>
            </div>
            <div class="flex items-center space-x-3">
              <input type="radio" name="correct_answer" value="1" class="h-4 w-4 text-blue-600" required>
              <input type="text" name="options[]" required
                     class="flex-1 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                     placeholder="Option 2">
              <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800">Remove</button>
            </div>
          </div>
          
          <button type="button" onclick="addOption()" class="mt-3 text-blue-600 hover:text-blue-800 text-sm">
            + Add Another Option
          </button>
          <p class="text-xs text-gray-500 mt-2">Select the radio button next to the correct answer</p>
        </div>

        <div class="flex space-x-4">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
            Create Question
          </button>
          <a href="/cpsproject/admin/questions" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>

  <script>
    let optionCount = 2;

    function addOption() {
      const container = document.getElementById('options-container');
      const optionDiv = document.createElement('div');
      optionDiv.className = 'flex items-center space-x-3';
      optionDiv.innerHTML = `
        <input type="radio" name="correct_answer" value="${optionCount}" class="h-4 w-4 text-blue-600" required>
        <input type="text" name="options[]" required
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
               placeholder="Option ${optionCount + 1}">
        <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800">Remove</button>
      `;
      container.appendChild(optionDiv);
      optionCount++;
    }

    function removeOption(button) {
      const container = document.getElementById('options-container');
      if (container.children.length > 2) {
        button.parentElement.remove();
        updateOptionIndices();
      }
    }

    function updateOptionIndices() {
      const container = document.getElementById('options-container');
      const radioButtons = container.querySelectorAll('input[type="radio"]');
      const textInputs = container.querySelectorAll('input[type="text"]');
      
      radioButtons.forEach((radio, index) => {
        radio.value = index;
      });
      
      textInputs.forEach((input, index) => {
        input.placeholder = `Option ${index + 1}`;
      });
      
      optionCount = radioButtons.length;
    }
  </script>
</body>
</html>