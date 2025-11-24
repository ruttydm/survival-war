<?php
include "../connect.php";
session_start();
?>
<link rel="stylesheet" href="../style.css" type="text/css">
<?php
if (isset($_SESSION['player']))
  {
    $player=$_SESSION['player'];
    $stmt = $pdo->prepare("SELECT * FROM km_users WHERE playername = ?");
    $stmt->execute([$player]);
    $userstats3 = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userstats3) {
      die("Could not get user stats");
    }

    print "<center>";
    print "<table class='maintable'>";
    print "<tr class='headline'><td><center>Post a Message</center></td></tr>";
    print "<tr class='mainrow'><td>";
    if(isset($_POST['submit']))
    {
       $ID = filter_var($_POST['msgid'], FILTER_VALIDATE_INT);
       if (!$ID) {
         die("Invalid message ID");
       }
       $themessage=$_REQUEST['themessage'];
       $themessage=strip_tags($themessage);
       $stmt = $pdo->prepare("UPDATE km_messages SET message = ? WHERE msgid = ?");
       $stmt->execute([$themessage, $ID]);
       print "Message edited, please go back to the <A href='messages.php?ID=".htmlspecialchars($ID, ENT_QUOTES, 'UTF-8')."'>Thread</a>";



    }
    else
    {
       $ID = filter_var($_GET['ID'], FILTER_VALIDATE_INT);
       if (!$ID) {
         die("Invalid message ID");
       }
       $stmt = $pdo->prepare("SELECT * FROM km_messages WHERE msgid = ?");
       $stmt->execute([$ID]);
       $getmessage3 = $stmt->fetch(PDO::FETCH_ASSOC);

       if (!$getmessage3) {
         die("Could not get message");
       }

       if($userstats3['ID']==$getmessage3['posterid'] || $userstats3['status']==3)
       {
          print "<form action='edit.php' method='post'>";
          print "<input type='hidden' name='msgid' value='".htmlspecialchars($ID, ENT_QUOTES, 'UTF-8')."'>";
          print "Name: ".htmlspecialchars($userstats3['playername'], ENT_QUOTES, 'UTF-8')."<br><br>";
          print "<textarea name='themessage' rows='5' cols='40'>".htmlspecialchars($getmessage3['message'], ENT_QUOTES, 'UTF-8')."</textarea><br>";
          print "<input type='submit' name='submit' value='submit'></form>";


       }
       else
       {
         die("You cannot edit this");
       }


    }

  }
 

else
   {
     print "You are not logged in.";
   }
?> 