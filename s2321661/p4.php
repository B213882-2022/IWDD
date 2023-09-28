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

function get_compounds($suppliers_query, $all_suppliers_name, $atom_names, $id_names, $db_col_names){
    $mansel = $suppliers_query;
    $snm = $all_suppliers_name;
    if(isset($_POST['natmax'])) {  //judge whether users have entered compound info
        $firstsl = False;  // avoid a "and" before first selection
        $compsel = "select catn,id,ManuID from Compounds where (";  //like 'mansel'
        for($j = 0 ; $j <sizeof($db_col_names) ; ++$j){  // use for loop to generate selection table
            if($j == 0){
                if(($_POST[$id_names[$j].'max'] != "") && ($_POST[$id_names[$j].'min']!="")){
                    $compsel = $compsel."(".$db_col_names[$j]." >".get_post('left_'.$atom_names[$j]).get_post($id_names[$j].'min')." and  ".$db_col_names[$j]." <".get_post('right_'.$atom_names[$j]).get_post($id_names[$j].'max').")";
                    $firstsl = True;
                }
            }
            else{
                if(($_POST[$id_names[$j].'max'] != "") && ($_POST[$id_names[$j].'min']!="")){
                    if($firstsl) $compsel = $compsel." and ";
                    $compsel = $compsel."(".$db_col_names[$j]." >".get_post('left_'.$atom_names[$j]).get_post($id_names[$j].'min')." and  ".$db_col_names[$j]." <".get_post('right_'.$atom_names[$j]).get_post($id_names[$j].'max').")";
                    $firstsl = True;
                }
            }
        }
        $compsel = $compsel.") and ".$mansel;

        if($firstsl) {
            echo "<hr>";
            echo "<b>SQL Query: </b><br>";
            echo '"'.$compsel.'"';
            echo "<br><br>";
            echo "<b>Results:</b> (Click each row's 'Catalogue ID' for <b>3D structure</b> or 'SMILES string' for 50% <b>Similarity Search</b> in ChEMBL) ";
            $result = mysql_query($compsel);  //get result from SQL query
            if(!result) die("unable to process query: " . mysql_error());
            $rows = mysql_num_rows($result);
            if($rows > 100) {
                echo '<br>Total number of results: '.$rows.'<br>';
                echo "Too many!! (Max is 100)";
            }
            elseif($rows == 0){
                echo "<br>Found nothing! Try other conditions.";
            } 
            else{
                echo '<button type="button" onclick="table_to_csv(\'#result_table\')">Download Table as CSV file</button><br>';
                echo "<table border='1' id='result_table' class='tablesorter'>";  // table header
                echo "<thead>";
                echo "<tr>";
                echo "<th>Catalogue ID</th>";
                echo "<th>manufacturer</th>";
                echo "<th>SMILES String</th>";
                echo "<th>Structure</th>";
                echo "</tr>";
                echo '</thead>';
                echo '<tbody>';
                for($j = 0 ; $j < $rows ; ++$j){  // use for loop to generate table data
                    $row = mysql_fetch_row($result);
                    $cid = $row[1];  // Suppliers ID in database
                    $compselsmi = "select smiles from Smiles where cid = ".$cid;
                    $resultsmi = mysql_query($compselsmi);
                    $smilesrow = mysql_fetch_row($resultsmi);
                    $convurl = "https://cactus.nci.nih.gov/chemical/structure/".urlencode($smilesrow[0])."/image";  // urlencode turns SMILES string into url format
                    $convstr = base64_encode(file_get_contents($convurl));  // get picture from other website and encode the picture in base64 format
                    $send_smiles = "similarity.php?smiles=".$smilesrow[0];
                    echo "<tr>";
                    echo "<td><a href='jmoltest.php?cid=$cid' target='_blank'><abbr title='click me view 3D structure'>".$row[0]."</abbr></a></td>";  // 'abbr' tag serves as a help to give users hint of what will happen after clicking the link
                    echo "<td>".$snm[$row[2]-1]."</td>";
                    echo "<td><a href='$send_smiles' target='_blank' class='simi_search'><abbr title='click me to do similarity (50%) search'>".$smilesrow[0]."</abbr></a></td>";
                    echo "<td><img src='data:image/gif;base64,".$convstr."' alt='structure picture ".$j."'></td>";  // decode base64 and generate a picture in table
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
}   //no compound record in SESSION

function search_by_id($all_suppliers_name, $mansel){
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
        $compsel = "select catn,id,ManuID from Compounds where (catn IN(".$sql_id_request.")) and ".$mansel;
        $result = mysql_query($compsel);
        if(!result) die("unable to process query: " . mysql_error());
        $rows = mysql_num_rows($result);
        echo '<hr>';
        echo "<b>SQL Query: </b><br>";
        echo '"'.$compsel.'"';
        echo "<br><br>";
        echo "<b>Results:</b> (click each row's 'Catalogue ID' for <b>3D structure</b> or 'SMILES string' for 50% <b>Similarity Search</b> in ChEMBL) ";
        if($rows == 0){
            echo "<br>Found nothing! Try other ID.";
        }
        else{
            echo '<button type="button" onclick="table_to_csv(\'#result_table\')">Download Table as CSV file</button><br>';
            echo "<table border='1' id='result_table' class='tablesorter'>";  // table header
            echo "<thead>";
            echo "<tr>";
            echo "<th>Catalogue ID</th>";
            echo "<th>manufacturer</th>";
            echo "<th>SMILES String</th>";
            echo "<th>Structure</th>";
            echo "</tr>";
            echo '</thead>';
            echo '<tbody>';
            for($j = 0 ; $j < $rows ; ++$j){  // use for loop to generate table data
                $row = mysql_fetch_row($result);
                $cid = $row[1];  // Suppliers ID in database
                $compselsmi = "select smiles from Smiles where cid = ".$cid;
                $resultsmi = mysql_query($compselsmi);
                $smilesrow = mysql_fetch_row($resultsmi);
                $convurl = "https://cactus.nci.nih.gov/chemical/structure/".urlencode($smilesrow[0])."/image";  // urlencode turns SMILES string into url format
                $convstr = base64_encode(file_get_contents($convurl));  // get picture from other website and encode the picture in base64 format
                $send_smiles = "similarity.php?smiles=".$smilesrow[0];
                echo "<tr>";
                echo "<td><a href='jmoltest.php?cid=$cid' target='_blank'><abbr title='click me view 3D structure'>".$row[0]."</abbr></a></td>";  // 'abbr' tag serves as a help to give users hint of what will happen after clicking the link
                echo "<td>".$snm[$row[2]-1]."</td>";
                echo "<td><a href='$send_smiles' target='_blank' class='simi_search'><abbr title='click me to do similarity (50%) search'>".$smilesrow[0]."</abbr></a></td>";
                echo "<td><img src='data:image/gif;base64,".$convstr."' alt='structure picture ".$j."'></td>";  // decode base64 and generate a picture in table
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

// generate info for JavaScript (keep selections after Form submission)
$range_elements = array();  // record range selections
$edge_elements = array();  // record interval edge selections
for($j = 0 ; $j <sizeof($atom_names) ; ++$j){
    array_push($range_elements, $id_names[$j].'min', $id_names[$j].'max');  //array_push can add element into array
    array_push($edge_elements, 'left_'.$atom_names[$j], 'right_'.$atom_names[$j]); 
}
if(isset($_POST['natmin'])){
    $submit_status = 'form_submitted';
}
else{
    $submit_status = 'form_unsubmitted';
}

?>
<html>
    <head>
        <title>Search Structure</title>
        <!-- <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"> -->
        <link rel="stylesheet" href="style.css">
        <script src='./scripts.js'></script>  <!-- include some javascript functions defined in 'scripts.js' -->
        <script type="text/javascript" src="jquery-3.3.1.min.js"></script>  <!-- use jquery module (javascript) for 'tablesorted' function that can sort table -->
        <script type="text/javascript" src="jquery.tablesorter.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $("#result_table").tablesorter();  // decide which table has the 'tablesorter' function
            });
        </script>
    </head>
    <body>
        <span id="top"></span>
        <div class="selected_suppliers">
            <?php show_selected_suppliers() ?>
        </div>
        <div class="conditions_compounds">
            <h3>Retrieve Compounds Structure:</h3>
            <div class='selection_part'>
                <div>
                    <form action="p4.php" method="post" id="p2_form">
                        <table class="table_compounds">
                            <tr>
                                <th colspan='4' style='text-align:left'>Selection Range:</th>
                                <th colspan='2'>Closed Interval:</th>
                            </tr>
                            <?php select_atoms($atom_names, $id_names) ?>
                        </table>
                        <br>
                        <input type="submit" value="SUBMIT"/>
                    </form>
                    Search compounds' structures by 'Range' or 'Catalogue ID'. <br>
                    Infinite range is not supported currently. (e.g. (-∞,N) or (N,+∞)). <br>
                    Please use (0,N) or (N,999999) instead. <br>
                    Multiple catalogue ID search example: SPH1-000-419,SPH1-002-081,SPH1-002-085 (Oai40000)
                </div>
                <div> 
                    <form action="p4.php" method="post">
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

        <!-- run javascript to keep selections after Form submission -->
        <script> 
            var form = <?php echo json_encode($_POST) ?>;  // get variable from PHP into variable in JS
            var range = <?php echo json_encode($range_elements) ?>;
            var edge = <?php echo json_encode($edge_elements) ?>;
            var form_sub = <?php echo json_encode($submit_status) ?>;
            //console.log(form_sub);
            if(form_sub == "form_submitted"){
                keep_range(form, range); //
                keep_edge(form, edge);
            }
        </script>
        <div class="results_compounds">
            <?php get_compounds($mansel,$snm, $atom_names, $id_names, $db_col_names) ?>
            <?php search_by_id($snm, $mansel) ?>
        </div>
    </body>
</html>