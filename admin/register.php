<?php
/**
 * Admin Registration Form
 *
 * Form to register a new admin user
 * WARNING: This file should be deleted after initial admin registration.
 */

require_once __DIR__ . '/../includes/bootstrap.php';

$latte->render(__DIR__ . '/../templates/admin/register.latte');
