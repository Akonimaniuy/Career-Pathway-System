<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Home', ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- Tailwind CDN (quick start) -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <main class="mt-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800"><?php echo htmlentities($title ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
        
        <p class="mt-2 text-gray-600"><?php echo htmlentities($message ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

        <section class="mt-6">
            <h1>Please Select 2 or more path.</h1>
          
        </section>
      </div>
    </main>
  </div>
</body>
</html>