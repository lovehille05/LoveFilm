<?php
declare(strict_types=1);

const LOVEFILM_BASE_URL = '/Lovehille';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
    'path' => LOVEFILM_BASE_URL . '/',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/database.php';

function escape_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $submittedToken): bool
{
    $sessionToken = $_SESSION['csrf_token'] ?? null;

    return is_string($sessionToken)
        && is_string($submittedToken)
        && hash_equals($sessionToken, $submittedToken);
}

function current_user(): ?array
{
    $user = $_SESSION['user'] ?? null;

    return is_array($user) && isset($user['id'], $user['name'])
        ? $user
        : null;
}

function redirect_to(string $path): void
{
    header('Location: ' . LOVEFILM_BASE_URL . $path, true, 303);
    exit;
}

function require_login(): array
{
    $user = current_user();

    if ($user === null) {
        redirect_to('/login.php');
    }

    return $user;
}

function redirect_authenticated_user(): void
{
    if (current_user() !== null) {
        redirect_to('/index.php');
    }
}
