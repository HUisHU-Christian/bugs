<?php
	$compte = 0;
	$retour = "Non";
	$namedir = date("YmdHis");
	$origpath = "../../uploads/";
	$origpath = "../uploads/";
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }

	chdir($prefixe."/temp");
	foreach($_POST as $ind => $val) {
		if (file_exists($origpath.$val.".html")) {
				$zip = new ZipArchive ();
				$zip-> open ('emails_'.$namedir.'.zip', ZipArchive :: CREATE); 
				$zip-> addFile ($origpath.$val.".html"); 
				$zip-> addFile ($origpath.$val."_tit.html"); 
				$zip-> close ();
				$compte = $compte + 2;
		}
	}
	$compte = file_exists('emails_'.$namedir.'.zip') ? $compte : 0;
	chdir("../");
	if ($compte > 0) { $retour = $compte.' files copied into <a href="temp/emails_'.$namedir.'.zip">temp/emails_'.$namedir.'.zip</a><br />'; }
	echo $retour;