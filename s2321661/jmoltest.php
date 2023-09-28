<?php
session_start();
require_once 'login.php';
require 'redir.php';
require 'menuf.php';

if(isset($_GET['cid'])) {
  	$cid = $_GET['cid'];
}
else{
	$cid = 1;
}

echo <<<_content
<html>
	<head>
		<link href="style.css" rel="stylesheet" type="text/css" />
		<title>3D_Molecule(Jmol)</title>
		<script  type="text/javascript" src="jsmol/JSmol.min.js"></script>
		<script> 
			$(document).ready(function(){  
				var Info = {
						width: "100%",
						height: "100%",
						debug: false,
						j2sPath: "jsmol/j2s",
						color: "0xC0C0C0",
						disableJ2SLoadMonitor: true,
						disableInitialConsole: true,
						addSelectionOptions: false,
						readyFunction: null,
						src: "http://mscidwd.bch.ed.ac.uk/s2321661/getmol_resp.php?cid=$cid"  
					}
				$("#mydiv").html(Jmol.getAppletHtml("jmolApplet0",Info))
			});
		</script>
	</head>
	<body>
		<b>3D-Molecule:</b><br>
		<span id="mydiv"></span>
		<button onclick="Jmol.script(jmolApplet0, 'spin on')">spin on</button>
		<button onclick="Jmol.script(jmolApplet0, 'spin off')">spin off</button>
	</body>
</html>
_content;
?>





