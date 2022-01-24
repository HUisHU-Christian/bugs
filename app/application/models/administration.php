<?php

class Administration extends Eloquent {

	public static $timestamps = true;

	public function __construct() {
		parent::__construct();
	}
	
	public static function AjourDataBase () {
		//31 décembre 2021: cette fonction ne sert plus, 
		//Voir plutôt controllers/ajax/administration.php
		/*
		$sql = file_get_contents("../install/".Input::get('fichier'));
		\DB::query($sql);
		
		$hist = file_get_contents('../install/historique.txt');
		$hist .= ";".Input::get('fichier');
		file_put_contents('../install/historique.txt', $hist);
		return true;
		*/
	}
	
	public static function AjourStructureBase ($comment = "admin") {
		if (file_exists("../install/".$_GET["MAJsql"])) {
			$sql = file_get_contents("../install/".$_GET["MAJsql"]);
			$dte = filemtime("../install/".$_GET["MAJsql"]);
			$commande = explode("\n", $sql);
			foreach ($commande as $cmd) {
				if (trim($cmd) == "") { continue; }
				$passe = false;
				if (strstr($cmd, "ADD COLUMN") > 0) {
					$field = substr($cmd, strpos($cmd, "ADD COLUMN") + 10, strpos($cmd, " ", strpos($cmd, "ADD COLUMN") + 11) - (strpos($cmd, "ADD COLUMN") + 10));
					$t = substr($cmd, strpos($cmd, "TABLE")+6, strpos($cmd, "ADD") - (strpos($cmd, "TABLE")+6) );
					$struc = \DB::query("SHOW COLUMNS FROM ".substr($cmd, strpos($cmd, "TABLE")+6, strpos($cmd, "ADD") - (strpos($cmd, "TABLE")+6) ));
					foreach ($struc as $champ) {
						if (trim($champ->field) == trim($field)) { $passe = true; break; }
					}
				} elseif (strstr($cmd, "SERT INTO") > 0 && strstr($cmd, "on duplicate") == 0) {
					$cmd = substr($cmd, 0, -1)." on duplicate key update id = id;";
				}
				if ($passe) { continue; }
				\DB::query($cmd);
			}
			\DB::table('update_history')->insert(array('Footprint' => 'Database update via '.$comment.'', 'Description' => $_GET["MAJsql"], 'DteRelease' => date("Y-m-d", $dte), 'DteInstall' => date("Y-m-d H:i:s")));
			unset($_GET["MAJsql"]);
		}
	}
	
	public static function VerifDataBase () {
		$dir = scandir("../install");
		foreach ($dir as $ind => $val) { 
			if (substr($val, 0, 7) != 'update_') { unset ($dir[$ind]); } 
			if (substr($val, -3) != 'sql') { unset ($dir[$ind]); } 
		}
		$dbitem = array();
		$DBitem = \DB::table('update_history')->where('Footprint', 'LIKE', 'Database update via%')->get(array('Description'));
		foreach ($DBitem as $cetItem) { $dbitem[] = $cetItem->description; }
		return array_diff($dir, $dbitem);
	}

	public static function versionsSQL ($comparable) {
		$lesVersions = array();
		$prevUpdates = scandir("../install");
		foreach ($prevUpdates as $ind => $nom) {
			if (substr($nom, 0, 8) == 'update_v' && !in_array($nom, $comparable) ) { $lesVersions[] = $nom; }
		}
		return $lesVersions;
	}
	
	
	public static function prefixe() {
		$prefixe = "";
		while (!file_exists($prefixe."config.app.php")) {
			$prefixe .= "../";
		}
		return $prefixe;
	}

	public static function retournons($a, $txt = "") {
		if ($txt == "") {
			$txt = ($a == 0) ? __('tinyissue.admin_modif_false') : __('tinyissue.admin_modif_true');
		}
		$coul =  ($a == 0) ? 'red' : 'black';
		return $txt.'|'.$coul;
	}

}
