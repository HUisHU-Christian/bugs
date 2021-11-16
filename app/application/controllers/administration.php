<?php

class Administration_Controller extends Base_Controller {

	public function __construct() {
		parent::__construct();
		$this->filter('before', 'permission:administration');
	}

	/**
	 * Show general application stats
	 * /administration
	 *
	 * @return View
	 */
	public function get_index() {
		$issues = Project\Issue::count_issues();
		return $this->layout->with('active', 'dashboard')->nest('content', 'administration.index', array(
			'users' => User::where('deleted', '=', 0)->count(),
			'active_projects' => Project::where('status', '=', 1)->count(),
			'archived_projects' => Project::where('status', '=', 0)->count(),
			'issues' => $issues,
			'roles' => Role::count(),
			'tags' => Tag::count(),
		));
	}

	public function get_errors() {
		return true;
		return "Je suis ici en ligne 18";
	}

	public function post_errors() {
//		return true;
		return "Je suis ici avec les ti-namis de la ligne 35 en controllers/administration.php";
//		$ceci = scandir(".");
//		var_dump($ceci);
//		return var_dump($ceci);
//		Administration\Preferences::errors(Input::get('detail'), Input::get('log'), Input::get('exit'), Input::get('exittxt'));
//		return "Voici mon contenu et je vous assure que rien ne sera fait";
	}



}