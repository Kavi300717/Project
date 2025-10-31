<?php
// includes/auth.php
require_once __DIR__ . '/config.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}
function current_user_role() {
    return $_SESSION['role'] ?? null;
}
function current_user_name() {
    return $_SESSION['name'] ?? null;
}
function require_login() {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}
function require_admin() {
    if (current_user_role() !== 'admin') {
        http_response_code(403);
        exit('403 - Forbidden');
    }
}
