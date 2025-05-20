<?php
include "connect.php"; // Provides $db PDO object
print "<link rel='stylesheet' href='style.css' type='text/css'>";

$playername = isset($_GET['player']) ? trim($_GET['player']) : '';
$validkey = isset($_GET['keynode']) ? trim($_GET['keynode']) : '';

print "<table class='maintable'>";
print "<tr class='headline'><td><center>Account Activation</center></td></tr>";
print "<tr class='forumrow'><td><center>";

if (empty($playername) || empty($validkey)) {
    print "Invalid activation parameters.";
} else {
    // Check if user exists with the given playername, validkey and is not yet validated
    $stmt_check = $db->prepare("SELECT * FROM km_users WHERE playername = :playername AND validkey = :validkey AND validated = '0'");
    $stmt_check->bindParam(':playername', $playername);
    $stmt_check->bindParam(':validkey', $validkey);
    $stmt_check->execute();
    $user_to_activate = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($user_to_activate) {
        // User found and not validated, proceed with activation
        $stmt_update = $db->prepare("UPDATE km_users SET validated = '1' WHERE playername = :playername AND validkey = :validkey");
        $stmt_update->bindParam(':playername', $playername);
        $stmt_update->bindParam(':validkey', $validkey);
        
        if ($stmt_update->execute()) {
            print "Account activated successfully. You can now log in.";
        } else {
            print "Could not activate account. Please contact support.";
        }
    } else {
        // User not found with validated = '0'. Check if already validated or if details are wrong.
        $stmt_already_active_or_invalid = $db->prepare("SELECT * FROM km_users WHERE playername = :playername AND validkey = :validkey AND validated = '1'");
        $stmt_already_active_or_invalid->bindParam(':playername', $playername);
        $stmt_already_active_or_invalid->bindParam(':validkey', $validkey);
        $stmt_already_active_or_invalid->execute();
        
        if ($stmt_already_active_or_invalid->fetch(PDO::FETCH_ASSOC)) {
            print "Account already activated or activation link has been used.";
        } else {
            print "Invalid activation link or user not found. Please check the link or contact support.";
        }
    }
}

print "</center></td></tr></table>";
?>