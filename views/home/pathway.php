<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'Explore Pathways', ENT_QUOTES, 'UTF-8'); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <?php include __DIR__ . '/../layout/navbar.php'; ?>

  <main class="mt-8 px-6 py-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header Section -->
      <section class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          EXPLORE PATHWAYS
        </h1>
        <p class="text-lg text-gray-600 mb-6">
          <?php echo htmlentities($message ?? 'Discover career pathways that match your interests and skills. Browse by category to explore opportunities in your field of interest.', ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <!-- Category Filter -->
        <div class="mb-6">
          <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-2">
            Filter by Category:
          </label>
          <select id="category-filter" class="w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">All Categories</option>
            <?php foreach ($categories ?? [] as $category): ?>
              <option value="<?php echo $category['id']; ?>">
                <?php echo htmlentities($category['name'], ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </section>

      <!-- Loading State -->
      <div id="loading" class="hidden text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Loading pathways...</p>
      </div>

      <!-- Pathways Grid -->
      <div id="pathways-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($pathways ?? [] as $pathway): ?>
          <article class="pathway-card bg-white rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-all duration-300 hover:shadow-xl" data-category="<?php echo $pathway['category_id']; ?>">
            <div class="relative">
              <img src="<?php echo htmlentities($pathway['image_url'] ?? 'public/images/default-pathway.jpg', ENT_QUOTES, 'UTF-8'); ?>" 
                   alt="<?php echo htmlentities($pathway['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                   class="w-full h-48 object-cover">
              <div class="absolute top-3 left-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  <?php echo htmlentities($pathway['category_name'] ?? 'Category', ENT_QUOTES, 'UTF-8'); ?>
                </span>
              </div>
            </div>
            
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">
                <?php echo htmlentities($pathway['name'], ENT_QUOTES, 'UTF-8'); ?>
              </h3>
              
              <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                <?php echo htmlentities($pathway['description'] ?? 'Explore this exciting career pathway and discover the opportunities it offers.', ENT_QUOTES, 'UTF-8'); ?>
              </p>
              
              <div class="flex justify-between items-center">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                  Learn More
                </button>
                <button class="text-blue-600 text-sm hover:text-blue-800 font-medium">
                  Details →
                </button>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <!-- Empty State -->
      <div id="empty-state" class="hidden text-center py-16">
        <div class="max-w-md mx-auto">
          <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.513-.751-6.266-2.024C4.49 12.48 3 10.87 3 9c0-3.314 2.686-6 6-6s6 2.686 6 6v3a2 2 0 11-4 0V9a2 2 0 10-4 0v6.172z"/>
          </svg>
          <h3 class="mt-4 text-lg font-medium text-gray-900">No pathways found</h3>
          <p class="mt-2 text-gray-600">Try selecting a different category or check back later for more pathways.</p>
        </div>
      </div>

      <!-- Call to Action -->
      <div class="mt-16 text-center bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white">
        <h2 class="text-2xl font-bold mb-4">Ready to Find Your Perfect Path?</h2>
        <p class="text-blue-100 mb-6">Take our adaptive assessment to get personalized pathway recommendations based on your skills and interests.</p>
        <a href="/cpsproject/assessment" class="inline-flex items-center bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
          Take Assessment
          <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </a>
      </div>
    </div>
  </main>

  <div id="login-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
      <div class="bg-white rounded-lg shadow-xl p-6 w-11/12 max-w-md relative">
          <button id="close-login-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <div class="max-w-md mx-auto py-12 px-4">
              <div class="mt-8 bg-white shadow rounded-lg p-6">
                  <h1 class="text-xl font-semibold text-gray-800">Login</h1>

                  <?php if (!empty($error)): ?>
                      <div class="mt-4 text-sm text-red-700 bg-red-50 border border-red-100 p-3 rounded">
                          <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                  <?php endif; ?>

                  <form method="post" action="/cpsproject/login" class="mt-4 space-y-4">
                      <?php echo \core\CSRF::inputField(); ?>
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Email</label>
                          <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                      </div>
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Password</label>
                          <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                      </div>
                      <div class="flex items-center justify-between">
                          <label class="inline-flex items-center text-sm">
                              <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                              <span class="ml-2 text-gray-700">Remember me</span>
                          </label>
                          <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
                      </div>
                      <div>
                          <button type="submit" class="w-full inline-flex justify-center py-2 px-4 rounded-md bg-blue-600 text-white hover:bg-blue-700">Login</button>
                      </div>
                  </form>

                  <p class="mt-4 text-sm text-gray-600">Don't have an account? <a href="#" id="open-register-from-login" class="text-blue-600 hover:underline">Register</a></p>
              </div>
          </div>
      </div>
  </div>

  <div id="register-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
      <div class="bg-white rounded-lg shadow-xl p-6 w-11/12 max-w-md relative">
          <button id="close-register-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">&times;</button>
          <div class="max-w-md mx-auto py-12 px-4">
              <div class="mt-8 bg-white shadow rounded-lg p-6">
                  <h1 class="text-xl font-semibold text-gray-800">Register</h1>

                  <?php if (!empty($error)): ?>
                      <div class="mt-4 text-sm text-red-700 bg-red-50 border border-red-100 p-3 rounded">
                          <?php echo htmlentities($error, ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                  <?php endif; ?>

                  <form method="post" action="/cpsproject/register" class="mt-4 space-y-4">
                      <?php echo \core\CSRF::inputField(); ?>
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Name</label>
                          <input type="text" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                      </div>
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Email</label>
                          <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                      </div>
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Password</label>
                          <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                      </div>
                      <div>
                          <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                          <input type="password" name="password2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                      </div>
                      <div>
                          <button type="submit" class="w-full inline-flex justify-center py-2 px-4 rounded-md bg-green-600 text-white hover:bg-green-700">Register</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const categoryFilter = document.getElementById('category-filter');
      const pathwayCards = document.querySelectorAll('.pathway-card');
      const loading = document.getElementById('loading');
      const pathwaysGrid = document.getElementById('pathways-grid');
      const emptyState = document.getElementById('empty-state');

      // Filter pathways by category
      categoryFilter.addEventListener('change', function() {
        const selectedCategory = this.value;
        let visibleCards = 0;

        // Show loading state briefly for better UX
        loading.classList.remove('hidden');
        pathwaysGrid.classList.add('opacity-50');

        setTimeout(() => {
          pathwayCards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            
            if (selectedCategory === '' || cardCategory === selectedCategory) {
              card.classList.remove('hidden');
              card.style.display = 'block';
              visibleCards++;
            } else {
              card.classList.add('hidden');
              card.style.display = 'none';
            }
          });

          // Show/hide empty state
          if (visibleCards === 0) {
            emptyState.classList.remove('hidden');
          } else {
            emptyState.classList.add('hidden');
          }

          // Hide loading state
          loading.classList.add('hidden');
          pathwaysGrid.classList.remove('opacity-50');
        }, 300);
      });

      // Add smooth animations to cards
      const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
            observer.unobserve(entry.target);
          }
        });
      }, observerOptions);

      pathwayCards.forEach(card => {
        observer.observe(card);
      });

      // Add click handlers for pathway cards
      pathwayCards.forEach(card => {
        const learnMoreBtn = card.querySelector('button');
        const detailsBtn = card.querySelector('button + button');
        const pathwayName = card.querySelector('h3').textContent;

        if (learnMoreBtn) {
          learnMoreBtn.addEventListener('click', () => {
            // Could open a modal or navigate to pathway detail page
            console.log('Learn more about:', pathwayName);
          });
        }

        if (detailsBtn) {
          detailsBtn.addEventListener('click', () => {
            // Could navigate to detailed pathway information
            console.log('View details for:', pathwayName);
          });
        }
      });

      // Modal script
      const loginBtn = document.getElementById('login-modal-btn');
      const registerBtn = document.getElementById('register-modal-btn');
      const mobileLoginBtn = document.getElementById('mobile-login-btn');
      const mobileRegisterBtn = document.getElementById('mobile-register-btn');
      const loginModal = document.getElementById('login-modal');
      const registerModal = document.getElementById('register-modal');
      const closeLoginBtn = document.getElementById('close-login-modal');
      const closeRegisterBtn = document.getElementById('close-register-modal');
      const openRegisterFromLoginBtn = document.getElementById('open-register-from-login');

      function showModal(modal) { modal.classList.remove('hidden'); }
      function hideModal(modal) { modal.classList.add('hidden'); }

      if (loginBtn) loginBtn.addEventListener('click', () => showModal(loginModal));
      if (mobileLoginBtn) mobileLoginBtn.addEventListener('click', () => { hideModal(document.getElementById('mobile-menu')); showModal(loginModal); });
      if (registerBtn) registerBtn.addEventListener('click', () => showModal(registerModal));
      if (mobileRegisterBtn) mobileRegisterBtn.addEventListener('click', () => { hideModal(document.getElementById('mobile-menu')); showModal(registerModal); });
      if (closeLoginBtn) closeLoginBtn.addEventListener('click', () => hideModal(loginModal));
      if (closeRegisterBtn) closeRegisterBtn.addEventListener('click', () => hideModal(registerModal));
      if (loginModal) loginModal.addEventListener('click', (e) => { if (e.target === loginModal) hideModal(loginModal); });
      if (registerModal) registerModal.addEventListener('click', (e) => { if (e.target === registerModal) hideModal(registerModal); });
      if (openRegisterFromLoginBtn) openRegisterFromLoginBtn.addEventListener('click', (e) => { e.preventDefault(); hideModal(loginModal); showModal(registerModal); });
    });
  </script>

  <style>
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .line-clamp-3 {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;  
      overflow: hidden;
    }

    .animate-fade-in {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</body>
</html>