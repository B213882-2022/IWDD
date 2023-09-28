<!-- Codes are modified from from the scripts provided in course: 'Introduction to Website and Database Design for Drug Discovery' lectured by Dr. Paul Taylor. -->
<?php
session_start();
require_once 'login.php';
require 'menuf.php';  // include a menu at the left
require 'functions.php';  // include a PHP with pre-decided functions

// pass user forname and surename to SESSION
if(isset($_POST['fn']) && isset($_POST['sn'])){
    $_SESSION['forname'] = $_POST['fn'];
    $_SESSION['surname'] = $_POST['sn'];
} 
else{ 
    header('location: http://mscidwd.bch.ed.ac.uk/s2321661/complib.php');  //Jump to other PHP if user names are unset (ensure users cannot access this PHP via url directly)
}

// By defalut, use all suppliers
$suppliers_info = suppliers_info();
$_SESSION['suppliers'] = $suppliers_info['all_names'];
?>
<html>
    <head>
        <title>Index</title>    
        <link rel="stylesheet" href="style.css">  
    </head>
    <body style="width:70%; margin:auto;padding:30px 20px;">
        For functions 2 to 5, you need to select your suppliers first! (By default, all suppliers are selected)
    </body>
</html>