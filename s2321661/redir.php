<?php
// ensure users cannot directly access this PHP by url
if(!(isset($_SESSION['forname']) &&
    isset($_SESSION['surname'])))
    {
    header('location: http://mscidwd.bch.ed.ac.uk/s2321661/complib.php');
    }
?>
