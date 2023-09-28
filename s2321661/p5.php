<!-- Codes are modified from from the scripts provided in course: 'Introduction to Website and Database Design for Drug Discovery' lectured by Dr. Paul Taylor. -->
<?php
session_start();
require_once 'login.php';
require 'redir.php';
require 'menuf.php';
require 'functions.php';

$suppliers_info = suppliers_info();
$mansel = $suppliers_info['sql'];
$sid = $suppliers_info['all_ids'];
$snm = $suppliers_info['all_names'];

function prop_result($all_suppliers_id, $all_suppliers_name, $mansel, $atom_names, $id_names, $db_col_names,$nms){
    $manid = $all_suppliers_id;
    $manarray = $all_suppliers_name;
    if (isset($_POST['mwmin'])) {
        $first_sele = False;
        $compsel = "select * from Compounds where (";  // record compound selection in SQL query like $mansel
        for($j = 0 ; $j <sizeof($db_col_names) ; ++$j){  // use for loop to generate selection table
            if($j == 0){
                if(($_POST[$id_names[$j].'max'] != "") && ($_POST[$id_names[$j].'min']!="")){
                    $compsel = $compsel."(".$db_col_names[$j]." >".get_post('left_'.$atom_names[$j]).get_post($id_names[$j].'min')." and  ".$db_col_names[$j]." <".get_post('right_'.$atom_names[$j]).get_post($id_names[$j].'max').")";
                    $first_sele = True;
                }
            }
            else{
                if(($_POST[$id_names[$j].'max'] != "") && ($_POST[$id_names[$j].'min']!="")){
                    if($first_sele) $compsel = $compsel." and ";
                    $compsel = $compsel."(".$db_col_names[$j]." >".get_post('left_'.$atom_names[$j]).get_post($id_names[$j].'min')." and  ".$db_col_names[$j]." <".get_post('right_'.$atom_names[$j]).get_post($id_names[$j].'max').")";
                    $first_sele = True;
                }
            }
        }
        $compsel = $compsel.") and ".$mansel;

        if($first_sele){
            echo "<hr>";
            echo "<b>SQL Query:</b><br>";
            echo '"'.$compsel.'"<br>';
            echo "<br>";
            echo "<b>Results:</b> (Click each row's 'Catalogue ID' for <b>3D structure</b>) ";
            $result = mysql_query($compsel);  //get result from database
            if(!$result) die("unable to process query: " . mysql_error());
            $rows = mysql_num_rows($result);
            if($rows > 10000) {
                echo "<br>Too many results ",$rows," Max is 10000\n";
            } 
            else {
                echo '<button type="button" onclick="table_to_csv(\'#result_table\')">Download Table as CSV file</button><br>';
                echo '<table border="1" class="tablesorter" id="result_table">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>CAT Number</th>';
                echo '<th>Manufacturer</th>';
                for($j = 0 ; $j < sizeof($nms) ; ++$j){
                    echo "<th>".$nms[$j]."</th>";
                }
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                for($j = 0 ; $j < $rows ; ++$j){
                    echo "<tr>";
                    $row = mysql_fetch_row($result);
                    // link to jmoltest.php which uses jmol module for 3D structure of compounds
                    printf("<td><a href=jmoltest.php?cid=%s target='_blank'><abbr title='click me view 3D structure'>%s</abbr></a></td> <td>%s</td>", $row[0],$row[11],$manarray[$row[10] - 1]);  
                    printf("<td>%s</td> ", $row[1]);
                    printf("<td>%s</td> ", $row[2]);
                    printf("<td>%s</td> ", $row[3]);
                    printf("<td>%s</td> ", $row[4]);
                    printf("<td>%s</td> ", $row[5]);
                    printf("<td>%s</td> ", $row[6]);
                    printf("<td>%s</td> ", $row[7]);
                    printf("<td>%s</td> ", $row[8]);
                    printf("<td>%s</td> ", $row[9]);
                    printf("<td>%s</td> ", $row[12]);
                    printf("<td>%s</td> ", $row[13]);
                    printf("<td>%s</td> ", $row[14]);
                    echo "</tr>";
                }
                echo '</tbody>';
                echo "</table>";
                echo "<hr>";
                echo "<div class='end'>";
                echo '<a href="#top">Back to Top</a>';
                echo "</div>";
            }
        }
        else {
            echo '<hr>';
            echo "No Full Query Given!<br>";
        }
    } 
}

