<?php
// ---------- Settings ----------
define('SESSION_TIMEOUT', 900);   // 15 minutes of inactivity
define('REMEMBER_DAYS', 30);
define('MAX_ATTEMPTS', 5);
define('LOCK_MINUTES', 10);
define('IS_HTTPS', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

// ---------- Secure session ----------
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
    'lifetime' => 0,            // dies when browser closes
    'path'     => '/',
    'secure'   => IS_HTTPS,
    'httponly' => true,         // JS cannot read it
    'samesite' => 'Strict'
]);
session_start();

// ---------- Database ----------
$conn = new mysqli("localhost", "root", "", "studenthub");
if ($conn->connect_error) {
    die("Connection failed");
}
$conn->set_charset("utf8mb4");

// ---------- Helpers ----------
function login_user($id, $name, $role) {
    session_regenerate_id(true);          // prevents session fixation
    $_SESSION['user_id']       = $id;
    $_SESSION['name']          = $name;
    $_SESSION['role']          = $role;
    $_SESSION['last_activity'] = time();
}

function set_remember_cookie($value, $expires) {
    setcookie('remember_me', $value, [
        'expires'  => $expires,
        'path'     => '/',
        'secure'   => IS_HTTPS,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

function create_remember_token($conn, $userId) {
    $selector  = bin2hex(random_bytes(9));    // 18 chars, public lookup key
    $validator = bin2hex(random_bytes(32));   // secret, only its hash is stored
    $hash      = hash('sha256', $validator);
    $expiresTs = time() + REMEMBER_DAYS * 86400;
    $expires   = date('Y-m-d H:i:s', $expiresTs);

    $stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $selector, $hash, $expires);
    $stmt->execute();
    $stmt->close();

    set_remember_cookie($selector . ':' . $validator, $expiresTs);
}

function try_remember_login($conn) {
    if (empty($_COOKIE['remember_me'])) return;

    $parts = explode(':', $_COOKIE['remember_me']);
    if (count($parts) !== 2) {
        set_remember_cookie('', time() - 3600);
        return;
    }
    list($selector, $validator) = $parts;
    $now = date('Y-m-d H:i:s');

    $stmt = $conn->prepare(
        "SELECT t.id, t.token_hash, u.id AS uid, u.name, u.role
         FROM remember_tokens t JOIN users u ON u.id = t.user_id
         WHERE t.selector = ? AND t.expires_at > ?");
    $stmt->bind_param("ss", $selector, $now);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && hash_equals($row['token_hash'], hash('sha256', $validator))) {
        // Rotate: delete used token, issue a new one
        $del = $conn->prepare("DELETE FROM remember_tokens WHERE id = ?");
        $del->bind_param("i", $row['id']);
        $del->execute();
        $del->close();

        login_user($row['uid'], $row['name'], $row['role']);
        create_remember_token($conn, $row['uid']);
    } else {
        $del = $conn->prepare("DELETE FROM remember_tokens WHERE selector = ?");
        $del->bind_param("s", $selector);
        $del->execute();
        $del->close();
        set_remember_cookie('', time() - 3600);
    }
}

function logout_user($conn) {
    // remove remember-me token from DB + browser
    if (!empty($_COOKIE['remember_me'])) {
        $parts = explode(':', $_COOKIE['remember_me']);
        $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE selector = ?");
        $stmt->bind_param("s", $parts[0]);
        $stmt->execute();
        $stmt->close();
        set_remember_cookie('', time() - 3600);
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_login() {
    header('Cache-Control: no-store, no-cache, must-revalidate'); // back button won't show private pages
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.html?error=required');
        exit;
    }
}

function require_role($roles) {
    require_login();
    $roles = (array)$roles;
    if (!in_array($_SESSION['role'], $roles, true)) {
        http_response_code(403);
        die("<h2 style='text-align:center;margin-top:50px'>403 - Access denied</h2>");
    }
}

// ---------- Runs on every page that includes config.php ----------
if (isset($_SESSION['user_id'])) {
    // Idle timeout
    if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_TIMEOUT) {
        logout_user($conn);            // also clears remember-me, so idle timeout wins
        header('Location: login.html?error=timeout');
        exit;
    }
    $_SESSION['last_activity'] = time();
} else {
    try_remember_login($conn);         // auto-login from cookie if valid
}