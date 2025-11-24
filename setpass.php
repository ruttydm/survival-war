<?php
/**
 * Password Change Module
 *
 * Allows logged-in users to change their password
 */

require_once 'includes/bootstrap.php';

$templateData = [
    'loggedIn' => false,
    'showForm' => false,
    'message' => null
];

if (isset($_SESSION['player'])) {
    $templateData['loggedIn'] = true;
    $player = $_SESSION['player'];

    if (isset($_POST['submit']) || isset($_POST['password'])) {
        try {
            $password = $_POST['password'] ?? '';

            // Validate password length
            if (strlen($password) < 1) {
                $templateData['message'] = "You need to enter a password.";
            } elseif (strlen($password) < 6) {
                $templateData['message'] = "Password must be at least 6 characters long.";
            } else {
                // Hash password with modern algorithm
                $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

                // Update password in database
                $stmt = $db->prepare("UPDATE km_users SET password = :password WHERE playername = :player");
                $stmt->execute([
                    'password' => $hashedPassword,
                    'player' => $player
                ]);

                // Clear session to force re-login
                Session::destroy();

                $templateData['message'] = "Password changed successfully!<br><br>For security, you have been logged out. Please <a href='login.php'>login</a> with your new password.";
            }
        } catch (PDOException $e) {
            error_log("Password change error for user '$player': " . $e->getMessage());
            $templateData['message'] = "An error occurred while changing your password. Please try again.";
        }
    } else {
        // Display password change form
        $templateData['showForm'] = true;
    }
}

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/setpass.latte', $templateData);
?>
