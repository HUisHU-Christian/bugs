<?php

require_once "application/libraries/mail.php";

class Project extends Eloquent {

	public static $table = 'projects';
	public static $timestamps = true;
	

	/**********************************************************
	 * Methods to use with loaded Project
	 **********************************************************/

	/**
	* Generate a URL for the active project
	*
	* @param  string  $url
	* @return string
	*/
	public function to($url = '') {
		return URL::to('project/' . $this->id . (($url) ? '/'. $url : ''));
	}

	/**
	* Returns all issues related to project
	*
	* @return mixed
	*/
	public function issues() {
		return $this->has_many('Project\Issue', 'project_id')->order_by('status', 'DESC')->order_by('weight', 'ASC');
	}
	
	
	public function nextissuesThisTab($tag_id, $thisCount, $NbIssues) {
		if ($thisCount > $NbIssues || 1==1) {
			$rendu = 0;
			$col = 0;
			if ($thisCount >= $NbIssues) {
				echo '&nbsp;&nbsp;&nbsp;'; 
				while ($rendu < $thisCount) {
					echo '<span class="smallNum" onclick="javascript: AffichonsAutres('.$col.', '.($rendu-0).');" sytle="cursor: url();" >'.(($rendu/$NbIssues)+1).'</span>&nbsp;&nbsp;';
					if (((($rendu+$NbIssues)/$NbIssues)/6) == round((($rendu+$NbIssues)/$NbIssues)/5)) { echo '<br />&nbsp;&nbsp;&nbsp;'; }
					$rendu = $rendu + $NbIssues;
				}
			}
		}  
	}


	/**
	* Assign a user to a project
	*
	* @param  int   $user_id
	* @param  int   $role_id
	* @return void
	*/
	public function assign_user($user_id, $role_id = 0) {
		if ($role_id == 0) {
			$role_id = \USER::where('id', '=', $user_id)->get(array('role_id'));
		}
		Project\User::assign($user_id, $this->id, $role_id);
	}

	public function users() {
		return $this->has_many_and_belongs_to('\User', 'projects_users', 'project_id', 'user_id');
	}

	public function users_not_in() {
		$users = array();

		foreach($this->users()->get(array('user_id')) as $user) { $users[] = $user->id; }
		$results = User::where('deleted', '=', 0);
		if(count($users) > 0) { $results->whereNotIn('id', $users); }

		return $results->get();
	}

	/**
	* Counts the project's issues assigned to the given user
	*
	* @param  int  $user_id
	* @return int
	*/
	public function count_assigned_issues($user_id = null) {
		if(is_null($user_id)) {
			$user_id = \Auth::user()->id;
		}
		return \DB::table('projects_issues')
				->where('project_id', '=', $this->id)
				->where('assigned_to', '=', $user_id)
//				->where('start_at', '<=', date("Y-m-d"))
				->where_null('closed_at', 'and', false)
				->count();
	}

	public function count_open_issues() {
		return \DB::table('projects_issues')
				->where('project_id', '=', $this->id)
				->where('start_at', '<=', date("Y-m-d"))
				->where_null('closed_at', 'and', false)
				->count();
	}

	public function count_closed_issues() {
		return \DB::table('projects_issues')
				->where('project_id', '=', $this->id)
				->where_null('closed_at', 'and', true)
				->count();
 	}
	public function count_future_issues() {
		return \DB::table('projects_issues')
				->where('project_id', '=', $this->id)
				->where('start_at', '>', date("Y-m-d"))
				->where_null('closed_at', 'and', false)
				->count();
 	}
	public function count_project_activity() {
 			return User\Activity::where('parent_id', '=', $this->id)->count();
 	}
 	
	/**
	* Select activity for a project
	*
	* @param  int    $activity_limit
	* @return array
	*/
	public function activity($activity_limit, $debut = 1) {
		$users = $issues = $comments = $activity_type = array();

		/* Load the activity types */
		foreach(Activity::all() as $row) {
			$activity_type[$row->id] = $row;
		}

		/* Loop through all the logic from the project and cache all the needed data so we don't load the same data twice */
		$project_activity = User\Activity::where('parent_id', '=', $this->id)
			->order_by('created_at', 'DESC')
			->take($activity_limit)
			->for_page($debut, $activity_limit)
			->get();

		if(!$project_activity) {
			return null;
		}

		foreach($project_activity as $activity) {
			if(!isset($issues[$activity->item_id])) {
				$issues[$activity->item_id] = Project\Issue::find($activity->item_id);
			}

			if(!isset($users[$activity->user_id])) {
				$users[$activity->user_id] = User::find($activity->user_id);
			}

			if(!isset($comments[$activity->action_id])) {
				$comments[$activity->action_id] = Project\Issue\Comment::find($activity->action_id);
			}

			if($activity->type_id == 5) {
				if(!isset($users[$activity->action_id])) {
					if($activity->action_id > 0) {
						$users[$activity->action_id] =  User::find($activity->action_id);
					} else {
						$users[$activity->action_id] = array();
					}
				}
			}
		}

		/* Loop through the projects and activity again, building the views for each activity */
		$return = array();

		foreach($project_activity as $row) {
			switch($row->type_id) {
				case 2:
					$return[] = View::make('activity/' . $activity_type[$row->type_id]->activity, array(
						'issue' => $issues[$row->item_id],
						'project' => $this,
						'user' => $users[$row->user_id],
						'comment' => $comments[$row->action_id],
						'activity' => $row
					));
					break;

				case 5:
					$return[] = View::make('activity/' . $activity_type[$row->type_id]->activity, array(
						'issue' => $issues[$row->item_id],
						'project' => $this,
						'user' => $users[$row->user_id],
						'assigned' => $users[$row->action_id],
						'activity' => $row
					));
					break;

				case 6:
					if ($row->data === NULL) { break; }
					$tag_diff = json_decode($row->data, true);
					$tag_diff['added_tags'] = $tag_diff['added_tags'] ?? array();
					$tag_diff['removed_tags'] = $tag_diff['removed_tags'] ?? array();
					$return[] = View::make('activity/' . $activity_type[$row->type_id]->activity, array(
						'issue' => $issues[$row->item_id],
						'project' => $this,
						'user' => $users[$row->user_id],
						'tag_diff' => $tag_diff,
						'tag_counts' => array('added' => sizeof($tag_diff['added_tags']), 'removed' => sizeof($tag_diff['removed_tags'])),
						'activity' => $row
					));
					break;

				case 8:	//Move ticket from project A to project B
					if ($row->data === NULL) { break; }
					$tag_diff = json_decode($row->data, true);
					$return[] = View::make('ChangeIssue-project_acti', array(
						'issue' => $issues[$row->item_id],
						'user' => $users[$row->user_id],
						'activity' => $row
					));
					break;

				default:
					$return[] = View::make('activity/' . $activity_type[$row->type_id]->activity, array(
						'issue' => $issues[$row->item_id],
						'project' => $this,
						'user' => $users[$row->user_id],
						'activity' => $row
					));

					break;
			}
		}

		return $return;
	}

