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
    <h1>Your Future, Unlocked.</h1>
    <div class="carousel" mask>
        <article>
            <img src="public/images/agri.jpg" alt="">
            <h2><?php echo $data['title'];?></h2>
            <div>
                <p><?php echo $data['message'];?></p>
                <a href="#">Explore</a>
            </div>
        </article>
        <article>
            <img src="public/images/cookery.jpg" alt="">
            <h2>Cookery</h2>
            <div>
                <p>Cookery is the art and practice of preparing, cooking, and presenting food. It involves mastering culinary techniques, ensuring food safety, and creating meals that are both nutritious and appealing. A career in cookery can lead to opportunities as a chef, baker, or food entrepreneur in restaurants, hotels, catering services, and other parts of the hospitality industry.</p>

                <a href="#">Explore</a>
            </div>
        </article>
        <article>
            <img src="public/images/ict.jpg" alt="">
            <h2>ICT</h2>
            <div>
                <p>ICT focuses on the use of technology to manage, process, and share information. It covers computer systems, networks, software, and digital communication tools that are essential in today’s industries. A career in ICT can lead to roles such as software developer, network administrator, IT support specialist, or web designer.</p>

                <a href="#">Explore</a>
            </div>
        </article>
        <article>
            <img src="public/images/electrical.jpg" alt="">
            <h2>Electrical</h2>
            <div>
                <p>Electrical focuses on the study and application of electricity, electronics, and power systems. It involves installing, maintaining, and repairing electrical wiring, equipment, and machinery. Careers in this field include electricians, electrical technicians, and engineers who work in construction, manufacturing, and energy industries.</p>

                <a href="#">Explore</a>
            </div>
        </article>
        <article>
            <img src="public/images/smaw.jpg" alt="">
            <h2>SMAW</h2>
            <div>
                <p>SMAW (Welding) involves joining metals using an electric arc and coated electrodes. It is widely used in construction, manufacturing, and repair industries. Careers in this field include welders, fabricators, and metalworkers who build and maintain metal structures, pipelines, and machinery.</p>

                <a href="#">Explore</a>
            </div>
        </article>
        <article>
            <img src="public/images/smaw.jpg" alt="">
            <h2>GUKO</h2>
            <div>
                <p>SMAW (Welding) involves joining metals using an electric arc and coated electrodes. It is widely used in construction, manufacturing, and repair industries. Careers in this field include welders, fabricators, and metalworkers who build and maintain metal structures, pipelines, and machinery.</p>

                <a href="#">Explore</a>
            </div>
        </article>
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