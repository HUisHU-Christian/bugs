<?php
	$compte = 0;
	$retour = "Non";
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }
	require_once "db.php";
	$config = require $prefixe."config.app.php";
	$nameDte = date("YmdHis");
	chdir ($prefixe);
	$fichier = "temp/database_".$nameDte;
	$sortie = "";
	
	$resuUSER = Requis("SELECT * FROM users WHERE email = '".$_POST["Courriel"]."' ");
	if(Nombre($resuUSER) == 1) {
		require_once("app/laravel/hash.php");
		$QuelUSER = Fetche($resuUSER);	
		if (Laravel\Hash::check($_POST["MotPasse"], $QuelUSER["password"]) && $QuelUSER["role_id"] == 4 ) {
				$commande = "mysqldump -u ".$config['database']['username']." --password=".$config['database']['password']." ".$config['database']['database']." > ".$fichier.".sql";
				exec($commande);
				$compte = $compte + 1;
			$zip = new ZipArchive ();
			$zip-> open ($fichier.".zip", ZipArchive :: CREATE); 
			$zip-> addFile ($fichier.".sql"); 
			$zip-> close ();
			$compte = file_exists($fichier.".zip") ? $compte : 0;
			$retour = 'Voici votre base de données archivée : <a href="'.$fichier.'.zip">'.$fichier.'.zip</a>';
		}
	}
	while (substr($retour, 0, 3) == '../') { $retour = substr($retour, 3); }
	echo ($compte == 0) ? 'Échec' : $retour;
?>
