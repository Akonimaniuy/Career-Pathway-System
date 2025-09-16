# Career Path System — Student Developer Guide

This repo is a small PHP MVC starter used in class. It includes user registration/login with "remember me", session management, a tiny router, and a simple model layer.

Quick links (files you'll use most)
- Entry: `index.php` (c:\wamp64\www\cpsproject\index.php)
- Config:
  - `config/database.php` (DB connection constants)
  - `config/auth.php` (auth & session constants)
- Core framework:
  - `core/Router.php` [`core\Router`](core/Router.php)
  - `core/Controller.php` [`core\Controller`](core/Controller.php)
  - `core/Model.php` [`core\Model`](core/Model.php)
  - `core/Database.php` [`core\Database`](core/Database.php)
  - `core/Auth.php` [`core\Auth`](core/Auth.php)
  - `core/Session.php` [`core\Session`](core/Session.php)
  - `core/CSRF.php` (CSRF helper)
- Controllers: `controllers/` (e.g. `AuthController.php`, `HomeController.php`, `UserController.php`)
- Models: `models/UserModel.php`
- Views: `views/` (organized by controller folder, e.g. `views/auth/login.php`)
- DB migration: `migrations/001_create_auth_schema.sql`

1) Quick goal/contract
- Inputs: HTTP requests routed by `index.php` → `core\Router`.
- Outputs: HTML views rendered from `views/<controller>/<view>.php`.
- Success: Server serves pages, user can register/login, protected pages redirect to login when not authenticated.
- Error modes: DB connection failures, missing views/controllers, CSRF validation failures, authentication failures.

2) Local setup (WAMP)
- Put the project in WAMP's web root (you already have `c:\wamp64\www\cpsproject`).
- Start Apache & MySQL from WAMP.

Create the database and run migrations:
- Using phpMyAdmin: import `migrations/001_create_auth_schema.sql`.
- Or from PowerShell (if `mysql` is in PATH):
```powershell
# create db (if it doesn't exist) and import schema
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS cps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p cps < .\migrations\001_create_auth_schema.sql
```

Adjust DB credentials if needed:
- Edit `config/database.php` (DB_HOST, DB_NAME, DB_USER, DB_PASS).

Access app in browser:
- Recommended (WAMP): http://localhost/cpsproject/
  - `index.php` currently trims a base path of `/cpsproject` so serving from `http://localhost/cpsproject/` matches default settings.
- Or update `index.php` basePath if you use a different URL.

3) How routing works (overview)
- Routes are declared in `index.php` via `$router->add('path', ['controller'=>'X', 'action'=>'y']);`.
  - Example routes already present: `''`, `users`, `user/{id:\d+}`, `login`, `register`, `logout`.
- `core\Router` converts the route to a regex and dispatches to `controllers\<ControllerName>` and calls the action method (camelCase).
- Controller class name convention: `controllers\HomeController` → view folder `views/home`.

4) How to add a new page / feature (step-by-step)
Example: add `/profile` which shows current user's profile.

A) Add route in `index.php`:
```php
$router->add('profile', ['controller' => 'UserController', 'action' => 'profile']);
```

B) Add controller method in `controllers/UserController.php`:
```php
public function profile()
{
    // Auth is already created in constructor and requireAuth() is called,
    // so $this->auth->user() returns current user.
    $user = $this->auth->user();
    $this->render('profile', ['title' => 'My Profile', 'user' => $user]);
}
```

C) Create view `views/user/profile.php`:
```php
<?php include __DIR__ . '/../layout/navbar.php'; ?>
<h1><?php echo htmlentities($title); ?></h1>
<p>Name: <?php echo htmlentities($user['name']); ?></p>
<p>Email: <?php echo htmlentities($user['email']); ?></p>
```

D) Link to it from navbar (optional): `views/layout/navbar.php` has menu links.

