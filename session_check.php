<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

if (isset($required_role) && $_SESSION['role'] !== $required_role) {
    $dashboards = [
        'patient'  => 'user-dashboard.php',
        'pharmacy' => 'pharmacy-dashboard.php',
        'admin'    => 'admin-dashboard.php',
    ];
    header('Location: ' . ($dashboards[$_SESSION['role']] ?? 'index.php'));
    exit;
}
?>