<!-- Codes are modified from from the scripts provided in course: 'Introduction to Website and Database Design for Drug Discovery' lectured by Dr. Paul Taylor. -->
<?php
session_start();
require_once 'login.php';
require 'redir.php';
require 'menuf.php';
require 'functions.php';

$suppliers_info = suppliers_info();
$manid = $suppliers_info['all_ids'];
$manarray = $suppliers_info['all_names'];
$rowid = array(11,1,2,3,4,5,6,7,8,9,12,13,14);
$nms = array("Catalogue ID","n atoms","n carbons","n nitrogens","n oxygens","n sulphurs","n cycles","n H donors","n H acceptors","n rot bonds","mol weight","TPSA","XLogP");

function prop_by_sup_result($nms,$rowid){
    // show all compounds information of one supplier
    if(isset($_POST['tgval'])){
        $chosen = $_POST['tgval'];
        $query = "select * from Compounds where ManuID = ".$chosen;
        $result = mysql_query($query);
        if(!$result) die("unable to process query: " . mysql_error());
        $resrows = mysql_num_rows($result);
        echo "<hr>";
        // echo "<b>SQL Query:</b><br>";
        // echo '"'.$query.'"<br>';
        // echo "<br>";
        echo "<b>Results: </b> (Click each row's 'Catalogue ID' for <b>3D structure</b>)<br> ";
        echo '<table id="myTable" class="display nowrap" style="width:100%"><thead><tr>';
        for($k = 0 ; $k < sizeof($nms) ; ++$k) {
            echo "<th>".$nms[$k]."</th>";
        }
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        for($j = 0 ; $j < $resrows ; ++$j){
            $row = mysql_fetch_row($result);
            echo "<tr>";
            for($k = 0 ; $k < sizeof($nms) ; ++$k) {
                if($k == 0){
                    printf("<td><a href=jmoltest.php?cid=%s target='_blank'><abbr title='click me view 3D structure'>%s</abbr></a></td>",$row[0],$row[$rowid[$k]]);
                }
                else{
                    echo "<td>".$row[$rowid[$k]]."</td>";
                }
            }
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }
}
?>
<html>
    <head>
        <title>All info</title>
        <!-- <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"> -->
        <link rel="stylesheet" href="style.css">  <!-- jquery module is loaded for 'tablesorter' function -->
        <link rel="stylesheet" href="theme.blue.min.css">
        <!-- use online url of DataTable -->
        <link href="https://cdn.datatables.net/v/dt/dt-1.13.4/datatables.min.css" rel="stylesheet"/>
        <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet"/>
        <link href="https://cdn.datatables.net/searchbuilder/1.4.1/css/searchBuilder.dataTables.min.css" rel="stylesheet"/>
        <link href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css" rel="stylesheet"/>
        <script src='./scripts.js'></script>
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <!-- use online url of DataTable -->
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/colreorder/1.6.2/js/dataTables.colReorder.min.js"></script>
        <script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
        <script src="https://cdn.datatables.net/searchbuilder/1.4.1/js/dataTables.searchBuilder.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#myTable').DataTable( {
                    // ref: https://datatables.net/extensions/buttons/examples/initialisation/export.html
                    scrollX: true,
                    scrollCollapse: true,
                    dom: 'Bfrtip',
                    lengthMenu: [
                        [ 10, 25, 50, 100, -1 ],
                        [ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
                    ],
                    buttons: [
                        'pageLength',{extend:'colvis', text:'Select Column'}, {extend:'csv',text:'Download as CSV'},'searchBuilder'
                    ],
                    fixedHeader: true,
                    colReorder: true,
                } );
            } );
        </script>
    </head>
    <body class="body_prop_by_sup">
        <span id="top"></span>
        <div class="select_prop_by_sup">
            <h3>All Info by Suppliers:</h3>
            <form action="p6.php" method="post">
                <table>
                    <?php keep_radio_selection($manarray,$manid,'tgval') ?>
                </table>
                <br>
                <input type="submit" value="SUBMIT" />
            </form>
            Check all properties of compounds from a selected suppliers.
        </div>
        <div class="result_prop_by_sup">
            <?php prop_by_sup_result($nms,$rowid) ?>
        </div>
        
    </body>
</html>