5) Authentication and security notes
- Password hashing: configured in `config/auth.php`. Uses Argon2id if available (`AUTH_PWD_ALGO`), otherwise `PASSWORD_DEFAULT`.
- Remember-me tokens: stored hashed in `auth_tokens` table; raw token is only in cookie.
- CSRF: forms render `\core\CSRF::inputField()` and controllers verify token.
- Sessions: `core\Session` uses a custom session name and secure cookie options (sameSite, httponly, secure depends on HTTPS).
- SQL: database access uses PDO prepared statements; avoid concatenating user input into raw SQL.
- Production hardening reminders:
  - Disable `display_errors` and error_reporting in production.
  - Use HTTPS to get secure cookies and set `AUTH_COOKIE_SECURE` to true.
  - Set a strong DB password and limit DB privileges.
  - Rotate long-lived tokens and periodically purge expired `auth_tokens`.

6) Common developer tasks & tips
- Register / Login: use `/register` and `/login`.
- Protected controllers: `HomeController` and `UserController` call `$this->auth->requireAuth()` in constructor. Use the same in other controllers to protect endpoints.
- Use `models/UserModel::createUser()` for creating users (it hashes passwords).
- To inspect routes at runtime, `core/Router::dispatch()` prints debug lines: you can remove `echo "Request URI: $url"` for production.
- Use `core\Database::getInstance()->getConnection()` for ad-hoc queries from models.

7) Testing & quality gates (quick checklist)
- Build/run: N/A (PHP runtime). Verify via browser requests.
- DB: import migration and ensure `users`, `auth_tokens`, `login_attempts` exist.
- Lint/Static: run any PHP linter you prefer (php -l).
- Smoke test:
  - Visit `/cpsproject/register` and create a user.
  - Log in at `/cpsproject/login`.
  - Visit `/cpsproject` (home) and `/cpsproject/users`.
- Edge cases:
  - Missing view => Controller::render prints "View not found".
  - DB down => `core\Database` dies with message (consider improving error handling).
  - CSRF mismatch => controllers show error message.

8) Small improvements students can implement
- Add composer + PSR-4 autoloading instead of manual spl_autoload_register.
- Add unit/integration tests (PHPUnit) — test Auth attempt, remember-me logic.
- Add an environment file loader (e.g., `.env`) instead of editing `config/*.php`.
- Implement better error handling and logging.
- Add pagination for `users` list, and role-based access control for admin features.

9) Where to look in code (quick reference)
- App entry & routes: `index.php` (c:\wamp64\www\cpsproject\index.php)
- Router: `core/Router.php` (`core\Router`)
- Base controller rendering: `core/Controller.php` (`core\Controller`)
- Auth implementation: `core/Auth.php` (`core\Auth`)
- DB wrapper: `core/Database.php` (`core\Database`)
- User model: `models/UserModel.php` (`models\UserModel`)
- Auth controller (login/register): `controllers/AuthController.php` (`controllers\AuthController`)
- Migration SQL: `migrations/001_create_auth_schema.sql`

10) Troubleshooting
- Blank pages / PHP errors: enable `display_errors` temporarily in `index.php` at top (already set in this repo for dev).
- Route not found: confirm route added in `index.php` and that `basePath` matches the URL; `index.php` strips `/cpsproject` prefix.
- Cookie not set (remember-me): ensure using HTTPS or change `AUTH_COOKIE_SECURE` appropriately for local dev.

11) Example commands (PowerShell)
```powershell
# Import DB schema (if mysql in PATH)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS cps CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p cps < .\migrations\001_create_auth_schema.sql

# Or open phpMyAdmin at http://localhost/phpmyadmin and import migrations/001_create_auth_schema.sql
```

12) Next steps I can take for you (pick one)
- Create `README.md` in the repo with this content.
- Add a sample `views/user/profile.php` and route, then run a smoke test (you must enable editing and terminal tools).
- Add composer and PSR-4 autoloading scaffolding.

--- 

Requirements coverage
- Read system and produce student-facing documentation: Done (README content above).
- Explain how to run, how files work, how to extend, security & testing notes: Done.
- If you'd like, I can create the README in your project now or make the small improvements listed — tell me which and I will update the files.

