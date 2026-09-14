<?php

require_once __DIR__ . '/functions.php';

function require_login() {
    if (empty($_SESSION['user_id'])) {
        redirect('index.php?page=login');
    }
}

function require_role($roles) {
    require_login();
    $roles = is_array($roles) ? $roles : [$roles];
    $currentRole = $_SESSION['role'] ?? '';

    if (!is_valid_role($currentRole) || !in_array($currentRole, $roles, true)) {
        redirect('index.php?page=' . rawurlencode($currentRole ?: 'login'));
    }
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_role() {
    return $_SESSION['role'] ?? null;
}
