<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>BUGS: review a deleted comment</title>
</head>
<body>
<?php
include_once "db.php";
$resuCOMM = (Requis("SELECT * FROM users_activity WHERE id = ".$_GET["Quel"]));

if (Nombre($resuCOMM) == 0) {
	echo 'Nothing to show';
} else {
	$QuelCOMM = Fetche($resuCOMM);
	echo $QuelCOMM["data"];
}

?>
</body>
</html>