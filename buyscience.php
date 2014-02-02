<?php
session_start();
include "up_html.php";


      print "<table class='maintable'><tr class='headline'><td><center>Buy army Training Pts</center></td></tr>";
      print "<tr class='mainrow'><td>";
      print "Each science point Cost 35 gold.<br>";
      $max=$userstats3[land]*10;
      if($userstats3[land]<=0)
      {
         print "Science not applicable due to lack of land.";
      }
      else
      {
        $percent=$userstats3[science]/$max*100;
        print "You have: $userstats3[science] pts ($percent % more power.)";
      }
      print "<form action='' method='post'>";
      print "<input type='text' name='scipts' size='6' class='inline_text_inp' style='width:75px;'><br>";
      print "<input type='submit' name='submit' value='submit' class='RedButton' style='width:93px'></form>";
      print "</td></tr></table>";
      print "</td>";


if (isset($_SESSION['player'])) 
  {
    $player=$_SESSION['player'];
    $userstats="SELECT * from km_users where playername='$player'";
    $userstats2=mysql_query($userstats) or die("Could not get user stats");
    $userstats3=mysql_fetch_array($userstats2);
    if(isset($_POST['submit']))
    {
         
        $scipts=$_POST['scipts'];
        $scipts=strip_tags($scipts);
        $totalcost=$scipts*35; 
        print "<br><center>";
        print "<table class='maintable'>";
        print "<tr class='headline'><td><center>Buy Science</center></td></tr>";
        print "<tr class='mainrow'><td>";
        if($scipts<0)
        {
           print "You cannot buy negative science. Back to <A href='index.php'>Main</a>";
        }
        else if($userstats3[numturns]<1)
        {
           die("You must have at least 1 turn to buy Science. Go back to <A href='index.php'>Main</a>");
        }
        else if($totalcost>$userstats3[gold])
        {
           die("You do not have enough gold. Please go back to <A href='index.php'>Main</a>");
        }
        else if($totalcost<=$userstats3[gold])
        {
            $updatestats="update km_users set science=science+'$scipts', gold=gold-'$totalcost', numturns=numturns-1 where ID='$userstats3[ID]'";
            mysql_query($updatestats) or die("Could not update stats");
            print "You have bought $scipts science points. Back to<A href='index.php'>Main Page</a>";


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
include "down_html.php";
?>

