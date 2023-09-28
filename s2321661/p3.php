<?php
session_start();
require_once 'login.php';
require 'redir.php';
require 'menuf.php';
require 'functions.php';

$suppliers_info = suppliers_info();
$mansel = $suppliers_info['sql'];

if(isset($_POST['corr'])){   //if property for correlation are selected by Form, pass the info to SESSION
    $_SESSION['corr'] = $_POST['corr'];  // what properties are selected will be stored in SESSION['corr']
}
elseif(!isset($_SESSION['corr'])){  // select all properties by default
    $_SESSION['corr'] = $nms;
}

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
    $compsel = '"'.$compsel.'"';
}
// echo $compsel;

function corr_selection($prop_selected, $prop_list){

    $prop_num = sizeof($prop_list);
    for($j = 0 ; $j < $prop_num ; ++$j){
        echo "<tr>";
        echo "<td >".$prop_list[$j]."</td>";
        if(in_array($prop_list[$j],$prop_selected)){
            echo'<td><input type="checkbox" name="corr[]" value="'.$prop_list[$j].'" checked/></td>';
        }
        else{
            echo'<td><input type="checkbox" name="corr[]" value="'.$prop_list[$j].'"/></td>';
        }
        echo "</tr>";
    }
}

function corr_heatmap($prop_selected, $nms, $dbfs,$compsel){
    $prop_sele_str = '';  // record the selected properties; e.g. "natm,ncar,nnit"
    $col_names = '';  // record the names for users; e.g. "n atoms,n carbons,n nitrogens"
    if(isset($_POST['corr']) && $_POST['submit_status'] == 'yes'){
        $first_sele = false;
        for($j = 0 ; $j < sizeof($dbfs) ; ++$j){
            if(in_array($nms[$j],$prop_selected) && $first_sele == false){
                $prop_sele_str = $prop_sele_str.$dbfs[$j];
                $col_names = $col_names.$nms[$j];
                $first_sele = true;
            }
            elseif(in_array($nms[$j],$prop_selected) && $first_sele == true){
                $prop_sele_str = $prop_sele_str.','.$dbfs[$j];
                $col_names = $col_names.','.$nms[$j];
            }
        }
        $prop_sele_str = '"'.$prop_sele_str.'"';
        $col_names = '"'.$col_names.'"';
        echo '<hr>';
        echo '<div class="result_corr">';
        echo '<b>SQL Query:</b><br>';
        echo 'select '.$prop_sele_str.' from Compounds where '.$compsel.'<br>';
        $py_script = './corr.py '.$prop_sele_str.' '.$compsel.' '.$col_names;
        $py_output = base64_encode(shell_exec($py_script));
        echo "<br><b>Heatmap:</b><br>";
        echo "<img src='data:image/png;base64,$py_output' alt='correlation heatmap'>";
        echo '</div>';
    }
    elseif($_POST['submit_status'] == 'yes'){
        echo '<hr>';
        echo '<div class="result_corr">';
        echo "Please select at least one propterty (in 'Corr across') for correlation!";
        echo '</div>';
    }
}

// this part is for keeping the values entered by users even after a submission
$range_elements = array();  // record range selections
$edge_elements = array();  // record interval edge selections
for($j = 0 ; $j <sizeof($atom_names) ; ++$j){
    array_push($range_elements, $id_names[$j].'min', $id_names[$j].'max');  //array_push can add element into array
    array_push($edge_elements, 'left_'.$atom_names[$j], 'right_'.$atom_names[$j]); 
}
if(isset($_POST['corr'])){
    $submit_status = 'form_submitted';
}
else{
    $submit_status = 'form_unsubmitted';
}
?>
<html>
    <head>
        <title>Correlation</title>
        <link rel="stylesheet" href="style.css">
        <script src='./scripts.js'></script>
    </head>
    <body>
        <span id="top"></span>
        <div class="selected_suppliers">
            <?php show_selected_suppliers() ?>
        </div>
        <div class="select_corr">
            <h3>Correlations(corr) Heatmap:</h3>
            <form action="p3.php" method="post">
                <div class='selection_part'>
                    <table  class="table_corr">
                        <tr>
                            <th colspan='4' style='text-align:left'>Selection Range:</th>
                            <th colspan='2'>Closed Interval:</th>
                        </tr>
                        <?php select_atoms($atom_names, $id_names) ?>
                    </table>
                    <table class='checkbox_corr'>
                        <tr>
                            <th colspan='2'>Corr across:</th>
                        </tr>
                        <?php corr_selection($_SESSION['corr'],$nms) ?>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <br>
                <input type="text" name='submit_status' value='yes' id='check_submit'>  <!-- check submit status -->
                <input type="submit" value="SUBMIT" id="submit_button"/>
            </form>
            Select properties (and range) in 'Corr across' of compounds to show their correlations.<br>
            Simply selecting all properties is highly recommended! <br>
            Infinite range is not supported currently. (e.g. (-∞,N) or (N,+∞)). <br>
            Please use (0,N) or (N,999999) instead. 
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
        </div>
        <div>
            <?php corr_heatmap($_SESSION['corr'], $nms, $dbfs, $compsel) ?>
        </div>
    </body>
</html>