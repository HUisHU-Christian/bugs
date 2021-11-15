<?php

class Ajax_Project_Controller extends Base_Controller {

	public $layout = null;

	public function __construct() {
		parent::__construct();
	}

	//Laissée ici à titre d'exemple d'une fonction $_GET
	public function get_inactive_users() {

		return json_encode($results);
	}

	public function post_errors() {
		Administration\Preferences::errors(Input::get('detail'), Input::get('log'), Input::get('exit'));
	}

	//Laissée ici à titre d'exemple d'une fonction $_POST qui appelle un modèle
	public function post_changeRoleUser() {
		Project\User::change_role(Input::get('user_id'), Input::get('role_id'), Input::get('project_id'));
	}

	//Laissée ici à titre d'exemple d'une fonction $_POST qui agit directement avant d'appeler un modèle
	public function post_issue_upload_attachment() {
		$user_id = Crypter::decrypt(str_replace(' ', '+', Input::get('session')));

		Auth::login($user_id);

		if(!Auth::user()->project_permission(Input::get('project_id'))) {
			return Response::error('404');
		}

		Project\Issue\Attachment::upload(Input::all());

		return true;
	}

}