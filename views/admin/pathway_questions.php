<?php
// File: views/admin/pathway_questions.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlentities($title ?? 'Pathway Questions', ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-5xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/admin_navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold"><?php echo htmlentities($title ?? 'Pathway Questions', ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="space-x-2">
          <a href="/cpsproject/admin/questions/create?pathway=<?php echo urlencode($pathway['id'] ?? ''); ?>"
             class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 text-sm">Add Question</a>
          <a href="/cpsproject/admin/questions" class="bg-gray-200 text-gray-800 px-3 py-2 rounded hover:bg-gray-300 text-sm">Back</a>
        </div>
      </div>

      <?php if (!empty($questions)): ?>
        <div class="space-y-4">
          <?php foreach ($questions as $q): ?>
            <div class="p-4 border rounded-lg">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-medium text-gray-800"><?php echo htmlentities($q['question_text'] ?? ($q['text'] ?? 'Question'), ENT_QUOTES, 'UTF-8'); ?></h3>
                  <p class="text-sm text-gray-500 mt-1">Type: <?php echo htmlentities($q['question_type'] ?? 'multiple_choice', ENT_QUOTES, 'UTF-8'); ?> — Difficulty: <?php echo htmlentities($q['difficulty_level'] ?? ($q['difficulty'] ?? 'medium'), ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="text-right text-sm space-y-2">
                  <a href="/cpsproject/admin/questions/<?php echo urlencode($q['id'] ?? ''); ?>/edit" class="text-blue-600 hover:underline">Edit</a>
                  <a href="/cpsproject/admin/questions/<?php echo urlencode($q['id'] ?? ''); ?>/delete" class="text-red-600 hover:underline" onclick="return confirm('Delete this question?')">Delete</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="p-6 text-center text-gray-600">
          No questions found for this pathway.
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>