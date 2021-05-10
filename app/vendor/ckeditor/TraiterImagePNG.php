<?php if (strstr($_SERVER['PHP_SELF'], 'outils/') != '' ) { ?> <script type="text/javascript" >document.location.href = "../"; </script> <?php } ?>
<?php
function TelechargementPNG ($CetteImage, $destination_dir = "images/Membres/Profil", $NomFinal = "Profil", $HauteurFinale = 1, $Sens = "Horiz") {
	global $QuiEstCe, $QuiEstCeNom;
	$file_max_size = 250000;
	if (isset($CetteImage) && isset($_POST["Confirmer"]) && is_array($CetteImage)) {
		if ($CetteImage['error'] == UPLOAD_ERR_OK) {
			if($CetteImage['size'] > 0 && $CetteImage["size"] <= $file_max_size) {
//				$authorized_extensions = array('image/jpeg', 'image/gif', 'image/png');
				$authorized_extensions = array('image/png', 'image/ppng' );
				if (!is_dir($destination_dir)) {
					echo 'Veuillez indiquer un répertoire destination correct !<br />';
					die(); 
 						}
				if (!is_writeable($destination_dir)) {
					echo 'Veuillez spécifier des droits en écriture pour le répertoire destination !<br />';
					die();      
				}  
				$lastPos = strRChr($CetteImage['name'], ".");
				if ($lastPos !== false && in_array(strToLower($CetteImage['type']), $authorized_extensions)) {
					$destination_file = $NomFinal.".png";
					//Nouvelle image au format de la version finale
					$ImgSource = imagecreatefrompng($CetteImage['tmp_name']);
					$TailleInitiale = getimagesize($CetteImage['tmp_name']);
					$LargeurFinale = ($HauteurFinale == 1) ? $TailleInitiale[0] : ($TailleInitiale[0] / $TailleInitiale[1] ) * $HauteurFinale;
					$HauteurFinale = ($HauteurFinale == 1) ? $TailleInitiale[1] : $HauteurFinale;
					$ImgPetite = imagecreatetruecolor($LargeurFinale, $HauteurFinale);
					imagealphablending($ImgPetite, false);
					imagesavealpha($ImgPetite, true);
					// Redimensionnement
					imagecopyresized($ImgPetite, $ImgSource, 0, 0, 0, 0, $LargeurFinale, $HauteurFinale, $TailleInitiale[0], $TailleInitiale[1]);
					//Rotation si necessaire
					if ( ($Sens == "Vert" && $TailleInitiale[0] > $TailleInitiale[1]) || ($Sens == "Horiz" && $TailleInitiale[0] < $TailleInitiale[1]) ) {
							imagerotate ($ImgPetite, 270, 0);
					}
					//Inscription de la propriété intellectuelle
					//Cette fonction a été éliminée du PNG par rapport au JPG
					//Enregistrement final
					imagepng($ImgPetite,$destination_dir.DIRECTORY_SEPARATOR.$destination_file);
					return $ImgPetite;
				} else { echo 'Mauvais format de fichier<br />'; }								
			} else { echo 'Fichier trop grand ou inexistant.<br />'; }
		} else { 
         		switch ($CetteImage['aFile']['error']){
				case UPLOAD_ERR_INI_SIZE:
						echo 'Le fichier Téléchargé dépasse la valeur spécifiée pour upload_max_filesize dans php.ini.<br />';
						break;
				case UPLOAD_ERR_FORM_SIZE:
						echo 'Le fichier téléchargé dépasse la valeur spécifiée pour MAX_FILE_SIZE dans le formulaire d\'upload.<br />';
						break;
				case UPLOAD_ERR_PARTIAL:
						echo 'Le fichier n`a été que partiellement téléchargé.<br />';
						break;
				default:
						echo 'Aucun fichier n`a été téléchargé.<br />';
			} // switch
  				return false;
		}
	} else { // aucun fichier reçu
  				echo 'Pas de fichier recu.<br />';
  				return false;
	} 
	//Fin du traitement relatif a l'ajout d'image
}