<?php

class Administration extends Eloquent {

	//public static $table  = 'projects_users';
	public static $timestamps = true;

	/******************************************************************
	* Static methods for working with Preferences on a Administration *
	******************************************************************/

	/**
	* Edit the administrator's preferences on error managing
	*
	* @param  int   		$detail    ... show details on user's screen  True/False
	* @param  int   		$log       ... log errors into  app/storage/logs
	* @param  int/text   $exit	   ... exit type:  0/1 just stop there    text: show text on screen
	* @return void
	*/
	public static function errorsManagment($details = "Oui", $log = "Non", $exit = 0, $exittxt) {
		return "Nous sommes ici en ligne 21 de models";
		$ceci = scandir(".");
		var_dump($ceci);
		exit();
		$NomFichier = "app/application/config/error.php";
		$RefFichier = fopen($NomFichier, "r");
		$rendu = 0;
		while (!feof($RefFichier)) {
			$MesLignes[$rendu] = fgets($RefFichier);
			if (strstr($MesLignes[$rendu], "'detail' => ") !== NULL) { $MesLignes[$rendu] = "'detail' => ".(($details == 'Oui') ? 'true' : 'false').", "; }
			if (strstr($MesLignes[$rendu], "'log' => ") !== NULL) { $MesLignes[$rendu] = "'log' => ".(($log == 'Oui') ? 'true' : 'false').", "; }
			if (strstr($MesLignes[$rendu], "'exit' => ") !== NULL) { $MesLignes[$rendu] = "'exit' => ".($exit == 'Non') ? 1 : "'".$exittxt."'").", "; }
			$rendu = $rendu + 1;
		}
		fclose($RefFichier);

		$NeoFichier = fopen($NomFichier, "w");
		foreach ($MesLignes as $ind => $val) {
			fwrite($NeoFichier, $val);
		}
		fclose($NeoFichier);
	}
}