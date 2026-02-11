<?php
/**
 * Password Recovery Module
 *
 * Generates a new temporary password and sends it via email
 */

require_once 'includes/bootstrap.php';

$templateData = [
    'message' => null
];

if (isset($_POST['submit'])) {
    try {
        // Sanitize email input
        $email = Validator::sanitizeEmail($_POST['getpassword'] ?? '');

        if (empty($email) || !Validator::validateEmail($email)) {
            $templateData['message'] = "Please enter a valid e-mail address.";
        } else {
            // Check if user exists with this email
            $stmt = $db->prepare("SELECT * FROM km_users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if (!$user) {
                $templateData['message'] = "We have no player with that e-mail address.";
            } else {
                // Generate secure random temporary password
                $tempPassword = bin2hex(random_bytes(8)); // 16 character hex string

                // Hash the password with modern algorithm
                $hashedPassword = password_hash($tempPassword, PASSWORD_ARGON2ID);

                // Update user's password
                $updateStmt = $db->prepare("UPDATE km_users SET password = :password WHERE ID = :id");
                $updateStmt->execute([
                    'password' => $hashedPassword,
                    'id' => $user['ID']
                ]);

                // Send email with temporary password
                $emailSubject = "Survival War - Password Reset";
                $emailBody = "Your password has been reset.\n\n";
                $emailBody .= "Your new temporary password is: $tempPassword\n\n";
                $emailBody .= "Please log in and change your password immediately.\n\n";
                $emailBody .= "If you did not request this password reset, please contact support immediately.";

                $mailHeaders = "From: " . (defined('MAIL_FROM') ? MAIL_FROM : "noreply@survival-war.com");

                if (mail($email, $emailSubject, $emailBody, $mailHeaders)) {
                    $templateData['message'] = "A new temporary password has been sent to your email address.<br>Please check your email and <a href='login.php'>login</a> with the new password.";
                } else {
                    error_log("Failed to send password reset email to: $email");
                    $templateData['message'] = "Failed to send email. Please try again or contact support.";
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Password recovery error: " . $e->getMessage());
        $templateData['message'] = "An error occurred during password recovery. Please try again.";
    }
}

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/getpass.latte', $templateData);
?>
