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