<?php 

function suppliers_info(){
    // this function can return three value: 
    // 1) $mansel: a string used in SQL query to limit the searching condition to current selected suppliers (e.g. ((ManuID = 4) or (ManuID = 5)))
    // 2) an array that contains all Suppliers(Manufactures) ID;
    // 3) an array that contains all Suppliers full names;
    $query = "select * from Manufacturers";
    $result = mysql_query($query);   // run SQL query
    if(!$result) die("unable to process query: " . mysql_error());  // if query fails
    $rows = mysql_num_rows($result);  // get row numbers from result
    $firstmn = False;  // ensure no 'or' shows before the first condition
    $mansel = "(";
    for($j = 0 ; $j < $rows ; ++$j){  // for loop to traverse all result row by row
        $row = mysql_fetch_row($result);  // get one row of data from result
        $sid[$j] = $row[0];
        $snm[$j] = $row[1];
        if(in_array($row[1],$_SESSION['suppliers'])){
            if($firstmn) $mansel = $mansel." or ";  //让mansel的第一个ID前面不要有or
            $firstmn = True;
            $mansel = $mansel."(ManuID = ".$sid[$j].")";
        }
    }
    $mansel = $mansel.")";
    return array("sql"=>$mansel, "all_ids"=>$sid, "all_names"=>$snm);
}

function show_selected_suppliers(){
    // this function can show selected Suppliers names
    if(isset($_SESSION['suppliers'])){
        echo "<b>Selected Suppliers:</b>";
        echo "<ul class='sele_sup_list'>";
        for($j = 0 ; $j < sizeof($_SESSION['suppliers']) ; ++$j){
            echo '<li>'.$_SESSION['suppliers'][$j].'</li>';
        }
        echo "<ul>";
    }
    else{
        echo "(you need to select suppliers first!)";
    }
}

function get_post($var){
    // this function can translate special characters into SQL-acceptable form
    return mysql_real_escape_string($_POST[$var]);
}

function keep_radio_selection($names, $id_and_value_attri, $name_attri){
    // can keep all selections in HTML input type 'radio' even after the Form is submitted. (This is a PHP method, JS method is used for keeping the selections in 'selection range')
    for($j = 0 ; $j < sizeof($names) ; ++$j) {
        if(!isset($_POST[$name_attri])){
            if($j == 0) {
                printf('<tr><td class="selection">%s</td><td><input type="radio" name="'.$name_attri.'" value="%s" id="%s" checked></td></tr>',$names[$j],$id_and_value_attri[$j],$id_and_value_attri[$j]);  //默认让第一个选项被选上，返回用户选中的value值（radio返回的不是数组是单个值）
            } 
            else {
                printf('<tr><td class="selection">%s</td><td><input type="radio" name="'.$name_attri.'" value="%s" id="%s"></td></tr>',$names[$j],$id_and_value_attri[$j],$id_and_value_attri[$j]);
            }
        }
        else{
            if($id_and_value_attri[$j] == $_POST[$name_attri]){
                printf('<tr><td class="selection">%s</td><td><input type="radio" name="'.$name_attri.'" value="%s" id="%s" checked></td></tr>',$names[$j],$id_and_value_attri[$j],$id_and_value_attri[$j]);
            }
            else {
                printf('<tr><td class="selection">%s</td><td><input type="radio" name="'.$name_attri.'" value="%s" id="%s"></td></tr>',$names[$j],$id_and_value_attri[$j],$id_and_value_attri[$j]);
            }
        }
    }   
}

function select_atoms($atom_names, $id_names){
    // this function is used in HTML table tag
    // it creates a table that accept values for atoms/properties selection in P4 and P5 PHP script
    for($j = 0 ; $j <sizeof($atom_names) ; ++$j){
        echo "<tr>";
        echo "<td>";
        echo "Min ".ucwords($atom_names[$j]);
        echo "</td>";
        echo "<td>";
        echo '<input type="text" name="'.$id_names[$j].'min"  class="input_text" id="'.$id_names[$j].'min"/>';
        echo "</td>";
        echo "<td>";
        echo "Max ".ucwords($atom_names[$j]);
        echo "</td>";
        echo "<td>";
        echo '<input type="text" name="'.$id_names[$j].'max" class="input_text" id="'.$id_names[$j].'max"/>';
        echo "</td>";
        echo "<td>";
        echo '<label for="left_'.$atom_names[$j].'">left</label><input type="checkbox" name="left_'.$atom_names[$j].'" id="left_'.$atom_names[$j].'" value="=" checked >';
        echo "</td>";
        echo "<td>";
        echo '<label for="right_'.$atom_names[$j].'">right</label><input type="checkbox" name="right_'.$atom_names[$j].'" id="right_'.$atom_names[$j].'" value="=" checked >';
        echo "</td>";
        echo "</tr>";
    }
}
?>