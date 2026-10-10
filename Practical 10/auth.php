
<?php
require_once "db.php";

ini_set("session.use_strict_mode", "1");
ini_set("session.use_only_cookies", "1");

session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
    "httponly" => true,
    "samesite" => "Lax"
]);

session_start();

function clearRememberCookie() {
    setcookie("remember_me", "", [
        "expires" => time() - 3600,
        "path" => "/",
        "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}

function endSession() {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), "", [
            "expires" => time() - 3600,
            "path" => $p["path"],
            "domain" => $p["domain"],
            "secure" => $p["secure"],
            "httponly" => $p["httponly"],
            "samesite" => $p["samesite"] ?? "Lax"
        ]);
    }

    session_destroy();
    clearRememberCookie();
}

if (isset($_SESSION["user_id"])) {
    if (time() - ($_SESSION["last_activity"] ?? 0) > 900) {
        endSession();
        header("Location: login.html?timeout=1");
        exit;
    }

    $_SESSION["last_activity"] = time();
} else {
    $cookie = $_COOKIE["remember_me"] ?? "";
    $parts = explode(":", $cookie, 2);

    if (count($parts) === 2 &&
        ctype_digit($parts[0]) &&
        strlen($parts[1]) === 64 &&
        ctype_xdigit($parts[1])) {

        $id = (int)$parts[0];
        $tokenHash = hash("sha256", $parts[1]);

        $stmt = $conn->prepare(
            "SELECT username, role FROM users
             WHERE id = ? AND remember_token_hash = ?
             AND remember_expires > NOW()"
        );
        $stmt->bind_param("is", $id, $tokenHash);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($username, $role);
            $stmt->fetch();

            session_regenerate_id(true);
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = $role;
            $_SESSION["last_activity"] = time();
        } else {
            clearRememberCookie();
        }
    } elseif ($cookie !== "") {
        clearRememberCookie();
    }
}

function requireLogin() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.html");
        exit;
    }
}

function requireRole($role) {
    requireLogin();

    if ($_SESSION["role"] !== $role) {
        http_response_code(403);
        exit("403 Forbidden: Access denied.");
    }
}