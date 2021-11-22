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
	
	public function post_DragDropChgIssue() {
		$retour = 0;
		
		if (Input::get('Quoi') == 1) {
			$tags = Input::get('tags') ?? 1;
			$attached = Input::get('attached') ?? 1;
			$requ = (Input::get('Etat') == 0) ?
				"INSERT INTO following VALUES(NULL, ".Input::get('Qui').", ".Input::get('Project').", ".Input::get('Quel').", 0, ".$attached.", ".$tags.")" :
				"DELETE FROM following WHERE user_id = ".Input::get('Qui')." AND project_id = ".Input::get('Project')." AND issue_id = ".Input::get('Quel')." AND project = 0";
			$retour = 1;
			try { \DB::query($requ); } catch (\Exception $e) { $retour = 0; }
		}

		if (Input::get('Quoi') == 2) {
			$requ = (Input::get('Etat') == 0) ?
				"INSERT INTO following VALUES(NULL, ".Input::get('Qui').", ".Input::get('Project').", 0, 1, 0, 0)" :
				"DELETE FROM following WHERE user_id = ".Input::get('Qui')." AND project_id = ".Input::get('Project')." AND project = 1" ;
			$retour = 4;
			try { \DB::query($requ); } catch (\Exception $e) { $retour = 0; }
		}
		
		//DragDrop an issue in the TODO windows		
		if (Input::get('Quoi') == 3) {
			$issue_id = substr(Input::get('cetDIV'), 8);
			$requISSU = "SELECT role_id, ISSU.status AS status, ISSU.project_id AS project_id FROM projects_issues AS ISSU LEFT JOIN projects_users AS PUSR ON PUSR.project_id = ISSU.project_id WHERE ISSU.id =".$issue_id;
			foreach (\DB::query($requISSU) as $lane) {
				if ($lane->role_id != 1) { 
					$old_status = intval(substr(Input::get('divORIG'),-1));
					$new_status = intval(substr(Input::get('divOVER'),-1));
					if ($new_status >= 0 && $new_status <= 3) {
						// Close issue if todo is moved to closed lane. 
						if ($new_status == 0) {
							\DB::query ("INSERT INTO users_activity (id,user_id,parent_id,item_id,type_id,created_at,updated_at) VALUES (NULL, ".\Auth::user()->id.", ".$lane->project_id.", ".$issue_id.", 3, NOW(), NOW() ) on duplicate key UPDATE updated_at = NOW()");
							\DB::query ("UPDATE users_todos SET status = 0, updated_at = NOW() WHERE issue_id = ".$issue_id);
							\DB::query ("UPDATE projects_issues SET status = 0, closed_by = ".\Auth::user()->id.", closed_at = NOW() WHERE id = ".$issue_id."");
							\DB::query ("UPDATE projects_issues_tags SET tag_id = 2, updated_at = NOW() WHERE id = ".$issue_id." AND tag_id = 1");
							$retour = 8;
						} else {
							$Moyenne = ($this->config_app['Percent'][$new_status] + $this->config_app['Percent'][$new_status + 1]) / 2;
							\DB::query ("INSERT INTO users_activity (id,user_id,parent_id,item_id,type_id, created_at,updated_at) VALUES (NULL, ".\Auth::user()->id.", ".$lane->project_id.", ".$issue_id.", 4, NOW(), NOW() ) on duplicate key UPDATE updated_at = NOW()");
							\DB::query ("UPDATE users_todos SET status = ".(($lane->status == 0) ? 4 : $lane->status ).", weight = ".$Moyenne.", updated_at = NOW() WHERE issue_id = ".$issue_id);
							\DB::query ("UPDATE projects_issues SET closed_by = NULL, closed_at = NULL, status = ".(($lane->status == 0) ? 4 : $lane->status ).", weight = ".$Moyenne.", updated_at = NOW() WHERE id = ".$issue_id);
							\DB::query ("UPDATE projects_issues_tags SET tag_id = 1, updated_at = NOW() WHERE id = ".$issue_id." AND tag_id = 2");
							$retour = 16;
						}
					}
				}
			}
		}
		return ($retour > 0) ? __('tinyissue.admin_modif_true')."|black" : __('tinyissue.admin_modif_false')."|red";
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
