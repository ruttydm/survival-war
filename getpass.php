<?php //module for password retrieval
 include 'connect.php';
 include 'up_html.php';
 if(isset($_POST['submit']))
 {
   $getpassword=$_POST['getpassword'];
   $passkey="SELECT * from km_users where email='$getpassword'";
   $passkey2=mysql_query($passkey) or die("Blah");
   $passkey3=mysql_fetch_array($passkey2);
   if(!$passkey3)
   {
     print "We have no player with that e-mail address";
   }
   else 
   {
        $email=$passkey3[email];
        $day=date("U");
        srand($day);
        $blah=RAND(1000000,2000000);
        $blah2=md5($blah);
        $newpass="update km_users set password='$blah2' where ID='$passkey3[ID]'";
        mysql_query($newpass) or die("Could not insert new password");
        mail("$email", "Password", "Your password has been set to  $blah ");
        print "Password sent";
    }
   
 
 }
 else
 {
   print "<form action='' method='post'>";
   print "Enter e-mail address: <input type='text' name='getpassword' size='20' class='inline_text_inp' style='width:180px'>&nbsp;";
   print "<input type='submit' name='submit' value='submit' class='RedButton' style='width:80px'></form>&nbsp;";
   print "<form action='login.php' method='post'><input type='submit' name='submit' value='cancel' class='RedButton' style='width:80px'></form>";	 
 }
include 'down_html.php';
?>