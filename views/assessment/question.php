<?php
// File: views/assessment/question.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Question', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <main class="mt-8">
      <!-- Progress indicator -->
      <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm font-medium text-gray-700">Question <?php echo $questionNumber; ?></span>
          <span class="text-sm text-gray-500"><?php echo htmlentities($question['pathway_name'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: <?php echo min(($questionNumber / 15) * 100, 100); ?>%"></div>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
          <?php echo htmlentities($question['question_text'], ENT_QUOTES, 'UTF-8'); ?>
        </h1>

        <?php if (isset($_GET['error'])): ?>
          <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
            <?php 
            $error = $_GET['error'];
            if ($error === 'no_answer') {
              echo 'Please select an answer before continuing.';
            } elseif ($error === 'invalid_token') {
              echo 'Invalid form token. Please try again.';
            } else {
              echo 'An error occurred. Please try again.';
            }
            ?>
          </div>
        <?php endif; ?>

        <form method="post" action="/cpsproject/assessment/answer/<?php echo $sessionId; ?>">
          <?php echo \core\CSRF::inputField(); ?>
          <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">

          <div class="space-y-3 mb-6">
            <?php foreach ($question['options'] as $option): ?>
              <label class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                <input type="radio" name="answer" value="<?php echo $option['id']; ?>" required
                       class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="ml-3 text-gray-700"><?php echo htmlentities($option['option_text'], ENT_QUOTES, 'UTF-8'); ?></span>
              </label>
            <?php endforeach; ?>
          </div>

          <div class="flex justify-between items-center">
            <p class="text-sm text-gray-500">
              Note: You cannot go back to previous questions.
            </p>
            <button type="submit" 
                    class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 font-medium transition-colors">
              Next Question
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <script>
    // Auto-submit after selection (optional enhancement)
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('form');
      const radioButtons = document.querySelectorAll('input[type="radio"]');
      
      radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
          // Optional: Add slight delay before auto-submit
          // setTimeout(() => form.submit(), 500);
        });
      });
    });
  </script>
</body>
</html>