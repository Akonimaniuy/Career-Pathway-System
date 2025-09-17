<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Home', ENT_QUOTES, 'UTF-8'); ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">
  <?php include __DIR__ . '/../layout/navbar.php'; ?>

  <main class="mt-8 px-6 py-8">
    <div class="bg-white shadow-sm rounded-lg p-6">
      <div class="flex flex-col gap-8 items-start">
        <!-- Left: Hero / Intro -->
        <section class="w-full">
          <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">
            EXPLORE<br class="hidden lg:inline" />PATHWAYS
          </h1>
          

          <?php if (!empty($message)): ?>
            <div class="mt-6 inline-block rounded-lg bg-green-50 text-green-800 px-4 py-2 text-sm">
              <?php echo htmlentities($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
          <?php endif; ?>
        </section>

        <!-- Right: Carousel (below the intro) -->
        <section class="w-full relative">
          <!-- arrows (visible on lg+) -->
          <button aria-label="Previous" class="hidden lg:inline-flex absolute -left-10 top-1/2 -translate-y-1/2 z-10 bg-amber-400 text-white p-3 rounded-full shadow hover:bg-amber-500">
            &#10094;
          </button>

          <div id="carousel" class="flex gap-6 overflow-x-auto snap-x snap-mandatory px-2 py-6 touch-pan-x scrollbar-thin scrollbar-thumb-gray-300">
            <article class="snap-start flex-shrink-0 w-72 bg-white text-gray-900 rounded-xl shadow-lg overflow-hidden">
              <img src="public/images/agri.jpg" alt="Agriculture" class="w-full h-44 object-cover">
              <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Agriculture</h3>
                <p class="text-sm text-gray-600 min-h-[3.75rem]">Agriculture is the science and practice of cultivating crops and raising animals to produce food, fiber, and other resources.</p>
                <a href="#" class="inline-block mt-4 px-4 py-2 bg-green-700 text-white rounded-md text-sm hover:bg-green-800">Explore</a>
              </div>
            </article>

            <article class="snap-start flex-shrink-0 w-72 bg-white text-gray-900 rounded-xl shadow-lg overflow-hidden">
              <img src="public/images/cookery.jpg" alt="Cookery" class="w-full h-44 object-cover">
              <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Cookery</h3>
                <p class="text-sm text-gray-600 min-h-[3.75rem]">Cookery is the art and practice of preparing, cooking, and presenting food — a path to careers as chefs and bakers.</p>
                <a href="#" class="inline-block mt-4 px-4 py-2 bg-green-700 text-white rounded-md text-sm hover:bg-green-800">Explore</a>
              </div>
            </article>

            <article class="snap-start flex-shrink-0 w-72 bg-white text-gray-900 rounded-xl shadow-lg overflow-hidden">
              <img src="public/images/ict.jpg" alt="ICT" class="w-full h-44 object-cover">
              <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">ICT</h3>
                <p class="text-sm text-gray-600 min-h-[3.75rem]">ICT covers computer systems, networks and software — careers include developers and sysadmins.</p>
                <a href="#" class="inline-block mt-4 px-4 py-2 bg-green-700 text-white rounded-md text-sm hover:bg-green-800">Explore</a>
              </div>
            </article>

            <article class="snap-start flex-shrink-0 w-72 bg-white text-gray-900 rounded-xl shadow-lg overflow-hidden">
              <img src="public/images/electrical.jpg" alt="Electrical" class="w-full h-44 object-cover">
              <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Electrical</h3>
                <p class="text-sm text-gray-600 min-h-[3.75rem]">Study and apply electricity, electronics and power systems — opportunities for technicians and engineers.</p>
                <a href="#" class="inline-block mt-4 px-4 py-2 bg-green-700 text-white rounded-md text-sm hover:bg-green-800">Explore</a>
              </div>
            </article>

            <article class="snap-start flex-shrink-0 w-72 bg-white text-gray-900 rounded-xl shadow-lg overflow-hidden">
              <img src="public/images/smaw.jpg" alt="Welding" class="w-full h-44 object-cover">
              <div class="p-4">
                <h3 class="text-lg font-semibold mb-2">Welding</h3>
                <p class="text-sm text-gray-600 min-h-[3.75rem]">Welding joins materials using heat — careers include welders, fabricators and inspectors.</p>
                <a href="#" class="inline-block mt-4 px-4 py-2 bg-green-700 text-white rounded-md text-sm hover:bg-green-800">Explore</a>
              </div>
            </article>
          </div>

          <button aria-label="Next" class="hidden lg:inline-flex absolute -right-10 top-1/2 -translate-y-1/2 z-10 bg-amber-400 text-white p-3 rounded-full shadow hover:bg-amber-500">
            &#10095;
          </button>
        </section>
      </div>
    </div>
  </main>

  <script>
    const carousel = document.getElementById('carousel');
    const prevBtn = document.querySelector('button[aria-label="Previous"]');
    const nextBtn = document.querySelector('button[aria-label="Next"]');

    prevBtn?.addEventListener('click', () => carousel.scrollBy({ left: -320, behavior: 'smooth' }));
    nextBtn?.addEventListener('click', () => carousel.scrollBy({ left: 320, behavior: 'smooth' }));
  </script>
</body>
</html>