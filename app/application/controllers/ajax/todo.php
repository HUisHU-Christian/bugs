<?php

class Ajax_Todo_Controller extends Base_Controller {

	public $layout = null;
  
	public function __construct() {
		parent::__construct();
		$this->config_app = require("../config.app.php");
	}
	
	public function post_AffichonsAutres() {
		$sortie = "";
		if (Input::get('col') == 0) {
			//Closed issues
			$requISSU = "SELECT ISSU.id, ISSU.status, ISSU.title, TODO.weight, PROJ.name, ISSU.project_id
				FROM projects_issues AS ISSU
				LEFT JOIN users_todos AS TODO ON TODO.issue_id = ISSU.id
				LEFT JOIN projects AS PROJ ON PROJ.id = ISSU.project_id  
				WHERE ISSU.status = 0 
					AND ISSU.assigned_to = ".Input::get('user')."
					AND ISSU.start_at <= NOW()  
				ORDER BY TODO.status DESC, ISSU.updated_at DESC 
				LIMIT ".Input::get('rendu').", ".($this->config_app["TodoNbItems"] ?? 25)." ";
		} else {
			//Active issues
			$requISSU = "SELECT ISSU.id, ISSU.status, ISSU.title, TODO.weight, PROJ.name, ISSU.project_id
				FROM projects_issues AS ISSU
				LEFT JOIN users_todos AS TODO ON TODO.issue_id = ISSU.id
				LEFT JOIN projects AS PROJ ON PROJ.id = ISSU.project_id  
				WHERE ISSU.status != 0 
					AND ISSU.assigned_to = ".Input::get('user')."
					AND TODO.weight >= ".$this->config_app["Percent"][Input::get('col')]." 
					AND TODO.weight < ".$this->config_app["Percent"][Input::get('col')+1]." 
					AND ISSU.start_at <= NOW()  
				ORDER BY TODO.status DESC, ISSU.updated_at DESC 
				LIMIT ".Input::get('rendu').", ".$this->config_app["TodoNbItems"]." ";
		}
		foreach(\DB::query($requISSU) as $lane) {
			$sortie .= '<div class="todo-list-item" id="todo-id-'.$lane->id.'" data-issue-id="'.$lane->id.'" draggable="true"  ondrag="dragStart(this.id);" ondragend="dragDrop(this.id);">';
			$sortie .= '	<div class="todo-list-item-inner">';
			$sortie .= '		<span><span class="colstate" style="color: '.$this->config_app["PriorityColors"][$lane->status].';" onmouseover="document.getElementById(\'taglev\').style.display = \'block\';" onmouseout="document.getElementById(\'taglev\').style.display = \'none\';">&#9899;</span>#'. $lane->id.'</span>';
			$sortie .= '			<a href="project/' . $lane->project_id . '/issue/' . $lane->id.'">'.$lane->title.'</a>&nbsp;<span>( '.$lane->weight.'%)</span>';
			$sortie .= '			<a class="todo-button del" title="Supprimer" data-issue-id="'.$lane->id.'" href="#">[X]</a>';
			$sortie .= '		<div>'.$lane->name.'</div>';
			$sortie .= '	</div>';
			$sortie .= '</div>
			';
		}
		return $sortie;
	}
  
	public function post_add_todo() {
		if (Auth::user()->role_id != 1) { 
			$result = Todo::add_todo(Input::get('issue_id'));
			return json_encode($result);
	}}

	public function post_remove_todo() {
		if (Auth::user()->role_id != 1) { 
			$result = Todo::remove_todo(Input::get('issue_id'));
			return json_encode($result);
	}}

	public function post_update_todo() {
		if (Auth::user()->role_id != 1) { 
			$result = Todo::update_todo(Input::get('issue_id'), Input::get('new_status'));
			return json_encode($result);
	}}
  
	public function post_get_user_todos() {
		if (Auth::user()->role_id != 1) { 
			$result = Todo::load_user_todos();
			return json_encode($result);
	}}

}
