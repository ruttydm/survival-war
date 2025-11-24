<?php
// Database connection configuration
$host = 'localhost'; 
$database = 'survival_war';
$user = 'root'; 
$password = 'cdcdcd10';
$version = '1';
	
parse_str($_SERVER['QUERY_STRING'] ?? '');

// Use mysqli instead of deprecated mysql_* functions
$db = mysqli_connect($host, $user, $password, $database) or die("Could not connect.");

if(!$db) 
	die("no db");

// Set character set to utf8mb4
mysqli_set_charset($db, "utf8mb4");

// Escape input data to prevent SQL injection
if(!get_magic_quotes_gpc())
{
  $_GET = array_map(function($val) use ($db) { 
    return mysqli_real_escape_string($db, $val); 
  }, $_GET); 
  $_POST = array_map(function($val) use ($db) { 
    return mysqli_real_escape_string($db, $val); 
  }, $_POST); 
  $_COOKIE = array_map(function($val) use ($db) { 
    return mysqli_real_escape_string($db, $val); 
  }, $_COOKIE);
}
else
{  
   $_GET = array_map('stripslashes', $_GET); 
   $_POST = array_map('stripslashes', $_POST); 
   $_COOKIE = array_map('stripslashes', $_COOKIE);
   $_GET = array_map(function($val) use ($db) { 
     return mysqli_real_escape_string($db, $val); 
   }, $_GET); 
   $_POST = array_map(function($val) use ($db) { 
     return mysqli_real_escape_string($db, $val); 
   }, $_POST); 
   $_COOKIE = array_map(function($val) use ($db) { 
     return mysqli_real_escape_string($db, $val); 
   }, $_COOKIE);
}
?>
