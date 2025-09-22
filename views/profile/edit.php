<?php
// File: views/profile/edit.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Edit Profile', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="mt-8 bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?></h1>
        <a href="/cpsproject/profile" class="text-gray-600 hover:text-gray-800">← Back to Profile</a>
      </div>

      <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
          <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" class="space-y-6">
        <?php echo \core\CSRF::inputField(); ?>

        <!-- Profile Photo Upload -->
        <div class="flex items-center space-x-6">
          <div class="shrink-0">
            <?php if (!empty($profile['profile_photo'])): ?>
              <img id="profile-preview" src="/cpsproject/uploads/<?php echo htmlentities($profile['profile_photo'], ENT_QUOTES, 'UTF-8'); ?>" 
                   alt="Profile Photo" 
                   class="h-20 w-20 object-cover rounded-full">
            <?php else: ?>
              <div id="profile-preview" class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
            <?php endif; ?>
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Profile Photo</label>
            <input type="file" name="profile_photo" accept="image/*" onchange="previewImage(this)"
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF up to 10MB</p>
          </div>
        </div>

        <!-- Personal Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="name" value="<?php echo htmlentities($profile['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Birth Date</label>
            <input type="date" name="birth_date" value="<?php echo htmlentities($profile['birth_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input type="tel" name="phone" value="<?php echo htmlentities($profile['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" value="<?php echo htmlentities($profile['email'], ENT_QUOTES, 'UTF-8'); ?>" 
                   disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
            <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Address</label>
          <textarea name="address" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"><?php echo htmlentities($profile['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Bio</label>
          <textarea name="bio" rows="4" placeholder="Tell us about yourself..."
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"><?php echo htmlentities($profile['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="flex space-x-4">
          <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Save Changes
          </button>
          <a href="/cpsproject/profile" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
            Cancel
          </a>
        </div>
      </form>
    </div>
  </div>

  <script>
    function previewImage(input) {
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const preview = document.getElementById('profile-preview');
          preview.innerHTML = `<img src="${e.target.result}" alt="Profile Photo" class="h-20 w-20 object-cover rounded-full">`;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
  </script>
</body>
</html>
