<?php //this is the login details required
//<!-- Codes are modified from from the scripts provided in course: 'Introduction to Website and Database Design for Drug Discovery' lectured by Dr. Paul Taylor. -->
$db_hostname = 'localhost';
$db_database = 's2321661';
$db_username = 's2321661';
$db_password = 'asdfzxcv';

// connect to database
$db_server = mysql_connect($db_hostname,$db_username,$db_password);
if(!$db_server) die("Unable to connect to database: ".mysql_error());  
mysql_select_db($db_database,$db_server) or die ("Unable to select database: ".mysql_error());  //select database

// set all compound attributes (used in p2 and p3 PHP script)
$dbfs = array("natm","ncar","nnit","noxy","nsul","ncycl","nhdon","nhacc","nrotb","mw","TPSA","XLogP"); // attribute names used in database
$nms = array("n atoms","n carbons","n nitrogens","n oxygens","n sulphurs","n cycles","n H donors","n H acceptors","n rot bonds","mol weight","TPSA","XLogP");  // non-abbreviated attribute names for users

// set all compound attributes (used in p4 and p5 PHP script that helps build up a selection table)
$atom_names = array('atoms','carbons','nitrogens','oxygens','sulphurs','cycles','hydrogen_donors','hydrogen_acceptors','rotatable_bonds','MW','TPSA','XLogP');
$id_names = array('nat','ncr','nnt','nox','nsu','ncy','nhd','nha','nrb','mw','tpsa','xlogp');
$db_col_names = array('natm','ncar','nnit','noxy','nsul','ncycl','nhdon','nhacc','nrotb','mw','TPSA','XLogP');
?>