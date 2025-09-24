<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($title ?? 'About Us', ENT_QUOTES, 'UTF-8'); ?></title>

  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Fade-in effect for sections */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    .fade-in.is-visible {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <main class="mt-8">
      <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 fade-in">About Our System</h1>
        <p class="mt-2 text-gray-600 fade-in">The Web-based Career Pathway System with Adaptive Assessment is designed to help students make accurate and personalized decisions about their academic and career paths.</p>

        <section class="mt-6 fade-in">
          <h2 class="text-xl font-semibold text-gray-800 transition-colors hover:text-gray-900">Project Overview</h2>
          <ul class="mt-3 space-y-2 text-gray-700">
            <li class="transition-colors hover:text-gray-900"><span class="font-medium">Project Title:</span> Web-based Career Pathway System with Adaptive Assessment</li>
            <li class="transition-colors hover:text-gray-900"><span class="font-medium">Proponents:</span> Christian Nino Abasolo, Matt Lyndon Duron, Marcham James Balvez, Neil Alfredo Ragas, John Marlowe Sepulveda</li>
            <li class="transition-colors hover:text-gray-900"><span class="font-medium">Project Adviser:</span> JOHNREL M. PAGLINAWAN, MIT</li>
          </ul>
        </section>

        <section class="mt-6 fade-in">
          <h2 class="text-xl font-semibold text-gray-800 transition-colors hover:text-gray-900">Statement of the Problem</h2>
          <p class="mt-3 text-gray-700">Many students are confused or overwhelmed when choosing their future career path or academic track. A recent Training Needs Analysis revealed that a large number of students are still undecided. Students often rely on limited information, peer influence, or family pressure, which can lead to stress and a lack of motivation.</p>
          <p class="mt-3 text-gray-700">Existing systems often use fixed, static questionnaires and lack personalized support for younger students beginning to explore their interests.</p>
        </section>

        <section class="mt-6 fade-in">
          <h2 class="text-xl font-semibold text-gray-800 transition-colors hover:text-gray-900">Project Objectives</h2>
          <h3 class="text-lg font-medium text-gray-800 mt-4 transition-colors hover:text-gray-900">Main Objective:</h3>
          <p class="mt-2 text-gray-700">To design and develop a web-based career pathway system with adaptive assessment to support accurate and personalized academic and career path selection for students.</p>
          
          <h3 class="text-lg font-medium text-gray-800 mt-4 transition-colors hover:text-gray-900">Specific Objectives:</h3>
          <ul class="mt-2 list-disc list-inside text-gray-700 space-y-2">
            <li class="transition-colors hover:text-gray-900">Implement an adaptive assessment to evaluate students' interests, skills, and strengths.</li>
            <li class="transition-colors hover:text-gray-900">Develop a recommendation that suggests suitable academic or career paths based on assessment results.</li>
            <li class="transition-colors hover:text-gray-900">Design a user-friendly interface that encourages exploration and informed decision-making.</li>
          </ul>
        </section>
      </div>
    </main>
  </div>

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
      // FADE-IN ON SCROLL LOGIC
      const fadeInElements = document.querySelectorAll('.fade-in');
      const observerOptions = { root: null, rootMargin: '0px', threshold: 0.2 };
      const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, observerOptions);
      fadeInElements.forEach(el => observer.observe(el));

      // MODAL LOGIC
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
</body>
</html>