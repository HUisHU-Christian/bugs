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
	
	public static function VerifDataBase () {
		$dir = scandir("../install");
		foreach ($dir as $ind => $val) { 
			if (substr($val, 0, 7) != 'update_') { unset ($dir[$ind]); } 
			if (substr($val, -3) != 'sql') { unset ($dir[$ind]); } 
		}
		$dbitem = array();
		$DBitem = \DB::table('update_history')->where('Footprint', '=', 'Database update via admin')->get(array('Description'));
		foreach ($DBitem as $cetItem) { $dbitem[] = $cetItem->description; }
//		$dbfile = file_get_contents("../install/historique.txt");
//		$dbitem = explode(";", $dbfile);
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
