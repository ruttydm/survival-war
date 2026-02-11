<?php
/**
 * Admin Registration
 *
 * Creates a new admin user with modern Argon2ID password hashing.
 * WARNING: This file should be deleted after initial admin registration.
 */

require_once __DIR__ . '/../includes/bootstrap.php';

try {
    // Sanitize inputs
    $username = Validator::sanitizeString($_POST['username'] ?? '', 50);
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Error: Username and password are required.";
    } else {
        // Check if admin user already exists
        $checkStmt = $db->prepare("SELECT id FROM km_users WHERE playername = :username AND status = '3'");
        $checkStmt->execute(['username' => $username]);

        if ($checkStmt->fetch()) {
            $message = "Error: Admin user already exists with this username.";
        } else {
            // Hash password with Argon2ID
            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

            // Insert new admin user
            $insertStmt = $db->prepare("INSERT INTO km_users (playername, password, status, validated) VALUES (:username, :password, '3', '1')");
            $insertStmt->execute([
                'username' => $username,
                'password' => $hashedPassword
            ]);

            $message = "Admin registered successfully. You should probably delete the admin register files now. You can login to your admin account <a href='login.php'>Here</a>.";
        }
    }
} catch (PDOException $e) {
    error_log("Admin registration error: " . $e->getMessage());
    $message = "Error: An error occurred during registration. Please try again.";
}

$template = TemplateEngine::getInstance();
$template->display('admin/reguser.latte', [
    'message' => $message
]);
