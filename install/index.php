<?php
session_start();
foreach (@$_GET as $ind => $val) {
	if (strstr($val, "<") != '' || strstr(htmlspecialchars_decode($val), "<") != '' || strpos($val, "script")  !== false) {
		unset($_GET[$ind]); 
	}
}
include_once "../app/application/language/all.php";
$EnLng = require_once("../app/application/language/en/install.php");
if (!isset($_GET["Lng"]) || !file_exists("../app/application/language/".@$_GET["Lng"]."/install.php")) { $_GET["Lng"] = 'en'; }
if (@$_GET["Lng"] != 'en' ) { $MyLng = require_once("../app/application/language/".$_GET["Lng"]."/install.php"); $MyLng = array_merge($EnLng, $MyLng); } else {$MyLng = $EnLng; }
if (!file_exists('./config-setup.php')) {
	echo '<script>';
	echo 'alert("'.$MyLng["Already_installed"].'");';
	echo 'document.location.href="../index.php";';
	echo '</script>';
	die();
} else {
	require './config-setup.php';
}

?>
<!DOCTYPE html>
<html>
<title>Installation BUGS</title>
<head>
	<link href="../app/assets/css/install.css" media="all" type="text/css" rel="stylesheet">
</head>
<body>
<div class="InstallLogo"></div>
<?php
require "./install.php";
$install = new install();
$database_check = $install->check_connect();
if ($database_check) {
	$resuUSER = $install->Requis("SELECT * FROM users");
	//Puisque la base de données est déjà installée, procédons à l'ouverture d'une session d'usager.
	if ($resuUSER) { 
		if ($install->Combien($resuUSER) == 0 && isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["language"]) && isset($_POST["email"]) && isset($_POST["password"])) {
			$install->create_adminUser();
			unlink ('config-setup.php');
		}
		echo '<script>document.location.href = "../";</script>';
		die();
	}
}
?>
</body>
</html>
