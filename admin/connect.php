<?php

$host = 'localhost'; 
$database ='';
$user =''; 
$password ='';

try {
    $db = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

?>