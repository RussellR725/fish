<?php
include '../database/connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST"){ 
    $search = $_POST['search'];
    header("Location: profile.php?profile=".$search);
}
?>