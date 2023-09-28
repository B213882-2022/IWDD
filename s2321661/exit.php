<!-- Codes are modified from from the scripts provided in course: 'Introduction to Website and Database Design for Drug Discovery' lectured by Dr. Paul Taylor. -->
<?php
session_start();
include 'redir.php';


function clear_everything(){
    $_SESSION = array();  //清空会话内容
    echo "All caches are cleared.";
    if( session_id() != "" || isset($_COOKIE[session_name()])){
        setcookie(session_name(), '', time() - 2592000, '/');
        session_destroy();
    }
}
?>
<html>
    <head>
        <title>Exit and Clear</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class='exit_and_clear'>
            Goodbye <b><?php echo ucwords($_SESSION['forname']); ?></b>! <br>
            You have now exited Complib. <br>
            <?php clear_everything(); ?>
        </div>
    </body>
</html>