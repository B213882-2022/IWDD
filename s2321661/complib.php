<!-- Codes are modified from from the scripts provided in course: 'Introduction to Website and Database Design for Drug Discovery' lectured by Dr. Paul Taylor. -->
<?php
session_start(); //start 'SESSION' to record information used between different scripts
require_once 'login.php';  //run login.php

// test connection with database. If fail, report error.
$query = "select * from Manufacturers";
$result = mysql_query($query);  // run SQL script
if(!$result) die("unable to process query: " . mysql_error());
mysql_close($db_server);  //break connection
?>
<html>
    <head>
        <title>User Name</title>  <!-- set title -->
        <!-- <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"> -->
        <link rel="stylesheet" href="style.css">  <!-- use stylesheet in "style.css" -->
        <script>
            function validate(form) {  //  run JavaScript, to ensure Forname and Surname are given   
                fail = ""
                if(form.fn.value =="") fail = "Must Give Forname "  //JS can get value from Form object. 
                if(fail =="") return true 
                else {alert(fail); return false}  // 'return false' means Form cannot be dilivered to its 'action'
            }
        </script>
    </head>
    <body class='complib_body'>
        <span id="top"></span>
        <p><h2 id="table_title_name">Compound Retrieval Website</h2></p>
        <div class="table_name">
            <form action="index.php" method="post" id="form_name" onSubmit="return validate(this)">   <!-- if onsubmit="return false", Form will not be passed -->
                <table>
                    <tr>
                        <td class='row_head'>First Name:</td>
                        <td>
                            <input type="text" name="fn" placeholder="forname"/>
                        </td>
                    </tr>
                    <tr>
                        <td class='row_head'>Second Name:</td>
                        <td>
                            <input type="text" name="sn" placeholder="surname"/>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="submit" value="SUBMIT" id="button_name" />
            </form>
        </div>
    </body>
</html>