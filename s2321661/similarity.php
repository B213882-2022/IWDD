<?php
session_start();
require_once 'login.php';
require 'redir.php';
require 'menuf.php';

if(isset($_GET['smiles'])) {
    $smiles = $_GET['smiles'];
}
else{
    echo '_GET[SMLIES] fails, using structure of ASPIRIN';
    $smiles = 'CC(=O)Oc1ccccc1C(=O)O';  //the structure of 'ASPIRIN'
}
// $smiles = 'CC(=O)Oc1ccccc1C(=O)O';
// $smiles = '[H]OC(=O)C([H])([H])SC1=NN=C(N1C([H])([H])[H])C([H])([H])N([H])[H]';

function find_similarity($smiles, $similarity, $max_number){
    $search_url = "https://www.ebi.ac.uk/chembl/api/data/similarity/".$smiles."/".$similarity;
    $string_contents = file_get_contents($search_url);
    $xml = new DOMDocument();  // create DOMDocument class instance to read XML format data
    $xml->loadXML($string_contents);
    $response = $xml->firstChild;  // the 'response' node in XML
    $molecules = $response->firstChild;  // the 'molecules' node in XML
    $found_number = $molecules->childNodes->length;
    $n = 0;
    if($found_number > 0){  // if similarity results are found
        echo "<b>".$similarity."% Similarity: (found number: ".$found_number.")</b> (click ID and get more info in <b>ChEMBL website</b>)<br>";
        echo "<table border='1' id='table_simi' class='tablesorter'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>ChEMBL ID</th>";
        echo "<th>Similarity</th>";
        echo "<th>SMILES String</th>";
        echo "<th>Structure</th>";
        echo "</tr>";
        echo "</thead>";
        echo '<tbody>';
        foreach($molecules->childNodes as $molecule){
            $n = $n + 1;
            if($n > $max_number){
                echo "<tr><td colspan='4' style='text-align:center'>Too Many. The rest ".($found_number-$max_number)." compounds will not be shown.</td></tr>";
                break;
            }
            else{
                foreach($molecule->childNodes as $node){
                    if($node->nodeName == 'molecule_chembl_id'){
                        // echo $node->nodeValue.':';
                        $chembl_id = $node->nodeValue;
                        $chembl_url = "https://www.ebi.ac.uk/chembl/compound_report_card/".$chembl_id;
                    }
                    if($node->nodeName == 'molecule_structures'){
                        // echo $node->firstChild->nodeValue.':';
                        $chembl_smiles = $node->firstChild->nodeValue;
                    }
                    if($node->nodeName == 'similarity'){
                        // echo $node->nodeValue.'<br>';
                        $chembl_similarity = $node->nodeValue;
                    }
                }
                $convurl = "https://cactus.nci.nih.gov/chemical/structure/".urlencode($chembl_smiles)."/image";  //same as p4
                $convstr = base64_encode(file_get_contents($convurl));
                echo "<tr>";
                echo "<td><a href='$chembl_url' target='_blank'><abbr title='Go to ChEMBL'>$chembl_id</abbr></a></td>";
                printf('<td>%.2f</td>',$chembl_similarity);
                echo "<td>$chembl_smiles</td>";
                echo "<td><img src='data:image/gif;base64,".$convstr."' alt='structure picture of ".$chembl_id."'></td>";
                echo "</tr>";
            }
        }
        echo '</tbody>';
        echo "</table>";
        echo "<br>";
    }
    else{
        echo "<b>".$similarity."% Similarity Results:</b><br>";
        echo 'Found Nothing<br><br>';
    }
}
?>
<html>
    <head>
        <title>Similarity Search</title>
        <link rel="stylesheet" href="style.css">
        <script type="text/javascript" src="jquery-3.3.1.min.js"></script>  <!-- same as p4, user tablesorter function -->
        <script type="text/javascript" src="jquery.tablesorter.min.js"></script>
        <script type="text/javascript">
            $(function() {
                $("#table_simi").tablesorter();
            });
        </script>
    </head>
    <body>
        <span id="top"></span>
        <?php find_similarity($smiles,'50',20) ?>
    </body>
</html>