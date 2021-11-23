<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>BUGS: review a deleted comment</title>
</head>
<body bgcolor="#CCCCCC" onblur="this.close();" >

<?php
//Ici on appelait le fichier db.php
////La version temporaire (ci-bas) est faite d'une page HTML complète.
////Ne faudrait-il pas changer cela et en faire une DIV dans la page locale ? 
////Quoi qu'il en soit, la présente page est libérée du fichier db.php qui est appelé à disparaître
$prefixe = "";
while (!file_exists($prefixe."config.app.php")) {
	$prefixe .= "../";
}
$config = require $prefixe."config.app.php";
$db = mysqli_connect($config['database']['host'], $config['database']['username'], $config['database']['password'], $config['database']['database']);
$resuCOMM = mysqli_query($db, "SELECT * FROM users_activity WHERE id = ".$_GET["Quel"]);

echo '<img src="'.$prefixe.'app/assets/images/layout/logo.jpg" height="55" alt="" align="left" style="padding-right: 10px;" />';

if (mysqli_num_rows($resuCOMM) == 0) {
	echo '<br />';
	echo '<b>Nothing to show</b>';
} else {
	$QuelCOMM = mysqli_fetch_array($resuCOMM);
	echo 'Commment suppressed<br />';
	echo 'on '.$QuelCOMM["created_at"].'<br />';
	echo '<hr />';
	echo '<br clear="all" />';
	echo $QuelCOMM["data"];
}

?>
</body>
</html>