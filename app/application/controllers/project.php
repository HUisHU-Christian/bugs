<?php
class Project_Controller extends Base_Controller {

	public $layout = 'layouts.project';

	public function __construct() {
		parent::__construct();

		$this->filter('before', 'project');
		$this->filter('before', 'permission:project-modify')->only('edit');
	}

	/**
	 * Display activity for a project
	 * /project/(:num)
	 *
	 * @return View
	 */
	public function get_index() {
		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/activity', array(
				'project' => Project::current(),
				'activity' => Project::current()->activity(\Config::get('application.pref.todoitems'))
			)),
			'active' => 'activity',
			'open_count' => Project::current()->count_open_issues(),
			'closed_count' => Project::current()->count_closed_issues(),
			'assigned_count' => Project::current()->count_assigned_issues(),
			'future_count' => Project::current()->count_future_issues()
		));
	}

	/**
	 * Display issues for a project
	 * /project/(:num)
	 *
	 * @return View
	 */
	public function get_issues() {
		Asset::add('tag-it-js', '/app/assets/js/tag-it.min.js', array('jquery', 'jquery-ui'));
		Asset::add('tag-it-css-base', '/app/assets/css/jquery.tagit.css');
		Asset::add('tag-it-css-zendesk', '/app/assets/css/tagit.ui-zendesk.css');

		
		//options de base pour le tri
		$sort_options = array('projects_issues.id' => __('tinyissue.sort_option_id'), 'projects_issues.updated_at' => __('tinyissue.sort_option_updated'), 'projects_issues.status' => __('tinyissue.priority'));

		/* Get what to sort by */
		$sort_by = Input::get('sort_by', (Input::get('tag_id','') == 2) ? 'projects_issues.updated_at' : 'projects_issues.status');
		$sort_keys = array_keys($sort_options);
		$default_sort_order = ((in_array($sort_by, $sort_keys)) ? 'desc' : 'asc');
		if (substr(Input::get('sort_by', ''), 0, strlen('tag:')) == 'tag:') {
			$sort_by_clause = DB::raw("
				MIN(CASE WHEN tags.tag LIKE " . DB::connection('mysql')->pdo->quote(substr(Input::get('sort_by', ''), strlen('tag:')) . ':%') . " THEN 1 ELSE 2 END),
				IF(NOT ISNULL(tags.tag), 1, 2),
				tags.tag
			");
		} else {
			//$sort_by_clause = ($sort_by == 'id') ? 'projects_issues.id' :  'projects_issues.updated_at';
			$sort_by_clause = (in_array($sort_by, $sort_keys)) ? $sort_by : 'projects_issues.status';
		}

		/* Get what order to use for sorting */
		$sort_order = Input::get('sort_order', $default_sort_order);
		$sort_order = (in_array($sort_order, array('asc', 'desc')) ? $sort_order : $default_sort_order);

		/* Get which user's issues to show */
		$assigned_to = Input::get('assigned_to', '');

		/* Get which tags to show */
		/* by tag_id  */
		$tags = Input::get('tags', '');
		$tag = Input::get('tag_id', '');

		/* Build query for issues */
		$issues = \Project\Issue::with('tags');

		$issues = $issues->where('project_id', '=', Project::current()->id);
		//Moduler l'affichage selon le taquet activé:
		if (Input::get('tag_id', '') == '2') {								//Taquet des messages fermés
			$issues->where_null('closed_at', 'and', true);
		} elseif (Input::get('tag_id', '') == '3') {						//Taquet des messages futurs
			$issues->where_null('closed_at', 'and', false)->where('start_at', '>', date('Y-m-d'));
		} else {																		//Taquet des messages ouverts ou de l'historique
			$issues->where_null('closed_at', 'and', false);
			if (Input::get('tag_id') == 1) {									//Taquet des messages ouverts
				$issues = $issues->where('projects_issues.start_at', '<=', date("Y-m-d"));
			}
		}

		if ($assigned_to) {
			$issues = $issues->where(Input::get('limit_contrib','assigned_to'), '=', $assigned_to);
		}
		
		if (Input::get('limit_period') != '') {
			$issues = $issues->where('projects_issues.'.Input::get('limit_event','created_at'), '>=', Input::get('DateInit',''));
			$issues = $issues->where('projects_issues.'.Input::get('limit_event','created_at'), '<=', Input::get('DateFina',''));
		}

		if ($tags) {
			$tags_collection = explode(',', $tags);
			foreach ($tags_collection as $Tid => $Tval ) { if(substr(trim($Tval), -2) == ':*') { unset ($tags_collection[$Tid]); } }
			$tags_amount = count($tags_collection);
			if ($tags_amount < 1) { $tag = false; } else { $issues = $issues->where_in('tags.tag', $tags_collection); }  //->get();
		}
		if ($tags || $tag || !in_array($sort_by, $sort_keys)) {
			$issues = $issues
				->left_join('projects_issues_tags', 'projects_issues_tags.issue_id', '=', 'projects_issues.id')
				->left_join('tags', 'tags.id', '=', 'projects_issues_tags.tag_id');
		}

		$issues = $issues
			->group_by('projects_issues.id')
			->order_by($sort_by_clause, $sort_order);

		if($tags && $tags_amount > 1){
			$issues = $issues->having(DB::raw('COUNT(DISTINCT `tags`.`tag`)'),'=',$tags_amount);
		}

		$issues = $issues->get(array('projects_issues.*'));

		/* Get which tab to highlight */
		if ($assigned_to == Auth::user()->id) { $active = 'assigned';
		} else if (Input::get('tags', '') == 'status:closed') { $active = 'closed';
		} else if (Input::get('tag_id', '') == '1') { $active = 'open';
		} else if (Input::get('tag_id', '') == '2') { $active = 'closed';
		} else if (Input::get('tag_id', '') == '3') { $active = 'future';
		} else { $active = 'open';
		}

		/* Get sort options */
		//La liste des « options de base pour le tri » est déplacée au début de la présente function
		$tags = \Tag::order_by('tag', 'ASC')->get();
		foreach ($tags as $tag) {
			$colon_pos = strpos($tag->tag, ':');
			if ($colon_pos !== false) {
				$tag = substr($tag->tag, 0, $colon_pos);
			} else {
				$tag = $tag->tag;
			}
			$sort_options["tag:$tag"] = $tag;
		}

		/* Get assigned users */
		$assigned_users = array('' => '');
		foreach(Project::current()->users as $user) {
			$assigned_users[$user->id] = $user->firstname . ' ' . $user->lastname;
		}
		
		/* Build layout */
		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/issues', array(
				'sort_options' => $sort_options,
				'sort_order' => $sort_order,
				'assigned_users' => $assigned_users,
				'issues' => $issues,
			)),
			'active' => $active,
			'open_count' => Project::current()->count_open_issues(),
			'closed_count' => Project::current()->count_closed_issues(),
			'assigned_count' => Project::current()->count_assigned_issues(),
			'future_count' => Project::current()->count_future_issues()
		));
	}

	/**
	 * Edit the project
	 * /project/(:num)/edit
	 *
	 * @return View
	 */
	public function get_edit() {
		\Log::write(5,'Edit project: enter into editing page for project `'.Project::current()->name.'` ');
		return $this->layout->nest('content', 'project.edit', array(
			'project' => Project::current()
		));
	}

	public function post_edit() {
		\Log::write(3,'Edit project: project `'.Project::current()->name.'` will be edited ');
		$ancProj = Project::current()->name;
		/* Delete the project */
		if(Input::get('delete')) {
			//Email to all of this project's followers
			$followers =\DB::query("SELECT USR.email, CONCAT(USR.firstname, ' ', USR.lastname) AS user, USR.language, PRO.name FROM following AS FAL LEFT JOIN users AS USR ON USR.id = FAL.user_id LEFT JOIN projects AS PRO ON PRO.id = FAL.project_id WHERE FAL.project_id = ".Project::current()->id." AND FAL.project = 1 ".((isset($thisIssue[0])) ? " AND FAL.user_id NOT IN (".$thisIssue[0]->attributes["assigned_to"].",".\Auth::user()->id.")" : ""). "");
			\Log::write(5,'Edit project: Mailing about the edition of project `'.Project::current()->name.'` ');
			\Mail::letMailIt(array(
				'ProjectID' => \Project::current()->id, 
				'IssueID' => 0, 
				'SkipUser' => true,
				'Type' => 'Project', 
				'user' => $followers,
				'contenu' => array('projectdel'),
				'src' => array('tinyissue')
				),
				\Auth::user()->id, 
				\Auth::user()->language
			);

			Project::delete_project(Project::current());
			return Redirect::to('projects')
				->with('notice', __('tinyissue.project_has_been_deleted'));
		}

		/* Update the project */
		$update = Project::update_project(Input::all(), Project::current());
		$weblnk = Project::update_weblnks(Input::all(), Project::current());

		if($update['success']) {
			\Log::write(3,'Edit project: project `'.Project::current()->name.'` as been succefully edited ');
			//Email to all of this project's followers
			\Mail::letMailIt(array(
				'ProjectID' => \Project::current()->id, 
				'IssueID' => Project::current()->id, 
				'SkipUser' => true,
				'Type' => 'Project', 
				'user' => \Auth::user()->id,
				'contenu' => array('projectmod','static:'.$ancProj),
				'src' => array('tinyissue','value')
				),
				\Auth::user()->id, 
				\Auth::user()->language
			);

			return Redirect::to(Project::current()->to('edit'))
				->with('notice', __('tinyissue.project_has_been_updated'));
		}

		return Redirect::to(Project::current()->to('edit'))
			->with_errors($update['errors'])
			->with('notice-error', __('tinyissue.we_have_some_errors'));
	}

	public function post_changeRoleUser() {
		return "Nous sommes ici à la ligne 211 - POST de controllers/project.php";
	}	
}