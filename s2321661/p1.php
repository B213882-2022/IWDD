<!-- Codes are modified from from the scripts provided in course: 'Introduction to Website and Database Design for Drug Discovery' lectured by Dr. Paul Taylor. -->
<?php
session_start();
require_once 'login.php';
require 'redir.php';  // ensure users cannot directly access this PHP by url
require 'menuf.php';
require 'functions.php';

$suppliers_info = suppliers_info();  // functions defined in "functions.php"
$snm = $suppliers_info['all_names'];  // get all suppliers name
$rows = sizeof($snm);  // get the number of suppliers


if(isset($_POST['supplier'])){   //if suppliers have been submitted by Form, pass the info to SESSION
    $_SESSION['suppliers'] = $_POST['supplier'];  // what suppliers are selected will be stored in SESSION['suppliers']
}
elseif(!isset($_SESSION['suppliers'])){  // select all suppliers by default
    $_SESSION['suppliers'] = $snm;
}


function suppliers_form($suppliers,$suppliers_num,$suppliers_list){
    // show suppliers selection form
    for($j = 0 ; $j < $suppliers_num ; ++$j){
        echo "<tr>";
        echo "<td >".$suppliers_list[$j]."</td>";
        if(in_array($suppliers_list[$j],$suppliers)){
            echo'<td><input type="checkbox" name="supplier[]" value="'.$suppliers_list[$j].'" checked/></td>';
        }
        else{
            echo'<td><input type="checkbox" name="supplier[]" value="'.$suppliers_list[$j].'"/></td>'; //an array called "suppliers" will be stored in Form, recording selections for suppliers
        }
        echo "</tr>";
    }
}

function current_suppliers($suppliers,$suppliers_list){
    // show current selected suppliers
    for($j = 0 ; $j < count($suppliers) ; ++$j){
        if(in_array($suppliers[$j],$suppliers_list)) 
        {
          echo "<li>".$suppliers[$j]."</li>" ;
        }
    }
}
?>
<html>
    <head>
        <title>Select Suppliers</title>
        <!-- <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"> -->
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <span id="top"></span>
        <div class="conditions_suppliers">
            <div>
                <h3>Select Suppliers:</h3>
                <form action="p1.php" method="post">
                    <table>
                        <?php suppliers_form($_SESSION['suppliers'],$rows,$snm) ?>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <input type="submit" value="SUBMIT"/>
                </form>
            </div>
            <div>
                <h3>Current Suppliers: </h3>
                <ul style="margin-top:0px;">  <!-- little inline style -->  
                    <?php current_suppliers($_SESSION['suppliers'],$snm) ?>
                </ul>
            </div>
        </div>
    </body>
</html>
