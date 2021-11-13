<?php

class Ajax_Project_Controller extends Base_Controller {

	public $layout = null;

	public function __construct() {
		parent::__construct();

		//$this->filter('before', 'ajax')->except('issue_upload_attachment');
		$this->filter('before', 'permission:project-modify')->only(array(
			'inactive_users',
			'add_user',
			'remove_user',
		));
		$this->filter('before', 'permission:issue-modify')->only(array(
			'issue_assign'
		));

	}

	public function get_inactive_users() {
		$project = Project::find(Input::get('project_id'));

		$results = array();
		$users = (is_null($project)) ? User::all() : $project->users_not_in();

		foreach($users as $row) {
			$results[] = array(
				'id' => $row->id,
				'label' => $row->firstname . ' ' . $row->lastname
			);
		}

		return json_encode($results);
	}

	public function post_add_user() {
		Project\User::assign(Input::get('user_id'), Input::get('project_id'));
	}

	public function post_changeRoleUser() {
		Project\User::change_role(Input::get('user_id'), Input::get('role_id'), Input::get('project_id'));
	}

	public function post_chronometrons() {
		if (Input::get("etat") == 'on') {
		$resu=	\DB::query("INSERT INTO projects_issues_comments (created_by, project_id, issue_id, temps_fait_deb, created_at) 
							VALUES (".Input::get("user_id").", ".Input::get("project_id").", ".Input::get("issue_id").", '".date("H:i:s")."', '".date("Y-m-d H:i:s")."') ");
		} else {
			$resu = \DB::query("UPDATE projects_issues_comments 
							SET 	comment = '".addslashes(Input::get("comment"))."', 
									temps_fait_fin = '".date("H:i:s")."', 
									temps_fait = (HOUR(TIMEDIFF(created_at, '".date("Y-m-d H:i:s")."')) + 1), 
									updated_at = '".date("Y-m-d H:i:s")."'
							WHERE created_by = ".Input::get("user_id")."
							AND project_id = ".Input::get("project_id")."
							AND issue_id = ".Input::get("issue_id")."
							AND temps_fait_deb IS NOT NULL
							AND temps_fait_fin IS NULL
							AND created_at IS NOT NULL
							AND updated_at IS NULL");
		}
		return $resu;
	}

	public function post_remove_user() {
		Project\User::remove_assign(Input::get('user_id'), Input::get('project_id'));
	}

	public function post_issue_assign() {
		Project\Issue::find(Input::get('issue_id'))->reassign(Input::get('user_id'));
	}

	//Patrick 25 mars 2017
	public function post_issue_retag() {
		Project\Issue::find(Input::get('issue_id'))->retag(Input::get('tag_id'));
	}

	public function post_issue_upload_attachment() {
		$user_id = Crypter::decrypt(str_replace(' ', '+', Input::get('session')));

		Auth::login($user_id);

		if(!Auth::user()->project_permission(Input::get('project_id'))) {
			return Response::error('404');
		}

		Project\Issue\Attachment::upload(Input::all());

		return true;
	}

	public function post_issue_remove_attachment() {
		Project\Issue\Attachment::remove_attachment(Input::all());
	}

}