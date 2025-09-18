<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Access Denied', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <div class="mt-16 text-center">
      <div class="bg-white shadow rounded-lg p-8">
        <div class="text-red-600 mb-4">
          <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-4"><?php echo htmlentities($title ?? 'Access Denied', ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="text-gray-600 mb-6"><?php echo htmlentities($message ?? 'You do not have permission to access this area.', ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="space-x-4">
          <a href="/cpsproject" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Go Home</a>
          <a href="/cpsproject/logout" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Logout</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>