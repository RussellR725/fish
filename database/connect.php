<?php

$host="Your host";
$user="Your Username";
$pass="Your Password!";
$db="Your Database";
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
    echo "Failed to connect DB".$conn->connect_error;
}
?>