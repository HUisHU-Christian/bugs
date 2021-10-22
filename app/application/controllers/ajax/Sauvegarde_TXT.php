<?php
	$compte = 0;
	$retour = "Non";
	$namedir = date("YmdHis");
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }

	chdir($prefixe."/uploads/");
	$zip = new ZipArchive ();
	$zip-> open ('../temp/emails_'.$namedir.'.zip', ZipArchive :: CREATE); 
	foreach($_POST as $ind => $val) {
		if (file_exists($val.".html")) {
				$zip-> addFile ($val.".html"); 
				$zip-> addFile ($val."_tit.html"); 
				$compte = $compte + 2;
		}
	}
	chdir("../");
	if (isset($_POST["config"])) {
		if (trim($_POST["config"]) != '') {
				$zip-> addFile ("config.app.php"); 
		} 
	}
	$zip-> close ();
	$compte = file_exists('temp/emails_'.$namedir.'.zip') ? $compte : 0;
	if ($compte > 0) { $retour = $compte.' files copied into <a href="temp/emails_'.$namedir.'.zip">temp/emails_'.$namedir.'.zip</a><br />'; }
	
	echo $retour;