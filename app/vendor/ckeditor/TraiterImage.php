<?php
function Telechargement ($CetteImage, $destination_dir = "uploads/", $NomFinal = "Profil", $LargeurFinale = 1, $HauteurFinale = 1, $Sens = "Horiz") {
	$file_max_size = 2000000;
	if (isset($CetteImage) && isset($_POST["Confirmer"]) && is_array($CetteImage)) {
		if ($CetteImage['error'] == UPLOAD_ERR_OK) {
			if($CetteImage['size'] > 0 && $CetteImage["size"] <= $file_max_size) {
				$authorized_extensions = array('image/jpeg', 'image/pjpeg' );
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
					$destination_file = $NomFinal.".jpg";
					//Nouvelle image au format de la version finale
					$ImgSource = imagecreatefromjpeg($CetteImage['tmp_name']);
					$TailleInitiale = getimagesize($CetteImage['tmp_name']);
					$LargeurFinale = ($LargeurFinale == 1) ? $TailleInitiale[0] : $LargeurFinale;
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
					imagejpeg($ImgPetite,   $destination_dir.DIRECTORY_SEPARATOR.$destination_file);
					return true;
				} else { echo 'Mauvais format de fichier<br />'; return false; }								
			} else { echo 'Fichier trop grand ou inexistant.<br />'; return false; }
		} else { 
         		switch ($CetteImage['aFile']['error']){
				case UPLOAD_ERR_INI_SIZE:
						echo 'Le fichier Téléchargé est plus grand que la taille maximale définie dans php.ini (variable `max_filesize`).<br />';
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