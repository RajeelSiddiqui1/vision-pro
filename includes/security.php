<?php


if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1); 
    ini_set('session.cookie_samesite', 'Lax'); 
    ini_set('session.use_strict_mode', 1); 
    ini_set('session.gc_maxlifetime', 7200); 
    session_start();
}


header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data: https:; connect-src 'self';");
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');


function no_cache_headers()
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
}


function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): void
{
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        die('Invalid security token. Please refresh the page and try again.');
    }
}

function verify_csrf_token(?string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token ?? '');
}


function redirect_if_logged_in(): void
{
    if (isset($_SESSION['user_id'])) {
        no_cache_headers();
        $dest = ($_SESSION['user_role'] === 'admin') ? 'admin.php' : 'dashboard.php';
        header("Location: $dest");
        exit;
    }
}


function require_auth(string $redirect_to = 'login.php'): void
{
    if (!isset($_SESSION['user_id'])) {
        no_cache_headers();
        $url = urlencode($_SERVER['REQUEST_URI']);
        header("Location: {$redirect_to}?redirect={$url}");
        exit;
    }
}


function require_admin(): void
{
    require_auth();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        http_response_code(403);
        die('Access Denied.');
    }
}


function e(mixed $val): string
{
    return htmlspecialchars((string)$val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function sanitize_redirect(string $url, string $fallback = 'dashboard.php'): string
{
    $parsed = parse_url($url);
    if (!empty($parsed['host'])) {
        return $fallback; 
    }
    return ltrim($url, '/') ?: $fallback;
}


function check_rate_limit(string $key, int $max = 5, int $window = 300): bool
{
    $now = time();
    $_SESSION['rate_limit'][$key] = $_SESSION['rate_limit'][$key] ?? ['count' => 0, 'start' => $now];
    $rl = & $_SESSION['rate_limit'][$key];
    if ($now - $rl['start'] > $window) {
        $rl = ['count' => 0, 'start' => $now]; 
    }
    $rl['count']++;
    return $rl['count'] <= $max;
}
