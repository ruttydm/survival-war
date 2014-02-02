<?php
#!/usr/local/bin/php
include "connect.php";
mysql_query("Update km_users set numturns=numturns+'10' Where numturns<='90'") or die("Cron failed");	
mysql_query("Update km_users set numturns='100' Where numturns>'90'") or die("Cron failed");	
?>