function search_by_id($all_suppliers_name, $mansel, $nms){
    // search by catalogue id and manufactures
    $snm = $all_suppliers_name;
    if(isset($_POST['cat_id'])){
        $ids = explode(',',$_POST['cat_id']);  // seperate the string by ',' as delimiter
        $sql_id_request = '';
        foreach($ids as $id){
            if($id == $ids[0]){
                $sql_id_request = $sql_id_request.'"'.trim($id).'"';  // use trim() to get gid of blanks at both sides
            }
            else{
                $sql_id_request = $sql_id_request.',"'.trim($id).'"';
            }
        }
        $compsel = "select * from Compounds where (catn IN(".$sql_id_request.")) and ".$mansel;
        $result = mysql_query($compsel);
        if(!result) die("unable to process query: " . mysql_error());
        $rows = mysql_num_rows($result);
        echo '<hr>';
        echo "<b>SQL Query: </b><br>";
        echo '"'.$compsel.'"';
        echo "<br><br>";
        echo "<b>Results: </b> (Click each row's 'Catalogue ID' for <b>3D structure</b>) ";
        if($rows == 0){
            echo "<br>Found nothing! Try other ID.";
        }
        else{
            echo '<button type="button" onclick="table_to_csv(\'#result_table\')">Download Table as CSV file</button><br>';
            echo '<table border="1" class="tablesorter" id="result_table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>CAT Number</th>';
            echo '<th>Manufacturer</th>';
            for($j = 0 ; $j < sizeof($nms) ; ++$j){
                echo "<th>".$nms[$j]."</th>";
            }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            for($j = 0 ; $j < $rows ; ++$j){
                echo "<tr>";
                $row = mysql_fetch_row($result);
                // link to jmoltest.php which uses jmol module for 3D structure of compounds
                printf("<td><a href=jmoltest.php?cid=%s target='_blank'><abbr title='click me view 3D structure'>%s</abbr></a></td> <td>%s</td>", $row[0],$row[11],$snm[$row[10] - 1]);  
                printf("<td>%s</td> ", $row[1]);
                printf("<td>%s</td> ", $row[2]);
                printf("<td>%s</td> ", $row[3]);
                printf("<td>%s</td> ", $row[4]);
                printf("<td>%s</td> ", $row[5]);
                printf("<td>%s</td> ", $row[6]);
                printf("<td>%s</td> ", $row[7]);
                printf("<td>%s</td> ", $row[8]);
                printf("<td>%s</td> ", $row[9]);
                printf("<td>%s</td> ", $row[12]);
                printf("<td>%s</td> ", $row[13]);
                printf("<td>%s</td> ", $row[14]);
                echo "</tr>";
            }
            echo '</tbody>';
            echo "</table>";
            echo "<hr>";
            echo "<div class='end'>";
            echo '<a href="#top">Back to Top</a>';
            echo "</div>";
        }
    }
}

// generate info for JavaScript (keep selections after Form submission), just like p4.php
$range_elements = array();
$edge_elements = array();
for($j = 0 ; $j <sizeof($atom_names) ; ++$j){
    array_push($range_elements, $id_names[$j].'min', $id_names[$j].'max');
    array_push($edge_elements, 'left_'.$atom_names[$j], 'right_'.$atom_names[$j]); 
}
if(isset($_POST['mwmin'])){
    $submit_status = 'form_submitted';
}
else{
    $submit_status = 'form_unsubmitted';
}
?>
<html>
    <head>
        <title>Search Properties</title>
        <!-- <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"> -->
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="theme.blue.min.css">
        <script src='./scripts.js'></script>  <!-- like p4.php, jquery module is loaded for 'tablesorter' function; 'scripts.js' is loaded for keeping selections -->
        <script type="text/javascript" src="jquery-3.3.1.min.js"></script>
        <script type="text/javascript" src="jquery.tablesorter.min.js"></script>
        <script src="jquery.tablesorter.widgets.min.js"></script>
        <script type="text/javascript">
            $(function() {
                // ref:https://mottie.github.io/tablesorter/docs/example-widget-columns.html
                $("#result_table").tablesorter({ 
                    theme:'blue', 
                    sortList : [[0,0]],
                    headerTemplate : '{content}{icon}',
                    widgets : ["zebra", "columns"],
                    widgetOptions : {
                        columns : [ "primary"],
                        columns_thead : true,
                        columns_tfoot : true
                    }
                });
            });
        </script>
    </head>
    <body>
        <span id="top"></span>
        <div class="selected_suppliers">
            <?php show_selected_suppliers() ?>
        </div>
        <div class="select_prop">
            <h3>Retrieve Compounds Properties:</h3>
            <div class='selection_part'>
                <div>
                    <form action="p5.php" method="post">
                        <table class="table_prop">
                            <tr>
                                <th colspan='4' style='text-align:left'>Selection Range:</th>
                                <th colspan='2'>Closed Interval:</th>
                            </tr>
                            <?php select_atoms($atom_names, $id_names) ?>
                        </table>  
                        <br>
                        <input type="submit" value="SUBMIT" />
                    </form>
                    Search compounds properties by 'Range' or 'Catalogue ID'. <br>
                    Infinite range is not supported currently. (e.g. (-∞,N) or (N,+∞)). <br>
                    Please use (0,N) or (N,999999) instead.<br>
                    Multiple catalogue ID search example: SPH1-000-419,SPH1-002-081,SPH1-002-085 (Oai40000)
                </div>
                <div> 
                    <form action="p5.php" method="post">
                        <table class='id_search'>
                            <tr>
                                <th><label for="cat_id">Serach by Catalogue ID:</label></th>
                            </tr>
                            <tr>
                                <td><input type="text" id="cat_id" name='cat_id' placeholder='e.g. SPH1-011-600'></td>
                            </tr>
                            <tr>
                                <td><input type="submit" value="SUBMIT"/></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <script>
            var form = <?php echo json_encode($_POST) ?>;
            var range = <?php echo json_encode($range_elements) ?>;
            var edge = <?php echo json_encode($edge_elements) ?>;
            var form_sub = <?php echo json_encode($submit_status) ?>;
            console.log(form_sub);
            if(form_sub == "form_submitted"){
                keep_range(form, range);
                keep_edge(form, edge);
            }
        </script>
        <div class="result_prop">
            <?php prop_result($sid,$snm,$mansel, $atom_names, $id_names, $db_col_names,$nms) ?>
            <?php search_by_id($snm, $mansel, $nms) ?>
        </div>  
    </body>
</html>
