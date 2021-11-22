<?php

class Administration extends Eloquent {

	public static $timestamps = true;

	public function __construct() {
		parent::__construct();
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