If you'd like me to actually create the README file in the repository, pick "Create README" and enable file editing; I'll add it and run a short smoke test / verification.

## Debugging & Testing Tools

This project includes a lightweight debugging subsystem (debug toolbar, logging) and a tiny test harness to run quick smoke checks locally. The tools are intentionally minimal, safe for development, and disabled in production. Add the files described in the "Debugging Tools" developer change-set if they are not yet present.

### Files & locations
- Debug config: `config/debug.php`
- Debug helper: `core/Debug.php`
- Debug storage (log): `storage/debug.log` (folder: `storage/`)
- Hooks added to: `index.php` (initialization), `core/Database.php` (query capture), `core/Router.php` (`getRoutes()` accessor)
- Test harness: `tests/run_tests.php` and `tests/TestRunner.php`

### Quick overview
- Debug toolbar injects a small pane into HTML pages when `APP_DEBUG` is true.
- Debug captures:
  - In-memory logs (`\core\Debug::log(...)`)
  - DB queries run through `core\Database::query(...)`
  - Session contents
  - Request summary and defined routes
- A small CLI test runner (`tests/run_tests.php`) provides smoke tests: DB connectivity, router matching, and basic model checks.

### Enable / disable
Open or create `config/debug.php` and ensure:
```php
defined('APP_DEBUG') || define('APP_DEBUG', true); // true for dev, false for production
defined('DEBUG_SHOW_TOOLBAR') || define('DEBUG_SHOW_TOOLBAR', true);
defined('DEBUG_LOG_PATH') || define('DEBUG_LOG_PATH', APP_PATH . '/storage/debug.log');
```
To disable all debug features in production set `APP_DEBUG` to `false` and/or `DEBUG_SHOW_TOOLBAR` to `false`.

### Prepare storage (Windows PowerShell)
Create the storage folder for debug logs (run from project root):
```powershell
New-Item -ItemType Directory -Path 'C:\wamp64\www\cpsproject\storage' -Force
```
Ensure Apache/PHP has write access to that folder (WAMP typically already works under Windows).

### How the toolbar appears
- When `APP_DEBUG` and `DEBUG_SHOW_TOOLBAR` are true and an HTML page is returned, the toolbar appears bottom-left.
- The toolbar shows tabs: Request, Session, Logs, Queries, Routes.
- Click a tab to open its content; "Close" hides the toolbar.

### Debug helper usage (in controllers or views)
- Add a log entry:
```php
\core\Debug::log('Fetching user list', ['count' => count($users)]);
```
- Dump a variable inline (only in dev):
```php
\core\Debug::dump($someVar);
```
- Dump and exit:
```php
\core\Debug::dd($someVar);
```

The toolbar displays recent logs and DB queries automatically (the DB class records queries on each `query()` call).

### Logging behavior
- Short in-memory buffer (trimmed to `DEBUG_MAX_IN_MEMORY`) is used for the toolbar.
- At script shutdown the debug helper appends a human-readable summary to `storage/debug.log`.
- You can inspect `storage/debug.log` to see persistent entries.

### Running tests (CLI smoke tests)
A tiny test runner lives in `tests/`. It performs fast, non-destructive checks.

From project root (PowerShell):
```powershell
cd C:\wamp64\www\cpsproject
php .\tests\run_tests.php
```
Expected output:
- PASS/FAIL lines for each check (DB connection, router matching, UserModel API)
- Exit code 0 on success, non-zero when failures occurred

Tests are intentionally light — they check API surface and connectivity, not deep behavior. Use them as a quick health check after making changes.

### Example: Add a debug log in HomeController
In `controllers/HomeController.php` inside `index()`:
```php
\core\Debug::log('Home index loaded', ['user_id' => $this->auth->id() ?? 'guest']);
```
Reload the home page in the browser. The toolbar will show the new log entry and any queries executed during the request.

