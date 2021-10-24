<?php
	$compte = 0;
	$retour = "Non";
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }
	require_once "db.php";
	chdir ($prefixe);
	$config = require "config.app.php";
	$nameDte = date("YmdHis");
	$fichier = "temp/database_".$nameDte;
	$sortie = "";
	
	$resuUSER = Requis("SELECT * FROM users WHERE email = '".$_POST["Courriel"]."' ");
	if(Nombre($resuUSER) == 1) {
		require_once("app/laravel/hash.php");
		$QuelUSER = Fetche($resuUSER);	
		if (Laravel\Hash::check($_POST["MotPasse"], $QuelUSER["password"]) && $QuelUSER["role_id"] == 4 ) {
			$commande = "mysqldump -u ".$config['database']['username']." --password=".$config['database']['password']." ".$config['database']['database']." > ".$fichier.".sql";
			exec($commande);

			$zip = new ZipArchive ();
			$zip-> open ($fichier.".zip", ZipArchive :: CREATE); 
			$zip-> addFile ($fichier.".sql"); 
			$zip-> close ();
			$compte = file_exists($fichier.".sql") ? ++$compte : 0;
			$retour = file_exists($fichier.".sql") ? 'Voici votre base de données archivée : <a href="'.$fichier.'.sql">'.$fichier.'.sql</a><br />' : "Échec";
			$compte = file_exists($fichier.".zip") ? ++$compte : 0;
			$retour .= file_exists($fichier.".zip") ? 'Voici votre base de données archivée : <a href="'.$fichier.'.zip">'.$fichier.'.zip</a><br />' : "Échec";
		}
	}
	echo ($compte == 0) ? 'Échec' : $retour;
?>
