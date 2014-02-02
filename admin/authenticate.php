<?php

include "connect.php";

if (isset($_POST['submit'])) // name of submit button
{
    $isadmin=$_POST['isadmin'];
    $password=$_POST['password'];
    $password=md5($password);
    $query = "Select * from km_users where playername='$isadmin' and password='$password' and status='3'"; 
    $result = mysql_query($query) ;
    $isAuth = false; //set to false originally
    while($row = mysql_fetch_array($result))
    {
        if($row['playername'] === $isadmin) 
        {
           
            $isadmin="IAMADMIN";
            $isAuth = true;
            session_start();
            $_SESSION['isadmin']=$isadmin; 

        }  
    }  
    
    if($isAuth)
    {
echo "<script>
window.location.href = 'index.php';
</script>";
    }    
    else
   {
     print "Wrong username or password";
   }
}

?>