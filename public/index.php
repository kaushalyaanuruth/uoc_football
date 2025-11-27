<?php

// Start output buffering to prevent "headers already sent" errors
ob_start();

session_start();

require '../app/core/init.php';

$app = new App();
$app->loadController();

// Flush output buffer
ob_end_flush();
