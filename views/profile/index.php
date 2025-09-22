<?php
// File: views/profile/index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'My Profile', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="mt-8">
      <!-- Success Messages -->
      <?php if (isset($_GET['success'])): ?>
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
          <?php 
          $success = $_GET['success'];
          if ($success === 'profile_updated') echo 'Profile updated successfully!';
          elseif ($success === 'password_changed') echo 'Password changed successfully!';
          else echo 'Action completed successfully!';
          ?>
        </div>
      <?php endif; ?>

      <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Cover Photo Area -->
        <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600"></div>
        
        <!-- Profile Info -->
        <div class="relative px-6 pb-6">
          <div class="flex items-end -mt-16 mb-4">
            <!-- Profile Photo -->
            <div class="relative">
              <?php if (!empty($profile['profile_photo'])): ?>
                <img src="/cpsproject/uploads/<?php echo htmlentities($profile['profile_photo'], ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="Profile Photo" 
                     class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
              <?php else: ?>
                <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gray-300 flex items-center justify-center">
                  <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
              <?php endif; ?>
            </div>
            
            <!-- Name and Role -->
            <div class="ml-6 flex-1">
              <h1 class="text-2xl font-bold text-gray-900">
                <?php echo htmlentities($profile['name'] ?: 'User', ENT_QUOTES, 'UTF-8'); ?>
              </h1>
              <p class="text-gray-600 capitalize">
                <?php echo htmlentities($profile['role'], ENT_QUOTES, 'UTF-8'); ?>
              </p>
              <p class="text-sm text-gray-500">
                Member since <?php echo date('F Y', strtotime($profile['created_at'])); ?>
              </p>
            </div>
            
            <!-- Edit Button -->
            <div class="flex space-x-3">
              <a href="/cpsproject/profile/edit" 
                 class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Edit Profile
              </a>
              <a href="/cpsproject/profile/change-password" 
                 class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                Change Password
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Details -->
      <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>
          
          <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <dt class="text-sm font-medium text-gray-500">Full Name</dt>
              <dd class="mt-1 text-sm text-gray-900"><?php echo htmlentities($profile['name'] ?: 'Not provided', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            
            <div>
              <dt class="text-sm font-medium text-gray-500">Email</dt>
              <dd class="mt-1 text-sm text-gray-900"><?php echo htmlentities($profile['email'], ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            
            <div>
              <dt class="text-sm font-medium text-gray-500">Birth Date</dt>
              <dd class="mt-1 text-sm text-gray-900">
                <?php echo $profile['birth_date'] ? date('F j, Y', strtotime($profile['birth_date'])) : 'Not provided'; ?>
              </dd>
            </div>
            
            <div>
              <dt class="text-sm font-medium text-gray-500">Phone</dt>
              <dd class="mt-1 text-sm text-gray-900"><?php echo htmlentities($profile['phone'] ?: 'Not provided', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            
            <div class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Address</dt>
              <dd class="mt-1 text-sm text-gray-900"><?php echo htmlentities($profile['address'] ?: 'Not provided', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            
            <div class="sm:col-span-2">
              <dt class="text-sm font-medium text-gray-500">Bio</dt>
              <dd class="mt-1 text-sm text-gray-900"><?php echo nl2br(htmlentities($profile['bio'] ?: 'No bio provided', ENT_QUOTES, 'UTF-8')); ?></dd>
            </div>
          </dl>
        </div>

        <!-- Account Status -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Account Status</h2>
          
          <div class="space-y-4">
            <div>
              <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                  <?php echo htmlentities(ucfirst($profile['status'] ?? 'active'), ENT_QUOTES, 'UTF-8'); ?>
                </span>
              </div>
              <p class="text-sm text-gray-600 mt-1">Account Status</p>
            </div>
            
            <div>
              <div class="flex items-center">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                  <?php echo htmlentities(ucfirst($profile['role']), ENT_QUOTES, 'UTF-8'); ?>
                </span>
              </div>
              <p class="text-sm text-gray-600 mt-1">User Role</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>