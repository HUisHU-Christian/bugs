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
			exec("cp ".$origpath.$val.".html .");
			exec("cp ".$origpath.$val."_tit.html .");
			if (file_exists($origpath.$val.".html")) 		{ $compte = $compte + 1; }
			if (file_exists($origpath.$val."_tit.html"))	{ $compte = $compte + 1; }
		}
	}
	chdir("../../");
	if ($compte > 0) { $retour = $compte." files copied into temp/".$namedir."<br />"; }
	echo $retour;
?>
