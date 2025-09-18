<?php
// File: core/Auth.php (Updated to load user role)
namespace core;

use core\Database;
use PDO;

class Auth
{
    protected $db;
    protected $user = null;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            Session::start();
        }
        $this->checkRemembered();
        $this->loadUserFromSession();
    }

    /* ---------- Public API ---------- */

    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        if ($this->isLocked($email)) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT * FROM " . AUTH_USER_TABLE . " WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->recordFailedAttempt($email);
            return false;
        }

        // verify password
        if (!password_verify($password, $user['password_hash'])) {
            $this->recordFailedAttempt($email);
            return false;
        }

        // if needs rehash (algorithm/params changed), rehash
        if (password_needs_rehash($user['password_hash'], AUTH_PWD_ALGO, AUTH_PWD_OPTIONS)) {
            $newHash = password_hash($password, AUTH_PWD_ALGO, AUTH_PWD_OPTIONS);
            $update = $this->db->prepare("UPDATE " . AUTH_USER_TABLE . " SET password_hash = :ph WHERE id = :id");
            $update->execute(['ph' => $newHash, 'id' => $user['id']]);
        }

        // login success: store minimal user info in session
        $this->loginSession($user);
        $this->clearFailedAttempts($email);

        if ($remember) {
            $this->rememberUser($user['id']);
        }

        return true;
    }

    public function logout()
    {
        // clear remember cookie + DB token
        if (!empty($_SESSION['auth_user_id'])) {
            $this->forgetRememberTokens((int)$_SESSION['auth_user_id']);
        }
        // destroy session safely
        Session::destroy();
    }

    public function check(): bool
    {
        return !empty($this->user);
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user['id'] ?? null;
    }

    public function requireAuth()
    {
        if (!$this->check()) {
            // basic redirect - adjust path to your login route
            header('Location: /cpsproject/login');
            exit();
        }
    }

    /* ---------- Remember-me implementation ---------- */

    protected function rememberUser(int $userId)
    {
        $token = random_bytes(AUTH_REMEMBER_TOKEN_BYTES);
        $tokenHex = bin2hex($token);
        $tokenHash = hash('sha256', $tokenHex);

        $expiresAt = time() + AUTH_REMEMBER_EXPIRE;

        // Store hashed token in DB with expiry and user agent
        $stmt = $this->db->prepare("INSERT INTO " . AUTH_TOKENS_TABLE . " (user_id, token_hash, expires_at, user_agent, created_at) VALUES (:uid, :th, :exp, :ua, NOW())");
        $stmt->execute([
            'uid' => $userId,
            'th' => $tokenHash,
            'exp' => date('Y-m-d H:i:s', $expiresAt),
            'ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255)
        ]);

        // Set secure cookie with raw tokenHex (we store only hash)
        setcookie(AUTH_REMEMBER_COOKIE, $userId . ':' . $tokenHex, time() + AUTH_REMEMBER_EXPIRE, '/', '', AUTH_COOKIE_SECURE, true);
    }

    protected function checkRemembered()
    {
        if (!empty($_SESSION['auth_user_id'])) {
            return;
        }
        if (empty($_COOKIE[AUTH_REMEMBER_COOKIE])) {
            return;
        }

        $value = $_COOKIE[AUTH_REMEMBER_COOKIE];
        [$uid, $tokenHex] = explode(':', $value) + [null, null];
        if (!$uid || !$tokenHex) {
            return;
        }

        $tokenHash = hash('sha256', $tokenHex);
        $stmt = $this->db->prepare("SELECT * FROM " . AUTH_TOKENS_TABLE . " WHERE user_id = :uid AND token_hash = :th AND expires_at > NOW() LIMIT 1");
        $stmt->execute(['uid' => $uid, 'th' => $tokenHash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // rotate token: delete old and issue new
            $this->deleteTokenById((int)$row['id']);
            // create new remember
            $this->rememberUser((int)$uid);

            // load user into session
            $stmt2 = $this->db->prepare("SELECT * FROM " . AUTH_USER_TABLE . " WHERE id = :id LIMIT 1");
            $stmt2->execute(['id' => $uid]);
            $user = $stmt2->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $this->loginSession($user);
            }
        } else {
            // possible stolen cookie - remove it
            setcookie(AUTH_REMEMBER_COOKIE, '', time() - 3600, '/', '', AUTH_COOKIE_SECURE, true);
        }
    }

    protected function deleteTokenById(int $id)
    {
        $stmt = $this->db->prepare("DELETE FROM " . AUTH_TOKENS_TABLE . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    protected function forgetRememberTokens(int $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM " . AUTH_TOKENS_TABLE . " WHERE user_id = :uid");
        $stmt->execute(['uid' => $userId]);
        setcookie(AUTH_REMEMBER_COOKIE, '', time() - 3600, '/', '', AUTH_COOKIE_SECURE, true);
    }

    /* ---------- Session / user helpers ---------- */

    protected function loginSession(array $userRow)
    {
        // Minimal user session footprint - now includes role
        $_SESSION['auth_user_id'] = $userRow['id'];
        $_SESSION['auth_user_email'] = $userRow['email'];
        $_SESSION['auth_user_name'] = $userRow['name'] ?? null;
        $_SESSION['auth_user_role'] = $userRow['role'] ?? 'user';
        // regenerate session id
        Session::regenerate();
        $this->loadUserFromSession();
    }

    protected function loadUserFromSession()
    {
        if (!empty($_SESSION['auth_user_id'])) {
            $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM " . AUTH_USER_TABLE . " WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $_SESSION['auth_user_id']]);
            $this->user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } else {
            $this->user = null;
        }
    }

    /* ---------- Login attempt tracking (simple, DB-backed) ---------- */

    protected function recordFailedAttempt(string $email)
    {
        // you can store by email and/or IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $stmt = $this->db->prepare("INSERT INTO login_attempts (email, ip, attempted_at) VALUES (:email, :ip, NOW())");
        $stmt->execute(['email' => $email, 'ip' => $ip]);
    }

    protected function clearFailedAttempts(string $email)
    {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE email = :email OR ip = :ip");
        $stmt->execute(['email' => $email, 'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0']);
    }

    protected function isLocked(string $email): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $minutes = AUTH_LOCKOUT_MINUTES;
        $stmt = $this->db->prepare("SELECT COUNT(*) AS attempts FROM login_attempts WHERE (email = :email OR ip = :ip) AND attempted_at > (NOW() - INTERVAL :minutes MINUTE)");
        // PDO doesn't accept interval variable; pass minutes as integer and assemble query (safe here because integer)
        $sql = "SELECT COUNT(*) AS attempts FROM login_attempts WHERE (email = :email OR ip = :ip) AND attempted_at > (NOW() - INTERVAL " . ((int)$minutes) . " MINUTE)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email, 'ip' => $ip]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row && (int)$row['attempts'] >= AUTH_MAX_ATTEMPTS);
    }
}