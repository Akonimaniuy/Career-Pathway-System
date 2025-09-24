<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Pathway</title>
    <link rel="stylesheet" type="text/css" href="public/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
  <?php include __DIR__ . '/../layout/navbar.php'; ?>
<div class="hero">
    <h1><?php echo htmlspecialchars($title ?? 'Your Future, Unlocked.'); ?></h1>
    <div class="carousel" mask>
        <?php if (!empty($featuredPaths)): ?>
            <?php foreach ($featuredPaths as $path): ?>
                <article class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="relative">
                        <img src="<?php echo htmlspecialchars($path['image_url'] ?: 'public/images/default-pathway.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($path['name']); ?>" 
                             class="w-full h-48 object-cover">
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?php echo htmlspecialchars($path['category_name'] ?? 'Category'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 h-14 overflow-hidden"><?php echo htmlspecialchars($path['name']); ?></h3>
                        <p class="text-gray-600 text-sm mb-4 h-20 overflow-hidden"><?php echo htmlspecialchars($path['description'] ?? 'Explore this exciting career pathway.'); ?></p>
                        <a href="/cpsproject/career-path/<?php echo $path['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">Learn More</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    
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
        // Desktop buttons from the navbar
        const loginBtn = document.getElementById('login-modal-btn');
        const registerBtn = document.getElementById('register-modal-btn');

        // Mobile buttons from the navbar
        const mobileLoginBtn = document.getElementById('mobile-login-btn');
        const mobileRegisterBtn = document.getElementById('mobile-register-btn');

        const loginModal = document.getElementById('login-modal');
        const registerModal = document.getElementById('register-modal');

        const closeLoginBtn = document.getElementById('close-login-modal');
        const closeRegisterBtn = document.getElementById('close-register-modal');
        const openRegisterFromLoginBtn = document.getElementById('open-register-from-login');

        // Function to show a modal
        function showModal(modal) {
            modal.classList.remove('hidden');
        }

        // Function to hide a modal
        function hideModal(modal) {
            modal.classList.add('hidden');
        }

        // Show the login modal when the desktop or mobile login buttons are clicked
        if (loginBtn) {
          loginBtn.addEventListener('click', () => showModal(loginModal));
        }
        if (mobileLoginBtn) {
          mobileLoginBtn.addEventListener('click', () => {
            hideModal(document.getElementById('mobile-menu'));
            showModal(loginModal);
          });
        }
        
        // Show the register modal when the desktop or mobile register buttons are clicked
        if (registerBtn) {
          registerBtn.addEventListener('click', () => showModal(registerModal));
        }
        if (mobileRegisterBtn) {
          mobileRegisterBtn.addEventListener('click', () => {
            hideModal(document.getElementById('mobile-menu'));
            showModal(registerModal);
          });
        }
        
        // Close modals with the close button
        closeLoginBtn.addEventListener('click', () => hideModal(loginModal));
        closeRegisterBtn.addEventListener('click', () => hideModal(registerModal));

        // Hide modals when clicking outside the modal content
        loginModal.addEventListener('click', function(e) {
            if (e.target === loginModal) {
                hideModal(loginModal);
            }
        });

        registerModal.addEventListener('click', function(e) {
            if (e.target === registerModal) {
                hideModal(registerModal);
            }
        });
        
        // Switch from login to register modal
        if (openRegisterFromLoginBtn) {
          openRegisterFromLoginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            hideModal(loginModal);
            showModal(registerModal);
          });
        }
    });
</script>
</body>
</html>