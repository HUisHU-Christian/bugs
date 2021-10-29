<?php
	require_once "db.php";
	$_POST["Courriel"] = $_POST["Courriel"] ?? '';

	$compte = 0;
	$retour = "Non";
	$sortie = "";
	chdir ($prefixe);

	$config = require "config.app.php";
	$fichier = "temp/database_".date("YmdHis");
	
	$resuUSER = Requis("SELECT * FROM users WHERE email = '".$_POST["Courriel"]."' ");
	if(Nombre($resuUSER) == 1) {
		require_once("app/laravel/hash.php");
		$QuelUSER = Fetche($resuUSER);	
		if (Laravel\Hash::check($_POST["MotPasse"], $QuelUSER["password"]) && $QuelUSER["role_id"] == 4 ) {
			if ($_POST["OS"] == 'Linux') {
				$commande = "mysqldump -u ".$config['database']['username']." --password=".$config['database']['password']." ".$config['database']['database']." > ".$fichier.".sql";
				exec($commande);
			} else {
					$sortie = "-- BUGS 
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
						$tab = $QuelTABL["Tables_in_".$config['database']['database']];
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
						$resuVALS = Requis("SELECT * FROM ".$tab." ");
						if (Nombre($resuVALS) > 0) {
							$sortie .= "INSERT INTO `".$tab."` VALUES";
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
					}
							
					$f = fopen($fichier.".sql", "w");
					fwrite($f, $sortie);
					fclose($f);
			}

			if (file_exists($fichier.".sql")) {
				$zip = new ZipArchive ();
				$zip-> open ($fichier.".zip", ZipArchive :: CREATE); 
				$zip-> addFile ($fichier.".sql"); 
				$zip-> close ();
				$compte = $compte + 1;
				$retour = 'Voici votre base de données archivée : <a href="'.$fichier.'.sql">'.$fichier.'.sql</a><br />';
			}
			if (file_exists($fichier.".zip")) {
				$compte = $compte + 1;
				$retour .= 'Voici votre base de données archivée : <a href="'.$fichier.'.zip">'.$fichier.'.zip</a><br />';
			}
		}
	}
echo ($compte == 0) ? 'Échec' : $retour;
?>