	/******************************************************************
	 * Static methods for working with projects
	 ******************************************************************/

	/**
	 * Current loaded Project
	 *
	 * @var Project
	 */
	private static $current = null;

	/**
	* Return the current loaded Project object
	*
	* @return Project
	*/
	public static function current() {
		return static::$current;
	}

	/**
	* Load a new Project into $current, based on the $id
	*
	* @param   int  $id
	* @return  void
	*/
	public static function load_project($id) {
		static::$current = static::find($id);
	}

	/**
	* Create a new project
	*
	* @param  array  $input
	* @return array
	*/
	public static function create_project($input) {
		$rules = array(
			'name' => 'required|max:250'
		);

		$validator = \Validator::make($input, $rules);

		if($validator->fails()) {
			return array(
				'success' => false,
				'errors' => $validator->errors
			);
		}

		$id_max = $val_max = 0;
		if(isset($input['user']) && count($input['user']) > 0) {
			foreach($input['user'] as $ind => $id) {
				$id_max = ($input["role"][$ind] > $val_max) ? $id : $id_max;
			}
		}

		$fill = array(
			'name' => $input['name'],
			'default_assignee' => $id_max,
		);

		$project = new Project;
		$project->fill($fill);
		$project->save();

		/* Assign selected users to the project */
		$id_max = 0;
		$val_max = 0;
		if(isset($input['user']) && count($input['user']) > 0) {
			foreach($input['user'] as $ind => $id) {
				$id_max = ($input["role"][$ind] > $val_max) ? $id : $id_max;
				$project->assign_user($id, $input["role"][$ind]);
			}
		}
		return array(
			'id' => $project->attributes["id"],
			'project' => $project,
			'success' => true
		);
	}
	
	/**
	* Update a project
	*
	* @param array     $input
	* @param \Project  $project
	* @return array
	*/
	public static function update_project($input, $project) {
		$rules = array(
			'name' => 'required|max:250'
		);

		$validator = \Validator::make($input, $rules);

		if($validator->fails()) {
			return array(
				'success' => false,
				'errors' => $validator->errors
			);
		}

		$fill = array(
			'name' => $input['name'],
			'status' => $input['status'],
			'default_assignee' => $input['default_assignee'],
		);

		$project->fill($fill);
		$project->save();

		return array(
			'success' => true
		);
	}

	public static function update_weblnks($input, $project) {
		/* Update all the links attached to the project, setting the « desactivated » date as NOW */
		\DB::table('projects_links')->where('id_project', '=', $project->id)->update(array('desactivated' => date("Y-m-d")));

		/* Insert new values, setting the passed due date as NOW */
		if (trim($input['Dev']) != '' ) { \DB::table('projects_links')->insert(array('id_project' => $project->id, 'category' => 'dev', 'link' => $input['Dev'], 'created' => date("Y-m-d"))); }
		if (trim($input['Git']) != '' ) { \DB::table('projects_links')->insert(array('id_project' => $project->id, 'category' => 'git', 'link' => $input['Git'], 'created' => date("Y-m-d"))); }
		if (trim($input['Prod']) != '' ) { \DB::table('projects_links')->insert(array('id_project' => $project->id, 'category' => 'prod', 'link' => $input['Prod'], 'created' => date("Y-m-d"))); }
	}

	/**
	* Delete a project and it's children
	*
	* @param  Project  $project
	* @return void
	*/
	public static function delete_project($project) {
		$id = $project->id;
		$project->delete();

		/* Delete all children from the project */
		Project\Issue::where('project_id', '=', $id)->delete();
		Project\Issue\Comment::where('project_id', '=', $id)->delete();
		Project\User::where('project_id', '=', $id)->delete();
		User\Activity::where('parent_id', '=', $id)->delete();
	}

}
