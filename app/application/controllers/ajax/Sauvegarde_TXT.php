<?php
	$compte = 0;
	$retour = "Non";
	$namedir = date("YmdHis");
	$origpath = "../../uploads/";
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }

	chdir($prefixe."/temp");
	mkdir($namedir);
	chdir($namedir);
	foreach($_POST as $ind => $val) {
		if (file_exists($origpath.$val.".html")) {
			$compte = (copy ($origpath.$val.".html .")) ? $compte + 1 : $compte;
			$compte = (copy ($origpath.$val."_tit.html .")) ? $compte + 1 : $compte;
		}
	}
	chdir("../../");
	if ($compte > 0) { $retour = $compte." files copied into temp/".$namedir."<br />"; }
	echo $retour;