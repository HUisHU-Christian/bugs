<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Restaurer un BUGS antérieur</title>
</head>
<body>
<?php
var_dump($_GET);
	foreach ($_GET as $ind => $val) {
		if ($ind != 'Lng') { unset($_GET[$ind]); }
	}
echo '<br /><br />';
var_dump($_POST);
echo '<br /><br />';
var_dump($_FILES);

	//Premier contrôle de qualité des variables reçues
	foreach ($_POST as $ind => $val) {
		if (trim($val) == '') {
			unset($_POST[$ind]);
		}
	}

	//Récupération des fichiers texte
	if (isset($_FILES["txte"])) {
		echo '<h2>Base des fichiers texte</h2>';
		echo '<br />';
		$zip = new ZipArchive ();
//		//$zip-> open ($_FILES["txte"]["tmp_name"], ZipArchive :: EXTRACT); 
//		$zip-> open ($_FILES["txte"]["tmp_name"]); 
//		$zip-> copyFile ("../uploads/*"); 
//		$zip-> close ();
		echo 'Fichiers récupérés et décompressés vers uploads<br />';
		
		if (file_exists("../uploads/config.app.php") ) {
			copy("../uploads/config.app.php", "../config.app.php");
			unlink("../uploads/config.app.php");
			echo '<br />';
			echo 'Fichier de configuration restauré<br />';
		}
		echo '<hr />';
	}
		

echo '<br /><br />';

	//Récupération des données
	if (isset($_POST["user"]) && isset($_POST["pswd"]) && isset($_POST['srvr']) && isset($_FILES["bdds"])) {
		$rendu = 0;
		$connect = mysqli_connect($_POST['srvr'], $_POST['user'], $_POST['pswd']);
		if($connect && substr($_FILES["bdds"]["name"], -4) == '.sql') {
			$FILEcnt = file_get_contents($_FILES["bdds"]["tmp_name"]);
			$FILElines = explode(";", $FILEcnt);
			if (count($FILElines) > 1) { 
				echo '<h2>Base de données</h2>';
				echo '<br />';
				foreach ($FILElines as $lgn => $cnt) {
					if (substr($cnt, 0, strlen('--')) == '--') { continue; }
					if (strpos($cnt, "sql_mode") > 0) { continue; }
					if (strpos($cnt, "time_zone") > 0) { continue; }
					if (strpos($cnt, "foreign_key_checks") > 0) { continue; }
					//$cnt = substr($cnt, 0, strpos($cnt, '/*'));
					mysqli_query($connect, $cnt.';');
					echo ++$rendu.' ';
				}
				echo '<br /><br />';
				echo $rendu.' commandes de restaurations accomplies.<br /><br />';
				echo '<br /><br />';
				echo '<hr />';
			}
		}
	}

?>
<div id="div_rebours" style="margin-top: 150px; text-align: center; font-size: 200%; background-color: yellow; color: black; font-weight: bold; width: 100%">11</div>
<script type="text/javascript" >
	var rebours = 10;
	function AfficheTemps() {
		document.getElementById('div_rebours').innerHTML = rebours;
		if (--rebours <= 0) { document.location.href = '..'; }
	}
	var chrono = setInterval(AfficheTemps, 1000);
</script>
</body>
</html>