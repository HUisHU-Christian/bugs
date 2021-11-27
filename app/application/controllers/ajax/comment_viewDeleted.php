<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>BUGS: review a deleted comment</title>
<style>
	body {
		background: #CDCDCD;
		color: #000000;
		font-size: 14pt;
	}
</style>
</head>
<body onblur="this.close();" >

<?php
$prefixe = "";
while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }
$config = require $prefixe."config.app.php";
$db = mysqli_connect($config['database']['host'], $config['database']['username'], $config['database']['password'], $config['database']['database']);
$resuCOMM = mysqli_query($db, "SELECT * FROM users_activity WHERE id = ".$_GET["Quel"]);

echo '<img src="'.$prefixe.'app/assets/images/layout/logo.jpg" height="55" alt="" align="left" style="padding-right: 10px;" />';

if (mysqli_num_rows($resuCOMM) == 0) {
	echo '<br />';
	echo '<b>Nothing to show</b>';
} else {
	$QuelCOMM = mysqli_fetch_array($resuCOMM);
	echo 'Commmentaire supprimé<br />';
	echo 'le '.$QuelCOMM["created_at"].'<br />';
	echo '<hr />';
	echo '<br clear="all" />';
	echo $QuelCOMM["data"];
}

?>
</body>
</html>