<?php
/**
 * User Account Activation
 *
 * Activates user account via email activation link
 */

require_once 'includes/bootstrap.php';

$templateData = [
    'success' => false,
    'errorMessage' => ''
];

try {
    // Sanitize inputs
    $username = Validator::sanitizeString($_GET['player'] ?? '', 50);
    $keynode = Validator::sanitizeString($_GET['keynode'] ?? '', 64);
    $password = $_GET['password'] ?? ''; // Legacy parameter, optional

    if (empty($username) || empty($keynode)) {
        $templateData['errorMessage'] = "Invalid activation link.";
    } else {
        // Build query based on available parameters
        if (!empty($password)) {
            // Legacy activation link with password
            $stmt = $db->prepare("
                SELECT * FROM km_users
                WHERE playername = :username
                AND validkey = :keynode
            ");
            $stmt->execute([
                'username' => $username,
                'keynode' => $keynode
            ]);
            $user = $stmt->fetch();

            // Verify password for legacy links
            if ($user) {
                $passwordValid = false;

                // Check if password is MD5 (legacy) or modern hash
                if (strlen($user['password']) === 32 && ctype_xdigit($user['password'])) {
                    // Legacy MD5 password
                    if (md5($password) === $user['password']) {
                        $passwordValid = true;
                    }
                } else {
                    // Modern password hash
                    if (password_verify($password, $user['password'])) {
                        $passwordValid = true;
                    }
                }

                if (!$passwordValid) {
                    $user = null; // Invalidate if password doesn't match
                }
            }
        } else {
            // Modern activation link (only username and keynode)
            $stmt = $db->prepare("
                SELECT * FROM km_users
                WHERE playername = :username
                AND validkey = :keynode
            ");
            $stmt->execute([
                'username' => $username,
                'keynode' => $keynode
            ]);
            $user = $stmt->fetch();
        }

        if (!$user) {
            $templateData['errorMessage'] = "No such user or invalid activation link.";
        } else {
            // Activate the account
            $updateStmt = $db->prepare("UPDATE km_users SET validated = '1' WHERE playername = :username");
            $updateStmt->execute(['username' => $username]);

            $templateData['success'] = true;
        }
    }
} catch (PDOException $e) {
    error_log("Activation error: " . $e->getMessage());
    $templateData['errorMessage'] = "An error occurred during activation. Please try again or contact support.";
}

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/activate.latte', $templateData);
?>
