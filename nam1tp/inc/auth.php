<?php
if (!ob_get_level()) {
    ob_start();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user_id']);
}

function require_admin_auth(): void {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function current_admin(): array {
    return [
        'id'       => $_SESSION['admin_user_id'] ?? 0,
        'username' => $_SESSION['admin_username'] ?? '',
        'fullname' => $_SESSION['admin_fullname'] ?? 'Admin',
        'role'     => $_SESSION['admin_role'] ?? 'admin'
    ];
}

function attempt_admin_login(string $username, string $password): bool {
    $db = db();
    try {
        $stmt = $db->prepare("SELECT id, username, password, fullname, role FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id']   = (int)$user['id'];
            $_SESSION['admin_username']  = $user['username'];
            $_SESSION['admin_fullname']  = $user['fullname'] ?? $user['username'];
            $_SESSION['admin_role']      = $user['role'] ?? 'admin';
            return true;
        }
    } catch (Throwable $e) {}
    return false;
}

function admin_logout(): void {
    $_SESSION['admin_logged_in'] = false;
    unset(
        $_SESSION['admin_logged_in'],
        $_SESSION['admin_user_id'],
        $_SESSION['admin_username'],
        $_SESSION['admin_fullname'],
        $_SESSION['admin_role']
    );
    session_destroy();
}

function set_flash(string $type, string $message): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_msg'] = [
        'type' => $type,
        'text' => $message
    ];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash_msg'])) {
        $msg = $_SESSION['flash_msg'];
        unset($_SESSION['flash_msg']);
        return $msg;
    }
    return null;
}