### Safety & production notes
- Never enable `APP_DEBUG` on a public/production server.
- The toolbar is shown only on HTML responses; it won't auto-inject into JSON or binary responses.
- Error and exception handlers provided by the Debug helper are registered only when `APP_DEBUG` is true.
- Rotate or prune `storage/debug.log` periodically to avoid excessive disk usage.

### Troubleshooting
- Toolbar not visible:
  - Ensure `APP_DEBUG` and `DEBUG_SHOW_TOOLBAR` are true.
  - Confirm the page is HTML and `core/Debug::startOutputBuffer()` is called (should be initialized in `index.php`).
  - Check browser console for JS errors (toolbar uses a minimal script).
- No logs or queries:
  - Make sure your DB access goes through `core/Database::query()` (the Debug hook is there).
  - Use `\core\Debug::log()` manually to confirm toolbar captures logs.
- Tests fail with DB errors:
  - Confirm DB credentials in `config/database.php` and that the `cps` database and migrations are applied.


## How to add a CRUD resource (step-by-step)

This guide shows how to add a simple CRUD resource named "Post" (you can replace "Post" with any resource). Follow these steps and copy the example files into the matching locations.

### 1) Create the database table (migration)
Create a new migration file `migrations/002_create_posts.sql` and run it against your `cps` database.

