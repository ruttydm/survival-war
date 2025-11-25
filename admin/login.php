<?php
/**
 * Admin Login
 *
 * Login page for administrators
 */

require_once __DIR__ . '/../includes/bootstrap.php';

$template = TemplateEngine::getInstance();
$template->display('admin/login.latte');
