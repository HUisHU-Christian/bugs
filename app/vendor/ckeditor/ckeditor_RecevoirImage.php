<?php
$prefixe = "";
while (!file_exists($prefixe."config.app.php")) {
	$prefixe .= "../";
}
if ($_FILES["upload"]['size'] > 0 ) {
	$destination_dir = $prefixe."uploads/";

	$_POST["Confirmer"] = "oui";
	$NomFinal = substr($_FILES["upload"]["name"],0, (strlen($_FILES["upload"]["name"])-4));
	$NomFinal = str_replace(" ", "", $NomFinal);

	$file_max_size = 2000000;
	if (isset($_FILES["upload"]) && is_array($_FILES["upload"])) {
		if ($_FILES["upload"]['error'] == UPLOAD_ERR_OK) {
			if($_FILES["upload"]['size'] > 0 && $_FILES["upload"]["size"] <= $file_max_size) {
				if (!is_dir($destination_dir)) {
					echo '<script>alert("Veuillez indiquer un répertoire destination correct !");</script>';
					die(); 
 				}
				if (!is_writeable($destination_dir)) {
					echo '<script>alert("Veuillez spécifier des droits en écriture pour le répertoire destination !");</script>';
					die();      
				}
				move_uploaded_file($_FILES["upload"]['tmp_name'], $destination_dir.$_FILES["upload"]['name']);
				if (!in_array(strtolower(substr($_FILES["upload"]["name"],-4)), array(".jpg", "jpeg", ".png"))) {
					die();
				}
			} else { 
				echo '<script>alert("Mauvais format de fichier.");</script>'; 
				die(); 
			}
		} else { 
			echo '<script>alert("Fichier trop grand ou inexistant.");</script>';
			die(); 
		}
	} else { 
   	switch ($_FILES["upload"]['aFile']['error']){
			case UPLOAD_ERR_INI_SIZE:
					echo '<script>alert("Le fichier Téléchargé est plus grand que la taille maximale définie dans php.ini (variable `max_filesize`).");</script>';
					break;
			case UPLOAD_ERR_FORM_SIZE:
					echo '<script>alert("Le fichier téléchargé dépasse la valeur spécifiée pour MAX_FILE_SIZE dans le formulaire d\'upload.");</script>';
					break;
			case UPLOAD_ERR_PARTIAL:
					echo '<script>alert("Le fichier n`a été que partiellement téléchargé.");</script>';
					break;
			default:
					echo '<script>alert("Aucun fichier n`a été téléchargé.");</script>';
		} // switch
		die(); 
	}
} else { // aucun fichier reçu
	echo '<script>alert("Pas de fichier recu.");</script>';
	die(); 
} 

?>
<script>
	for(NumRef=90; NumRef<1900; NumRef++) {
		if (parent.document.getElementById('cke_' + NumRef + '_label')) {
			if (parent.document.getElementById('cke_' + NumRef + '_label').innerHTML == 'OK') {
				break;
			}
		}
	}
	//De base, on peut s'attendre à ce que le bouton OK porte le numéro 138
	//Dans ce cas, les valeurs qui nous intéressent seront 57, 67, 70, 76, 79, 82, 85 et 138
	//Donc les valeurs calculées se font en soustrayant   81, 71, 68, 62, 59, 56, 53 et 0
	parent.document.getElementById('cke_' + (NumRef - 81) + '_textInput').value = "<?php echo "uploads/".$_FILES["upload"]['name']; ?>";
	parent.document.getElementById('cke_' + (NumRef - 71) + '_textInput').value = "200";
	parent.document.getElementById('cke_' + (NumRef - 68) + '_textInput').value = "150";
	parent.document.getElementById('cke_' + (NumRef - 62) + '_textInput').value = "0";
	parent.document.getElementById('cke_' + (NumRef - 59) + '_textInput').value = "15";
	parent.document.getElementById('cke_' + (NumRef - 56) + '_textInput').value = "15";
	parent.document.getElementById('cke_' + (NumRef - 53) + '_select').selectedIndex = 1;
	parent.document.getElementById('cke_' + (NumRef - 0) + '_label').click();
</script>
