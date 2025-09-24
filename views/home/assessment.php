<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlentities($title ?? 'Assessment', ENT_QUOTES, 'UTF-8'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>

    <main class="mt-8 px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <section class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    Career Assessment
                </h1>
                <p class="text-lg text-gray-600 mb-6">
                    Ready to discover your ideal career path? Our assessment will help you understand your strengths and interests.
                </p>
                <a href="/cpsproject/assessment" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Start Assessment
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <p class="text-sm text-gray-500 mt-3">You will be asked to log in or register to save your progress.</p>
            </section>
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