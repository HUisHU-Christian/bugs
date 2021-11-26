<?php namespace User;

class Activity extends \Eloquent {

	public static $table = 'users_activity';
	public static $timestamps = true;
	
	
	public function user() {
		return $this->belongs_to('\User');
	}
	
	
	public function other_user() {
		return $this->belongs_to('\User');
	}
	
	public function activity() {
		return $this->belongs_to('Activity', 'type_id');
	}
		
	/**
	* Add an users_activity action
	*
	* @param  int     $type_id
	* @param  int     $parent_id
	* @param  int     $item_id
	* @param  int     $action_id
	* @param  string  $data
	* @return bool
	*/
	public static function add($type_id = 18, $parent_id = 0, $item_id = null, $action_id = null, $data = null, $created_at = NULL, $updated_at = NULL) {
		$created_at = (date("Y-m-d H:i:s") === NULL) ? date("Y-m-d H:i:s") : $created_at;
		$updated_at = (date("Y-m-d H:i:s") === NULL) ? date("Y-m-d H:i:s") : $updated_at;
		$insert = array(
			'type_id' => $type_id,
			'parent_id' => $parent_id,
			'user_id' => \Auth::user()->id,
			'item_id' => (is_null($item_id) ? NULL : $item_id),
			'action_id' => (is_null($action_id) ? NULL : $action_id),
			'data' => (is_null($data) ? NULL : $data),
			'created_at' => $created_at,
			'updated_at' => $updated_at
		);

		$activity = new static;

		return $activity->fill($insert)->save();
	}

}