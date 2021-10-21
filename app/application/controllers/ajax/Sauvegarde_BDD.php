<?php
	$retour = "Non";
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }
	require_once "db.php";
	$config = require $prefixe."config.app.php";
	$fichier = $prefixe."temp/database_".date("YmdHis").".sql";
	
	$resuUSER = Requis("SELECT * FROM users WHERE email = '".$_POST["Courriel"]."' ");
	if(Nombre($resuUSER) == 1) {
		require_once($prefixe."app/laravel/hash.php");
		$QuelUSER = Fetche($resuUSER);	
		if (Laravel\Hash::check($_POST["MotPasse"], $QuelUSER["password"]) && $QuelUSER["role_id"] == 4 ) {
			$commande = "mysqldump -u ".$config['database']['username']." --password=".$config['database']['password']." ".$config['database']['database']." > ".$fichier;
			exec($commande);
			$retour = file_exists($fichier) ? $fichier : 'Échec';
		}
	}
	while (substr($retour, 0, 3) == '../') { $retour = substr($retour, 3); }
	echo $retour;
?>
