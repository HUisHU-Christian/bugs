<?php
	$retour = "Non";
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }
	require_once "db.php";
	$config = require $prefixe."config.app.php";
	$namedir = date("YmdHis");
	chdir ($prefixe);
	$fichier = "temp/database_".$namedir.".sql";
	$sortie = "";
	
	$resuUSER = Requis("SELECT * FROM users WHERE email = '".$_POST["Courriel"]."' ");
	if(Nombre($resuUSER) == 1) {
		require_once("app/laravel/hash.php");
		$QuelUSER = Fetche($resuUSER);	
		if (Laravel\Hash::check($_POST["MotPasse"], $QuelUSER["password"]) && $QuelUSER["role_id"] == 4 ) {
//			if (strtolower(substr(php_uname('s'), 0, 3)) != 'win') {
			if (1==1) {
				$commande = "mysqldump -u ".$config['database']['username']." --password=".$config['database']['password']." ".$config['database']['database']." > ".$fichier;
				exec($commande);
			} else {
				$sortie .= "-- BUGS 
					SET NAMES utf8;
					SET time_zone = '+00:00';
					SET foreign_key_checks = 0;
					SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
	
				CREATE DATABASE `".$config['database']['database']."` /*!40100 DEFAULT CHARACTER SET utf8 */;
				USE `".$config['database']['database']."`;
				";
	
				$resuTABL = Requis("SHOW TABLES FROM ".$config['database']['database']);
				while ($QuelTABL = Fetche($resuTABL)) {
					////Récupération de la structure
					$key = "";
					$tab = $QuelTABL["Tables_in_tickets"];
					$resuCOLS = Requis("SHOW COLUMNS FROM ".$tab);
					$sortie .= "CREATE TABLE IF NOT EXISTS ".$tab." (";
					while ($QuelCOLS = Fetche($resuCOLS)) {
						if ($QuelCOLS["Key"] == 'PRI') { $key = $QuelCOLS["Field"]; }
						 $sortie .= "`".$QuelCOLS["Field"].'` '.$QuelCOLS["Type"].' '.(($QuelCOLS["Null"]=='NO') ? 'NOT NULL' : 'DEFAULT NULL').''.((trim($QuelCOLS["Extra"]) !='') ? ' ': '').$QuelCOLS["Extra"].', ';
					}
					$sortie .= "PRIMARY KEY (`".$key."`)";
					$sortie .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;
					";
					$sortie .= "
					";
					
					////Récupération des données
					$compte = 0;
					$lien = "";
					$sortie .= "INSERT INTO `".$tab."` VALUES";
					$resuVALS = Requis("SELECT * FROM ".$tab." ");
					while ($QuelVALS = Fetche($resuVALS)) {
						$sortie .= $lien." (";
						$lien2 = "";
						mysqli_data_seek($resuCOLS, 0);
						while ($QuelCOLS = Fetche($resuCOLS)) {
							$sortie .= $lien2." '".((trim($QuelCOLS["Type"]) == '') ? NULL : addslashes($QuelVALS[$QuelCOLS["Field"]]))."' ";
							$lien2 = ",";
						}
						$sortie .= ")";
						$lien = ",";
						if (++$compte > 50) {
							$sortie .= ";
							INSERT INTO `".$tab."` VALUES";
							$compte = 0;
							$lien = "";
						}
					}
					$sortie .= ";
					";
				}
	
				$sortie .= "
				*/--
				-- Dump completed on 
				".date("Y-m-d H:i:s")."						
				--*/";
				
				$f = fopen($fichier, "w");
				fwrite($f, $sortie);
				fclose($f);
			}
			
			$zip = new ZipArchive ();
			$zip-> open ("temp/database_".$namedir.".zip", ZipArchive :: CREATE); 
			$zip-> addFile ($fichier); 
			$zip-> close ();
			$retour = 'Voici votre base de données archivée : <a href="temp/database_'.$namedir.'.zip">temp/database_'.$namedir.'.zip</a>';
		}
	}
	while (substr($retour, 0, 3) == '../') { $retour = substr($retour, 3); }
	echo $retour;
?>
