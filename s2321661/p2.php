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


if(isset($_POST['natmax'])){
    $firstsl = False;  // avoid a "and" before first selection
    $compsel = "(";
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
    if($compsel != '('){  // in case nothing is selected in 'selection range'
        $compsel = $compsel.") and ".$mansel;  // comsel=composed selections, can be used in SQL after "where"
    }
    else{
        $compsel = $mansel;
    }
}
// echo $compsel;

function stats_result($stats_names, $database_functions,$compsel){
    $nms = $stats_names;
    $dbfs = $database_functions;
    if(isset($_POST['tgval'])){  // if the Form is submitted
        for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {
            if($dbfs[$j] == $_POST['tgval']){
                echo "<hr>";
                echo '<div class="stats">';         
                printf('<b>Statistics for "%s" (%s):</b><br>',$nms[$j],$_POST['tgval']);  
                echo "<hr>";
                $py_script = './stats.py "'.$_POST['tgval'].'" "'.$compsel.'"';  // run python script "stats.py"
                // echo '<b>Script Run:</b><br>';
                // echo '['.$py_script.']<br><br>';
                system($py_script);
                $query = sprintf("select AVG(%s), STD(%s) from Compounds where %s",$_POST['tgval'],$_POST['tgval'], $compsel);  //calculate AVG and STD in SQL Query
                // echo '<b>SQL Query: </b><br>';
                // echo '"'.$query.'"';
                // echo "<br><br>";
                $result = mysql_query($query);
                if(!$result) die("unable to process query: " . mysql_error());
                $row = mysql_fetch_row($result);
                printf("<b>Avg:</b> %f <br> <b>Std:</b> %f <br>",$row[0],$row[1]);
                echo "</div>";
            }
        } 
    }
}

function hist_result($stats_names, $database_functions, $compsel){
    // show histogram
    $nms = $stats_names;
    $dbfs = $database_functions;
    if(isset($_POST['tgval'])) {
        for($j = 0 ; $j <sizeof($dbfs) ; ++$j) {
            if($dbfs[$j] == $_POST['tgval']){
                $mansel = suppliers_info()['sql'];
                $comtodo = './histog.py "'.$_POST['tgval'].'" "'.$nms[$j].'" "'.$compsel.'"';  //run python script "histog.py"，e.g. $comtodo = ./histog.py ncycl "n cycles" "((ManuID = 2) or (ManuID = 4))"
                echo "<br>";
                echo '<b>Script Run:</b><br>';
                echo '['.$comtodo.']<br><br>';
                $output = base64_encode(shell_exec($comtodo));
                echo "<b>Histogram:</b><br>";
                echo "<img src='data:image/png;base64,$output' alt='result histogram'>"; 
            }
        }
    }
}

// this part is for keeping the values entered by users even after a submission
$range_elements = array();  // record range selections
$edge_elements = array();  // record interval edge selections
for($j = 0 ; $j <sizeof($atom_names) ; ++$j){
    array_push($range_elements, $id_names[$j].'min', $id_names[$j].'max');  //array_push can add element into array
    array_push($edge_elements, 'left_'.$atom_names[$j], 'right_'.$atom_names[$j]); 
}
if(isset($_POST['tgval'])){
    $submit_status = 'form_submitted';
}
else{
    $submit_status = 'form_unsubmitted';
}
?>
<html>
    <head>
        <title>Statistics</title>
        <!-- <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"> -->
        <link rel="stylesheet" href="style.css">
        <script src='./scripts.js'></script>
    </head>
        <span id="top"></span>
        <div class="selected_suppliers">
            <?php show_selected_suppliers() ?>
        </div>
        <div class="select_stats">
            <h3>Check Distribution:</h3>  
            <form action="p2.php" method="post">
                <div class='selection_part'>
                    <table class="table_stats">
                        <tr>
                            <th colspan='4' style='text-align:left'>Selection Range:</th>
                            <th colspan='2'>Closed Interval:</th>
                        </tr>
                        <?php select_atoms($atom_names, $id_names) ?>
                    </table>
                    <table class='radio_stats'>
                        <tr>
                            <th colspan='2'>Stats for:</th>
                        </tr>
                        <?php keep_radio_selection($nms,$dbfs,'tgval') ?>
                    </table>
                </div>
                <br>
                <input type="submit" value="SUBMIT" id="submit_button"/>
            </form>
            Select the range and properties (in 'Stats for') of compounds to show the distribution.<br>
            Infinite range is not supported currently. (e.g. (-∞,N) or (N,+∞)). <br>
            Please use (0,N) or (N,999999) instead. 
        </div>
        <script> 
            var form = <?php echo json_encode($_POST) ?>;  // get variable from PHP into variable in JS
            var range = <?php echo json_encode($range_elements) ?>;
            var edge = <?php echo json_encode($edge_elements) ?>;
            var form_sub = <?php echo json_encode($submit_status) ?>;
            console.log(form_sub);
            if(form_sub == "form_submitted"){
                keep_range(form, range); //
                keep_edge(form, edge);
            }
        </script>
        <div class="result_stats">
            <?php stats_result($nms,$dbfs,$compsel) ?>
            <div class="hist">
                <?php hist_result($nms,$dbfs,$compsel) ?>
            </div>
        </div>
    </body>
</html>
