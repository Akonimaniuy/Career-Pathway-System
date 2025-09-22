<?php
// File: views/admin/bulk_import.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Bulk Import Questions', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/admin_navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>

      <!-- Step 1: Download Template -->
      <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h2 class="text-lg font-semibold text-blue-800 mb-2">Step 1: Download Excel Template</h2>
        <p class="text-blue-700 mb-4">Download the Excel template to format your questions properly.</p>
        <a href="/cpsproject/admin/questions/download-template" 
           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Download Template
        </a>
      </div>

      <!-- Step 2: Upload and Preview -->
      <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Step 2: Upload Questions File</h2>
        
        <form id="upload-form" class="space-y-4">
          <?php echo \core\CSRF::inputField(); ?>
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Select Pathway</label>
            <select name="pathway_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="">Choose a pathway...</option>
              <?php foreach ($pathways as $pathway): ?>
                <option value="<?php echo $pathway['id']; ?>">
                  <?php echo htmlentities($pathway['category_name'] . ' - ' . $pathway['name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Excel File</label>
            <input type="file" name="template" accept=".xlsx,.xls,.csv" required
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
          </div>

          <div>
            <button type="button" onclick="previewQuestions()" 
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
              Preview Questions
            </button>
          </div>
        </form>
      </div>

      <!-- Preview Modal -->
      <div id="preview-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-bold text-gray-900">Preview Questions</h3>
              <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            
            <div id="preview-content">
              <!-- Preview content will be loaded here -->
            </div>
            
            <div class="flex justify-end space-x-4 mt-6">
              <button onclick="closePreview()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                Cancel
              </button>
              <button onclick="importQuestions()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Import Questions
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading indicator -->
      <div id="loading" class="hidden text-center py-4">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Processing...</p>
      </div>
    </div>
  </div>

  <script>
    let previewData = null;

    function previewQuestions() {
      const form = document.getElementById('upload-form');
      const formData = new FormData(form);
      
      if (!formData.get('pathway_id') || !formData.get('template').name) {
        alert('Please select a pathway and upload a file.');
        return;
      }

      document.getElementById('loading').classList.remove('hidden');

      fetch('/cpsproject/upload/preview-questions', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById('loading').classList.add('hidden');
        
        if (data.success) {
          previewData = data;
          showPreview(data);
        } else {
          alert('Error: ' + data.error);
        }
      })
      .catch(error => {
        document.getElementById('loading').classList.add('hidden');
        alert('Error: ' + error.message);
      });
    }

    function showPreview(data) {
      const content = document.getElementById('preview-content');
      let html = `
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded">
          <p class="text-green-800">Found ${data.total_questions} questions to import.</p>
        </div>
      `;

      if (data.errors && data.errors.length > 0) {
        html += `
          <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded">
            <h4 class="text-red-800 font-semibold">Errors found:</h4>
            <ul class="text-red-700 text-sm mt-2">
              ${data.errors.map(error => `<li>• ${error}</li>`).join('')}
            </ul>
          </div>
        `;
      }

      html += `
        <div class="max-h-96 overflow-y-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Difficulty</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Options</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
      `;

      data.questions.forEach(question => {
        const correctOption = question.options.find(opt => opt.is_correct);
        html += `
          <tr>
            <td class="px-4 py-2 text-sm text-gray-900">${question.question_text}</td>
            <td class="px-4 py-2 text-sm text-gray-900">${question.difficulty_level}</td>
            <td class="px-4 py-2 text-sm text-gray-900">
              ${question.options.map(opt => 
                `<span class="${opt.is_correct ? 'font-bold text-green-600' : ''}">${opt.text}</span>`
              ).join(', ')}
            </td>
          </tr>
        `;
      });

      html += `
            </tbody>
          </table>
        </div>
      `;

      content.innerHTML = html;
      document.getElementById('preview-modal').classList.remove('hidden');
    }

    function closePreview() {
      document.getElementById('preview-modal').classList.add('hidden');
      previewData = null;
    }

    function importQuestions() {
      if (!previewData) return;

      document.getElementById('loading').classList.remove('hidden');
      closePreview();

      const form = document.getElementById('upload-form');
      const formData = new FormData();
      formData.append('_csrf', document.querySelector('input[name="_csrf"]').value);
      formData.append('pathway_id', form.querySelector('select[name="pathway_id"]').value);
      formData.append('questions_data', JSON.stringify(previewData.questions));

      fetch('/cpsproject/admin/questions/bulk-import', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById('loading').classList.add('hidden');
        
        if (data.success) {
          alert(`Successfully imported ${data.imported} out of ${data.total} questions.`);
          if (data.errors && data.errors.length > 0) {
            alert('Some errors occurred:\n' + data.errors.join('\n'));
          }
          window.location.href = '/cpsproject/admin/questions?success=bulk_import';
        } else {
          alert('Error: ' + data.error);
        }
      })
      .catch(error => {
        document.getElementById('loading').classList.add('hidden');
        alert('Error: ' + error.message);
      });
    }
  </script>
</body>
</html>