<?php

class Ajax_Administration_Controller extends Base_Controller {

	public $layout = null;

	public function __construct() {
		parent::__construct();
		
	}

	//Laissée ici à titre d'exemple d'une fonction $_GET
	public function get_inactive_users() {

		return json_encode($results);
	}

	public function get_errors() {
		return "Je suis ici en ligne 18";
	}

	public function post_errors() {
		$NomFichier = "application/config/error.php";
		$RefFichier = fopen($NomFichier, "r");
		$rendu = 0;
		while (!feof($RefFichier)) {
			$MesLignes[$rendu] = fgets($RefFichier);
			if (strpos($MesLignes[$rendu], "'detail' => ") > 0) { $MesLignes[$rendu] = "   'detail' => ".Input::get('detail').", 
			"; }
			if (strpos($MesLignes[$rendu], "'log' => ") 	  > 0) { $MesLignes[$rendu] = "   'log' => ".Input::get('log') .", 
			"; }
			if (strpos($MesLignes[$rendu], "'exit' => ")   > 0) { $MesLignes[$rendu] = "   'exit' => ".((Input::get('exit') == 'false') ? 1 : "'".Input::get('exittxt')." <a href=\"todo\">BUGS</>.'").", 
			"; }
			$rendu = $rendu + 1;
		}
		fclose($RefFichier);
		file_put_contents($NomFichier, $MesLignes);
		return Input::get('log').' & '.Input::get('exit');
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