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
          fetch('/cpsproject/assessment/pathways/' + categoryId)
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

<?php
// File: views/assessment/results.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Assessment Results', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <main class="mt-8">
      <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
          <h2 class="text-lg font-semibold text-green-800 mb-2">Assessment Complete!</h2>
          <p class="text-green-700">
            You answered <?php echo $totalQuestions; ?> questions. Here are your results based on your performance.
          </p>
        </div>

        <?php if ($recommendation): ?>
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-blue-800 mb-3">🎯 Recommended Pathway</h2>
            <h3 class="text-lg font-semibold text-blue-900 mb-2"><?php echo htmlentities($recommendation['pathway_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p class="text-blue-800 mb-3"><?php echo htmlentities($recommendation['pathway_description'], ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="flex items-center space-x-4">
              <div class="text-2xl font-bold text-blue-900"><?php echo number_format($recommendation['percentage'], 1); ?>%</div>
              <div class="flex-1 bg-blue-200 rounded-full h-3">
                <div class="bg-blue-600 h-3 rounded-full" style="width: <?php echo $recommendation['percentage']; ?>%"></div>
              </div>
              <div class="text-sm text-blue-700">
                <?php echo $recommendation['correct_answers']; ?>/<?php echo $recommendation['total_questions']; ?> correct
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Detailed Results -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Detailed Results</h2>
        
        <div class="grid gap-4">
          <?php foreach ($results as $result): ?>
            <div class="border border-gray-200 rounded-lg p-4 <?php echo $result === $recommendation ? 'ring-2 ring-blue-500 bg-blue-50' : ''; ?>">
              <div class="flex justify-between items-start mb-3">
                <div>
                  <h3 class="text-lg font-semibold text-gray-800">
                    <?php echo htmlentities($result['pathway_name'], ENT_QUOTES, 'UTF-8'); ?>
                    <?php if ($result === $recommendation): ?>
                      <span class="ml-2 text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Recommended</span>
                    <?php endif; ?>
                  </h3>
                  <p class="text-gray-600 text-sm"><?php echo htmlentities($result['pathway_description'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="text-right">
                  <div class="text-2xl font-bold text-gray-800"><?php echo number_format($result['percentage'], 1); ?>%</div>
                  <div class="text-sm text-gray-500">
                    <?php echo $result['correct_answers']; ?>/<?php echo $result['total_questions']; ?> correct
                  </div>
                </div>
              </div>
              
              <div class="mb-2">
                <div class="flex justify-between items-center mb-1">
                  <span class="text-sm text-gray-600">Performance</span>
                  <span class="text-sm font-medium text-gray-700"><?php echo number_format($result['percentage'], 1); ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="h-2 rounded-full transition-all duration-500 <?php 
                    if ($result['percentage'] >= 80) echo 'bg-green-500';
                    elseif ($result['percentage'] >= 60) echo 'bg-yellow-500';
                    else echo 'bg-red-500';
                  ?>" style="width: <?php echo $result['percentage']; ?>%"></div>
                </div>
              </div>

              <div class="text-sm text-gray-600">
                <?php
                if ($result['percentage'] >= 80) {
                  echo "Excellent fit! You showed strong aptitude for this pathway.";
                } elseif ($result['percentage'] >= 60) {
                  echo "Good potential. Consider exploring this pathway further.";
                } else {
                  echo "This pathway might be challenging, but don't let that discourage you if you're passionate about it.";
                }
                ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Action buttons -->
        <div class="mt-8 flex flex-wrap gap-4 justify-center">
          <a href="/cpsproject/assessment" 
             class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors">
            Take Another Assessment
          </a>
          <a href="/cpsproject/pathway" 
             class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700 transition-colors">
            Explore Pathways
          </a>
          <a href="/cpsproject" 
             class="bg-gray-600 text-white py-2 px-6 rounded-lg hover:bg-gray-700 transition-colors">
            Back to Home
          </a>
        </div>
      </div>
    </main>
  </div>
</body>
</html>