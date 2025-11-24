<?php
/**
 * User Authentication
 *
 * Handles user login with support for legacy MD5 passwords
 * and modern password_hash() passwords.
 */

require_once 'includes/bootstrap.php';

if (isset($_POST['submit'])) {
    try {
        // Sanitize inputs
        $player = Validator::sanitizeString($_POST['player'] ?? '', 50);
        $password = $_POST['password'] ?? '';

        if (empty($player) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            // Query user by playername only
            $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player AND validated = '1'");
            $stmt->execute(['player' => $player]);
            $user = $stmt->fetch();

            if ($user) {
                $passwordValid = false;

                // Check if password is MD5 (legacy) or modern hash
                if (strlen($user['password']) === 32 && ctype_xdigit($user['password'])) {
                    // Legacy MD5 password
                    if (md5($password) === $user['password']) {
                        $passwordValid = true;

                        // Rehash password with modern algorithm
                        $newHash = password_hash($password, PASSWORD_ARGON2ID);
                        $updateStmt = $db->prepare("UPDATE km_users SET password = :password WHERE id = :id");
                        $updateStmt->execute(['password' => $newHash, 'id' => $user['id']]);
                    }
                } else {
                    // Modern password hash
                    if (password_verify($password, $user['password'])) {
                        $passwordValid = true;

                        // Check if rehash is needed (algorithm change)
                        if (password_needs_rehash($user['password'], PASSWORD_ARGON2ID)) {
                            $newHash = password_hash($password, PASSWORD_ARGON2ID);
                            $updateStmt = $db->prepare("UPDATE km_users SET password = :password WHERE id = :id");
                            $updateStmt->execute(['password' => $newHash, 'id' => $user['id']]);
                        }
                    }
                }

                if ($passwordValid) {
                    // Set session variables (using original session variable name for compatibility)
                    Session::setUser($user['id'], $user['playername']);
                    $_SESSION['player'] = $user['playername']; // Legacy compatibility

                    // Redirect to home page
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Wrong username or password.";
                }
            } else {
                $error = "Wrong username or password or non-activated account.";
            }
        }
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        $error = "An error occurred. Please try again.";
    }

    // If we got here, authentication failed
    if (isset($error)) {
        $_SESSION['login_error'] = $error;
        header("Location: login.php");
        exit;
    }
}

// If not POST, redirect to login
header("Location: login.php");
exit;
?>
