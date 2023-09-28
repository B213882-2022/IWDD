<?php  //debug php
session_start();
include 'redir.php'; 
echo<<<_HEAD1
<html>
<head>
<title>Debug Page</title>
<link rel="stylesheet" href="style.css">
</head>
<body style="width:70%; margin:auto;padding:30px 20px;">
_HEAD1;
include 'menuf.php';
$all_suppliers = array('Asinex','KeyOrganics','MayBridge','Nanosyn','Oai40000');
$smask = $_SESSION['supmask'];
$selected_suppliers = $_SESSION['suppliers'];
for($x=0;$x<count($selected_suppliers);$x++)
{
    echo $selected_suppliers[$x];
    echo "<br>";
    if(in_array($selected_suppliers[$x],$all_suppliers)){ echo 'found<br>';};
}
echo <<<_MAIN1
    <pre>
This is the debug page
Current value of Supplier mask $smask;
reset to 31
    </pre>
_MAIN1;
$_SESSION['supmask'] = 31;
echo <<<_TAIL1
</body>
</html>
_TAIL1;

?>
