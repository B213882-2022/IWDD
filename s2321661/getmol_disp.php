<?php
require_once 'login.php';
if(isset($_GET['cid'])) {
     $cid = $_GET['cid'];
     $query = "select molecule from Molecules where cid=$cid";
     $result = mysql_query($query);
     if(!result) die("unable to process query: " . mysql_error());
     $row = mysql_fetch_row($result);
     echo "<p><b> Compound selected is ID $cid </b></p>";
     echo "<pre>",base64_decode($row[0]),"</pre";
     mysql_close($db_server);
}
?>