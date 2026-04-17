<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log the logout
error_log("=== LOGOUT ===");
error_log("User: " . ($_SESSION['user_id'] ?? 'Unknown'));

// Clear all session data
$_SESSION = [];
session_destroy();

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to landing page
header('Location: ../public/index.php?url=landingPage');
exit();
?>
