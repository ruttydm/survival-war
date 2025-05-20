<?php
session_start(); // Ensure session is started at the beginning
include "connect.php";

if (isset($_POST['submit'])) // name of submit button
{
    $player_unsafe = $_POST['player'];
    $password_plaintext = $_POST['password']; // Plain text password from form

    $player = strip_tags($player_unsafe);
    
    // Prepare statement to fetch user by playername
    $stmt = $db->prepare("SELECT * FROM km_users WHERE playername = :playername AND validated = '1'");
    $stmt->bindParam(':playername', $player);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $authenticated = false;

    if ($user) {
        // First, try to verify with password_verify
        if (password_verify($password_plaintext, $user['password'])) {
            $authenticated = true;
        } 
        // Else, if that fails, check if it's an old MD5 hash
        else if (md5($password_plaintext) === $user['password']) {
            $authenticated = true;
            // Rehash and update the password in the database
            $new_hash = password_hash($password_plaintext, PASSWORD_DEFAULT);
            $update_stmt = $db->prepare("UPDATE km_users SET password = :new_hash WHERE ID = :id");
            $update_stmt->bindParam(':new_hash', $new_hash);
            $update_stmt->bindParam(':id', $user['ID']);
            $update_stmt->execute();
        }
    }

    if ($authenticated)
    {
       $_SESSION['player'] = $user['playername']; // Use playername from DB to be safe
       echo "<script>
window.location.href = 'index.php';
</script>";
       exit; // Good practice to exit after redirect
    }
    else
    {
       print "Wrong username or password or non-activated account.";
    }
}

?>