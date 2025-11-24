<?php
require_once 'includes/bootstrap.php';

// If already logged in, redirect to index
if (isset($_SESSION['player'])) {
    header("Location: index.php");
    exit;
}

// Prepare template data
$templateData = [
    'loginError' => $_SESSION['login_error'] ?? null,
];

// Clear error after retrieving
if (isset($_SESSION['login_error'])) {
    unset($_SESSION['login_error']);
}

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/login.latte', $templateData);
