<?php
//killmonster amin index
session_start();
include 'up_html.php';

      print "<table class='maintable'><tr class='headline'><td><center>Buy Army</center></td></tr>";
      print "<tr class='mainrow'><td>";
      print "Each troop Cost 75 gold and each acres can support a max of ten troops:<br>";
      print "<form method='post' action=''>";
      print "Offensive Units:&nbsp;<input type='text' name='off' size='6' class='inline_text_inp' style='width:75px;'><br>";
      print "Defensive Units:&nbsp;<input type='text' name='dff' size='6' class='inline_text_inp' style='width:75px;'><br>";
      print "<input type='submit' name='submit' value='Buy Army' class='RedButton' style='width:93px'></form>";
      print "</td></tr></table><br><br>";
if (isset($_SESSION['player'])) 
  {
    $player=$_SESSION['player'];
    $userstats="SELECT * from km_users where playername='$player'";
    $userstats2=mysql_query($userstats) or die("Could not get user stats");
    $userstats3=mysql_fetch_array($userstats2);
    if(isset($_POST['submit']))
    {
        $off=$_POST['off'];          
        $dff=$_POST['dff'];
        $off=strip_tags($off);
        $dff=strip_tags($dff);
        $totalcost=($off+$dff)*75;
        $totalunits=$off+$dff+$userstats3[offarmy]+$userstats3[dffarmy];
        $landhold=$userstats3[land]*10;
        $threshold=date("U")-3600*6;    
        $thetime=date("U");  
        print "<center>";
        print "<table class='maintable'>";
        print "<tr class='headline'><td><center>Buy Land</center></td></tr>";
        print "<tr class='mainrow'><td>";
        if($off<0||$dff<0)
        {
           die("You may not buy negative units");
        }
        else if($userstats3[numturns]<1)
        {
           die("You must have at least 1 turn to buy units. Go back to <A href='index.php'>Main</a>");
        }
        else if($totalcost>$userstats3[gold])
        {
          die("You do not have that much gold, go back to <A href='index.php'>Main page</a>");
        }
        else if($totalunits>$landhold)
        {
          die("You do not have enough land to support that many units, go back to <A href='index.php'>Main</a>");
        }
        else if($userstats3[lastaction]>$threshold)
        {
          die("You have to wait six hours after an attack to buy troops, go back to <A href='index.php'>Main</a>.");
        }
        else if($totalunits<=$landhold)
        {
           $getarmy="update km_users set gold=gold-'$totalcost', offarmy=offarmy+'$off', dffarmy=dffarmy+'$dff', numturns=numturns-1 where ID='$userstats3[ID]'";
           mysql_query($getarmy) or die("Can't get army");
           print "Troops aquired. go back to <A href='index.php'>Main</a>.";
        }
        print "</td></tr></table>";



    }
  }
else
  {
     echo "<script>
window.location.href = 'login.php';
</script>";
  
  }
include 'down_html.php';

?>

