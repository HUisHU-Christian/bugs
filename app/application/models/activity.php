<?php

class Activity extends Eloquent {

	public static $table = 'activity';

	/**
	* Add an activity action
	*
	* @param  int     		$id
	* @param  varchar(255)  $description
	* @param  varchar(255)  $activity
	* @return bool
	*/
	public static function add($description = NULL, $activity = NULL) {
		$insert = array(
			'description' => $description,
			'activity' => $activity
		);

		$activity = new static;

		return $activity->fill($insert)->save();
	}
	
	public static function update_activity($info) {
		//Enregistrement de la modification en bdd
		$requ = "UPDATE activity SET ";
		$lien = "";
		foreach ($info["desc"] as $ind => $val) {
			if (in_array($ind, array('id'))) { continue; }
			$requ .= $lien.strtoupper($ind). " = '".$val."'";
			$lien = ", ";
		}
		$requ .= " WHERE id = ".$info["id"];

		try {
			\DB::query($requ);
		} catch (\Exception $e) {
			return array('success' => false, 'requ' => $requ);
		}
		return array('success' => true, 'requ' => $requ);

	}	
	
}