````sql
-- filepath: c:\wamp64\www\cpsproject\migrations\002_create_posts.sql
CREATE TABLE IF NOT EXISTS `posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `body` TEXT NOT NULL,
  `user_id` INT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

Run it (PowerShell example):
mysql -u root -p cps < .\migrations\002_create_posts.sql

### 2) Add the Model
Create models/PostModel.php — it exposes basic methods (getAll, getById, create, update, delete).

// filepath: c:\wamp64\www\cpsproject\models\PostModel.php
<?php
namespace models;

use core\Model;
use core\Database;
use PDO;

class PostModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT id, title, body, user_id, created_at FROM posts ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT id, title, body, user_id, created_at FROM posts WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO posts (title, body, user_id, created_at) VALUES (:title, :body, :user_id, NOW())");
        $stmt->execute([
            'title' => $data['title'],
            'body' => $data['body'],
            'user_id' => $data['user_id'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update($id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE posts SET title = :title, body = :body WHERE id = :id");
        return $stmt->execute([
            'title' => $data['title'],
            'body' => $data['body'],
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

3) Add the Controller
Create controllers/PostController.php. Use Auth to guard actions that need login and CSRF for form protection.

<?php
// filepath: c:\wamp64\www\cpsproject\controllers\PostController.php
<?php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use core\CSRF;
use models\PostModel;

class PostController extends Controller
{
    protected $postModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        Session::start();
        $this->postModel = new PostModel();
        $this->auth = new Auth();
        // protect create/store/edit/update/delete - public index/show are allowed
    }

    // GET /posts
    public function index()
    {
        $posts = $this->postModel->getAll();
        $this->render('index', ['posts' => $posts, 'title' => 'Posts']);
    }

    // GET /post/{id}
    public function show($id)
    {
        $post = $this->postModel->getById($id);
        if (!$post) {
            echo "Post not found";
            return;
        }
        $this->render('show', ['post' => $post, 'title' => $post['title']]);
    }

    // GET /post/create
    public function create()
    {
        $this->auth->requireAuth();
        $this->render('create', ['title' => 'Create Post']);
    }

    // POST /post/store
    public function store()
    {
        $this->auth->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/posts');
            exit;
        }

        if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
            $this->render('create', ['error' => 'Invalid CSRF token']);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');

        // basic validation
        if ($title === '' || $body === '') {
            $this->render('create', ['error' => 'Title and body are required', 'title' => 'Create Post']);
            return;
        }

        $userId = $this->auth->id(); // may be null if not logged
        $id = $this->postModel->create(['title' => $title, 'body' => $body, 'user_id' => $userId]);

        header('Location: /cpsproject/post/' . $id);
        exit;
    }

    // GET /post/{id}/edit
    public function edit($id)
    {
        $this->auth->requireAuth();
        $post = $this->postModel->getById($id);
        if (!$post) {
            echo "Post not found";
            return;
        }
        $this->render('edit', ['post' => $post, 'title' => 'Edit Post']);
    }

    // POST /post/{id}/update
    public function update($id)
    {
        $this->auth->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/posts');
            exit;
        }

        if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
            $this->render('edit', ['error' => 'Invalid CSRF token', 'title' => 'Edit Post']);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if ($title === '' || $body === '') {
            $this->render('edit', ['error' => 'Title and body are required', 'post' => ['id' => $id, 'title' => $title, 'body' => $body]]);
            return;
        }

        $this->postModel->update($id, ['title' => $title, 'body' => $body]);
        header('Location: /cpsproject/post/' . $id);
        exit;
    }

    // POST /post/{id}/delete
    public function delete($id)
    {
        $this->auth->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/posts');
            exit;
        }

        if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
            echo "Invalid CSRF token";
            return;
        }

        $this->postModel->delete($id);
        header('Location: /cpsproject/posts');
        exit;
    }
}

4) Register the routes
Open index.php and add route entries (keep existing base path behavior). Add these lines with other $router->add calls.

<?php
// filepath: [index.php](http://_vscodecontentref_/3)
$router->add('posts', ['controller' => 'PostController', 'action' => 'index']);
$router->add('post/create', ['controller' => 'PostController', 'action' => 'create']);
$router->add('post/store', ['controller' => 'PostController', 'action' => 'store']);
$router->add('post/{id:\d+}', ['controller' => 'PostController', 'action' => 'show']);
$router->add('post/{id:\d+}/edit', ['controller' => 'PostController', 'action' => 'edit']);
$router->add('post/{id:\d+}/update', ['controller' => 'PostController', 'action' => 'update']);
$router->add('post/{id:\d+}/delete', ['controller' => 'PostController', 'action' => 'delete']);

5) Create Views
Create views/post/index.php, views/post/show.php, views/post/create.php, views/post/edit.php.

<?php
// filepath: c:\wamp64\www\cpsproject\views\post\index.php
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?php echo htmlentities($title ?? 'Posts'); ?></title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>
    <div class="mt-6 bg-white p-6 rounded shadow">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold"><?php echo htmlentities($title ?? 'Posts'); ?></h1>
        <a href="/cpsproject/post/create" class="px-3 py-1 bg-green-600 text-white rounded">Create Post</a>
      </div>

      <ul class="mt-4 space-y-4">
      <?php foreach ($posts as $p): ?>
        <li class="border p-3 rounded">
          <a href="/cpsproject/post/<?php echo urlencode($p['id']); ?>" class="text-lg font-semibold"><?php echo htmlentities($p['title']); ?></a>
          <div class="text-sm text-gray-600 mt-1"><?php echo htmlentities(substr($p['body'], 0, 200)); ?>...</div>
        </li>
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
</body>
</html>

Example create view (form uses CSRF token):
<?php
// filepath: c:\wamp64\www\cpsproject\views\post\create.php
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Create Post</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-50 min-h-screen">
  <div class="max-w-2xl mx-auto py-8 px-4">
    <?php include __DIR__ . '/../layout/navbar.php'; ?>
    <div class="mt-6 bg-white p-6 rounded shadow">
      <h1 class="text-2xl font-bold">Create Post</h1>

      <?php if (!empty($error)): ?>
        <div class="text-red-700 mt-3"><?php echo htmlentities($error); ?></div>
      <?php endif; ?>

      <form method="post" action="/cpsproject/post/store" class="mt-4 space-y-4">
        <?php echo \core\CSRF::inputField(); ?>
        <div>
          <label class="block text-sm">Title</label>
          <input name="title" required class="mt-1 w-full border rounded px-2 py-1" />
        </div>
        <div>
          <label class="block text-sm">Body</label>
          <textarea name="body" rows="8" required class="mt-1 w-full border rounded px-2 py-1"></textarea>
        </div>
        <div>
          <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

Create edit.php similar to create.php but pre-fill fields and post to /cpsproject/post/{id}/update. show.php displays the post and includes a delete form (POST) with CSRF for authorized users.

6) Add a link to the navbar
Update views/layout/navbar.php to include a "Posts" link (near Home/Users). Example snippet to add:

<?php
// add inside the nav links block
<a href="/cpsproject/posts" class="text-gray-600 hover:text-gray-900">Posts</a>

7) Run & verify
Apply migration:
mysql -u root -p cps < .\migrations\002_create_posts.sql

Place new files in their folders.
Visit:
List: http://localhost/cpsproject/posts
Create form: http://localhost/cpsproject/post/create
After creating, you should be redirected to the show page: /cpsproject/post/{id}
Quick manual verification checklist:

Form displays and accepts input.
CSRF token is included in form and validated.
After create, record exists in DB.
Edit and delete perform expected actions.
Protected actions (create, edit, update, delete) require login (via Auth::requireAuth()); configure as needed.

8) Add smoke tests (optional)
You can add a small test file to tests/ to verify DB and model methods quickly. Example test (append to tests/run_tests.php or create a new test file):

<?php
// filepath: c:\wamp64\www\cpsproject\tests\test_posts.php
<?php
require __DIR__ . '/../models/PostModel.php';
$pm = new models\PostModel();
// create a temporary post
$id = $pm->create(['title' => 'test', 'body' => 'body', 'user_id' => null]);
echo "Created post id: $id\n";
$post = $pm->getById($id);
var_dump($post !== false);
$pm->delete($id);


I'll add a clear, copy-pasteable "How to create a CRUD" section you can append to README.md. At the top I list the steps; below are code examples (model, controller, routes, views, migration) and run/verify commands.

Steps (what I'll show)

Create DB migration for the resource (SQL).
Add a Model: models/PostModel.php.
Add a Controller: controllers/PostController.php.
Register routes in index.php.
Add Views under views/post/ (index/create/edit/show).
Add a link to the navbar.
Run migration, verify in browser.
Optional: add smoke tests and debug logs.
Append this to README.md (paste below):

Run it (PowerShell example):

2) Add the Model
Create models/PostModel.php — it exposes basic methods (getAll, getById, create, update, delete).

3) Add the Controller
Create controllers/PostController.php. Use Auth to guard actions that need login and CSRF for form protection.

4) Register the routes
Open index.php and add route entries (keep existing base path behavior). Add these lines with other $router->add calls.

Notes:

We use explicit store/update/delete endpoints mapped to POST actions (you can instead use RESTful patterns).
For links and forms, use these paths prefixed with /cpsproject.
5) Create Views
Create views/post/index.php, views/post/show.php, views/post/create.php, views/post/edit.php.

Example index view:

Example create view (form uses CSRF token):

Create edit.php similar to create.php but pre-fill fields and post to /cpsproject/post/{id}/update. show.php displays the post and includes a delete form (POST) with CSRF for authorized users.

6) Add a link to the navbar
Update views/layout/navbar.php to include a "Posts" link (near Home/Users). Example snippet to add:

7) Run & verify
Apply migration:
Place new files in their folders.
Visit:
List: http://localhost/cpsproject/posts
Create form: http://localhost/cpsproject/post/create
After creating, you should be redirected to the show page: /cpsproject/post/{id}
Quick manual verification checklist:

Form displays and accepts input.
CSRF token is included in form and validated.
After create, record exists in DB.
Edit and delete perform expected actions.
Protected actions (create, edit, update, delete) require login (via Auth::requireAuth()); configure as needed.
8) Add smoke tests (optional)
You can add a small test file to tests/ to verify DB and model methods quickly. Example test (append to tests/run_tests.php or create a new test file):

Run:
php .\tests\test_posts.php