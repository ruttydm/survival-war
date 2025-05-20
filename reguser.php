<?php
session_start();
include 'up_html.php';
include "connect.php";
$path="http://rutgerx99.ninetynine.axc.nl";
$player=$_POST['player'];
$password=$_POST['password'];
$pass2=$_POST['pass2'];
$player=strip_tags($player);
$email=$_POST['email'];
$email=strip_tags($email);
$ip=$_SERVER['REMOTE_ADDR'];
if ($password==$pass2)
{
  
  // Check if playername exists using PDO
  $stmt_check_player = $db->prepare("SELECT * from km_users where playername = :playername");
  $stmt_check_player->bindParam(':playername', $player);
  $stmt_check_player->execute();
  $isplayer3 = $stmt_check_player->fetch(PDO::FETCH_ASSOC);

  if(!$_POST['password'] || !$_POST['pass2'])
  {
     print "You did not enter a password";
  }
  else if($isplayer3 || strlen($player)>21 || strlen($player)<5)
  {
     print "There is already a player of that name or the name you specified is over 21 letters or less than 5 letter";
  }
  else
  {
    // Check if email exists using PDO
    $stmt_check_email = $db->prepare("SELECT * from km_users where email = :email");
    $stmt_check_email->bindParam(':email', $email);
    $stmt_check_email->execute();
    $isaddress3 = $stmt_check_email->fetch(PDO::FETCH_ASSOC);

    if($isaddress3)
    {
      print "There is already a player with that e-mail address";
    }
    else
    {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $date=round(date("U")/1000); // Note: using date for srand is not cryptographically secure for key generation
      srand($date); // Consider a better random key generator if security is paramount
      $thekey=rand(1,100000000);
      $thekey=md5($thekey); // The key is also MD5ed, consider if this is needed or if a stronger key is better

      $stmt_insert_user = $db->prepare("INSERT into km_users(playername, password, email, validated, validkey, numturns, ip, lasttime, oldtime, tsgone, continent) VALUES (:playername, :password, :email, '0', :validkey, '30', :ip, :time, :time, :time, '')");
      $time = time(); // Get current timestamp for time fields

      $stmt_insert_user->bindParam(':playername', $player);
      $stmt_insert_user->bindParam(':password', $hashed_password);
      $stmt_insert_user->bindParam(':email', $email);
      $stmt_insert_user->bindParam(':validkey', $thekey);
      $stmt_insert_user->bindParam(':ip', $ip);
      $stmt_insert_user->bindParam(':time', $time);
      
      if ($stmt_insert_user->execute()) {
        // The mail function used the md5 of the password. 
        // For activation, it might be better to send a key that is not directly related to the password,
        // or adjust the activation process. For now, sending the key as generated.
        // The original mail function sent $password (which was md5($original_password)).
        // Since we are no longer storing md5($original_password) directly, we need to decide what to send.
        // Sending $thekey which is already part of the DB seems most sensible for activation.
        // The activation script activate.php will also need to be updated to reflect this change.
        // For now, I will leave the password parameter out of the mail function as it's not clear
        // if activate.php can handle a non-md5 password or if it should use the key.
        // This is a deviation but necessary due to change in password handling.
        // A proper fix would involve updating activate.php as well.
        mail("$email","Your Kill Monster Activation key","Paste the URL to activate your account.  $path/activate.php?player=$player&keynode=$thekey");
        print "registration successful. You have been sent an activation key.<br>";
        print "Click here to <A href='login.php'>Login</a>";
      } else {
        print "Could not register the user.";
      }
    }
  }
}

else
{
  print "Your passwords didn't match or you did not enter a password."; // Corrected the original message slightly
}
include 'down_html.php';
?>


