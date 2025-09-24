<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Edit Question', ENT_QUOTES, 'UTF-8'); ?></title>
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

      <form method="post" action="/cpsproject/admin/questions/<?php echo $question['id']; ?>/edit" class="space-y-6">
        <?php echo \core\CSRF::inputField(); ?>

        <div>
          <label for="pathway_id" class="block text-sm font-medium text-gray-700">Pathway</label>
          <select id="pathway_id" name="pathway_id" required disabled
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed">
            <?php foreach ($pathways as $pathway): ?>
              <option value="<?php echo $pathway['id']; ?>" <?php echo ($question['pathway_id'] == $pathway['id']) ? 'selected' : ''; ?>>
                <?php echo htmlentities($pathway['name'], ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <p class="text-xs text-gray-500 mt-1">Changing the pathway for a question is not supported. Please create a new question for a different pathway.</p>
        </div>

        <div>
          <label for="question_text" class="block text-sm font-medium text-gray-700">Question Text</label>
          <textarea id="question_text" name="question_text" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"><?php echo htmlentities($question['question_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="question_type" class="block text-sm font-medium text-gray-700">Question Type</label>
                <select id="question_type" name="question_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="multiple_choice" <?php echo ($question['question_type'] ?? 'multiple_choice') === 'multiple_choice' ? 'selected' : ''; ?>>Multiple Choice</option>
                </select>
            </div>
            <div>
                <label for="difficulty_level" class="block text-sm font-medium text-gray-700">Difficulty Level</label>
                <select id="difficulty_level" name="difficulty_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="easy" <?php echo ($question['difficulty_level'] ?? 'medium') === 'easy' ? 'selected' : ''; ?>>Easy</option>
                    <option value="medium" <?php echo ($question['difficulty_level'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="hard" <?php echo ($question['difficulty_level'] ?? 'medium') === 'hard' ? 'selected' : ''; ?>>Hard</option>
                </select>
            </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Answer Options</label>
          <div id="options-container" class="mt-2 space-y-3">
            <?php 
            $options = $question['options'] ?? [];
            for ($i = 0; $i < 4; $i++): 
                $option = $options[$i] ?? ['option_text' => '', 'is_correct' => 0];
            ?>
              <div class="flex items-center space-x-3">
                <input type="radio" name="correct_answer" value="<?php echo $i; ?>" <?php echo $option['is_correct'] ? 'checked' : ''; ?>
                       class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <input type="text" name="options[<?php echo $i; ?>]" placeholder="Option <?php echo $i + 1; ?>" value="<?php echo htmlentities($option['option_text'], ENT_QUOTES, 'UTF-8'); ?>"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
              </div>
            <?php endfor; ?>
          </div>
          <p class="text-xs text-gray-500 mt-1">Select the radio button for the correct answer.</p>
        </div>

        <div class="pt-4 flex justify-between items-center">
          <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">Update Question</button>
          <a href="/cpsproject/admin/questions" class="text-sm text-gray-600 hover:underline">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>



<!--
[PROMPT_SUGGESTION]Add a delete button for each question in the list.[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]Can you implement the "Create Question" page now?[/PROMPT_SUGGESTION]
-->