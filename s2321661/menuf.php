<!-- Codes are modified from from the scripts provided in course: 'Introduction to Website and Database Design for Drug Discovery' lectured by Dr. Paul Taylor. -->
<html>
    <head>
        <!-- <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"> -->
        <script type="text/javascript" src="jquery-3.3.1.min.js"></script>
        <script>
            function exit(){
                window.location.href='http://mscidwd.bch.ed.ac.uk/s2321661/exit.php';  // this JS function can jump to 'exit.php'
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#hide_or_show").click(function(){
                if ($(".menu_list").css("display")=="none"){
                    $(".menu_list").css("display","block");
                    $(".menu").css("width","210px");
                }else{
                    $(".menu_list").css("display","none");
                    $(".menu").css("width","0%");
                }               
                });
            });
        </script>
    </head>
    <body>
        <div class="menu">
            <button id='hide_or_show'>hide/show</button>
            <ol class='menu_list'>
                <li>
                    <a href="http://mscidwd.bch.ed.ac.uk/s2321661/p1.php"> Select Suppliers </a>
                </li>           
                <li>
                    <a href="http://mscidwd.bch.ed.ac.uk/s2321661/p2.php"> Stats & Histogram </a>
                </li>          
                <li>
                    <a href="http://mscidwd.bch.ed.ac.uk/s2321661/p3.php"> Corr & Heatmap</a>
                </li>           
                <li>
                    <a href="http://mscidwd.bch.ed.ac.uk/s2321661/p4.php"> Search Structure </a>
                </li>
                <li>
                    <a href="http://mscidwd.bch.ed.ac.uk/s2321661/p5.php"> Search Properties </a>
                </li>
                <li>
                    <a href="http://mscidwd.bch.ed.ac.uk/s2321661/p6.php"> All Info by Suppliers </a>
                </li>
                <li id="help_page">
                    <a href="http://mscidwd.bch.ed.ac.uk/s2321661/help.php"> Help Page </a>
                </li>
                <br>
                <br>        
                <li id="back_to_top">
                    <a href="#top">Back to Top</a>
                </li>
            </ol>
        </div>
        <div>
            <button onclick="exit()" class="exit"><b>Exit</b></button>
        </div>
    </body>
</html>
