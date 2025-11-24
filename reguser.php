<?php
/**
 * User Registration Handler
 *
 * Processes new user registration with password hashing
 */

require_once 'includes/bootstrap.php';

// Site URL for activation email (use config or fallback to hardcoded)
$path = defined('SITE_URL') ? SITE_URL : "http://rutgerx99.ninetynine.axc.nl";
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize inputs
        $player = Validator::sanitizeString($_POST['player'] ?? '', 50);
        $password = $_POST['password'] ?? '';
        $pass2 = $_POST['pass2'] ?? '';
        $email = Validator::sanitizeEmail($_POST['email'] ?? '');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        // Validate passwords match
        if ($password !== $pass2) {
            $message = "Your passwords didn't match.";
        } elseif (empty($password) || empty($pass2)) {
            $message = "You did not enter a password.";
        } else {
            // Check username constraints
            $playerLength = strlen($player);
            if ($playerLength > 21 || $playerLength < 5) {
                $message = "Username must be between 5 and 21 characters.";
            } else {
                // Check if player already exists
                $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :player");
                $stmt->execute(['player' => $player]);
                $existingPlayer = $stmt->fetch();

                if ($existingPlayer) {
                    $message = "There is already a player with that name.";
                } else {
                    // Check if email already exists
                    $stmt = $db->prepare("SELECT * FROM km_users WHERE email = :email");
                    $stmt->execute(['email' => $email]);
                    $existingEmail = $stmt->fetch();

                    if ($existingEmail) {
                        $message = "There is already a player with that e-mail address.";
                    } else {
                        // Hash password with modern algorithm
                        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

                        // Generate activation key
                        $thekey = bin2hex(random_bytes(32));

                        // Insert new user
                        $stmt = $db->prepare("
                            INSERT INTO km_users
                            (playername, password, email, validated, validkey, numturns, ip)
                            VALUES (:player, :password, :email, '0', :validkey, '30', :ip)
                        ");

                        $stmt->execute([
                            'player' => $player,
                            'password' => $hashedPassword,
                            'email' => $email,
                            'validkey' => $thekey,
                            'ip' => $ip
                        ]);

                        // Send activation email
                        $activationUrl = "$path/activate.php?player=" . urlencode($player) . "&keynode=$thekey";
                        $emailSubject = "Your Survival War Activation Key";
                        $emailBody = "Welcome to Survival War!\n\nClick the link below to activate your account:\n$activationUrl\n\nIf you did not create this account, please ignore this email.";

                        mail($email, $emailSubject, $emailBody, "From: " . MAIL_FROM);

                        $message = "Registration successful! You have been sent an activation key to your email.<br>";
                        $message .= "Click here to <a href='login.php'>Login</a>";
                    }
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        $message = "An error occurred during registration. Please try again.";
    }
}

// Render template
$template = TemplateEngine::getInstance();
$template->display('pages/reguser.latte', ['message' => $message]);
