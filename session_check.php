<?php
// session_check.php
// Usage: require 'session_check.php'; at top of any protected PHP page.
// Pass $required_role = 'patient' | 'pharmacy' | 'admin' before including.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

if (isset($required_role) && $_SESSION['role'] !== $required_role) {
    // Wrong role — send them to their own dashboard
    $dashboards = [
        'patient'  => 'user-dashboard.php',
        'pharmacy' => 'pharmacy-dashboard.php',
        'admin'    => 'admin-dashboard.php',
    ];
    header('Location: ' . ($dashboards[$_SESSION['role']] ?? 'index.php'));
    exit;
}
?>