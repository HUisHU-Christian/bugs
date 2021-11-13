<?php namespace Project;

class User extends \Eloquent {

	public static $table  = 'projects_users';

	/**********************************************************
	* Methods to use with loaded User
	**********************************************************/

	/**
	* @return User
	*/
	public function user() {
		return $this->belongs_to('User', 'user_id')->order_by('firstname', 'ASC');
	}

	/**
	* @return Project
	*/
	public function project() {
		return $this->belongs_to('Project', 'project_id')->order_by('name', 'ASC');
	}

	/******************************************************************
	* Static methods for working with Users on a Project
	******************************************************************/

	/**
	* Assign a user to a project with a role
	*
	* @param  int   $user_id
	* @param  int   $project_id
	* @param  int   $role_id
	* @return void
	*/
	public static function assign($user_id, $project_id, $role_id = 0) {
		if(!static::check_assign($user_id, $project_id)) {
			$fill = array(
				'user_id' => $user_id,
				'project_id' => $project_id,
				'role_id' => $role_id
			);

			$relation = new static;
			$relation->fill($fill);
			$relation->save();
		}
	}
	
	public static function GetRole($project_id) {
		$MonRole =  \DB::table('projects_users')->where('user_id', '=', \Auth::user()->id)
				->where('project_id', '=', $project_id)
				->get(array('role_id'));
				
		return $MonRole[0]->role_id;
	}
	
	public static function MbrProj($user_id, $project_id) {
		$resu = false;
		if (is_null($user_id)) { $user_id = \Auth::user(); }
		if (is_null($project_id)) { $project_id = Project::current()->id; }
		foreach (static::active_projects() as $row) {
			if($row->original["id"] == $project_id) { $resu =  true;  break;}
		}
		return $resu;
	}

	/**
	 * Removes a user from a project
	 *
	 * @param  int   $user_id
	 * @param  int   $project_id
	 * @return void
	 */
	public static function remove_assign($user_id, $project_id)
	{
		static::where('user_id', '=', $user_id)
			->where('project_id', '=', $project_id)
			->delete();
	}

	/**
	 * Checks to see if a user is assigned to a project
	 *
	 * @param  int   $user_id
	 * @param  int   $project_id
	 * @return bool
	 */
	public static function check_assign($user_id, $project_id) {
		return (bool) static::where('user_id', '=', $user_id)
				->where('project_id', '=', $project_id)
				->first(array('id'));
	}

	/**
	 * Changes the role of an user for a project
	 *
	 * @param  int   $user_id
	 * @param  int   $role_id
	 * @param  int   $project_id
	 * @return bool
	 */
	public static function change_role($user_id, $role_id, $project_id) {
		$resu = \DB::table('projects_users')->where('user_id', '=', $user_id)
				->where('project_id', '=', $project_id)
				->update(array('role_id' => $role_id, 'updated_at' => date("Y-m-d H:i:s")));

		return (bool) $resu;
	}

	/**
	 * Checks the role an user for a project
	 *
	 * @param  int   $user_id
	 * @param  int   $project_id
	 * @return bool
	 */
	public static function check_role($user_id, $project_id) {
		$roles = array();
		$val = static::where('user_id', '=', $user_id)
				->where('project_id', '=', $project_id)
				->get(array('role_id'));
		if (!isset($val[0]->role_id)) { return 0; }
		$role = ($val[0]->role_id == 0) ? 4 : $val[0]->role_id;
		for ($x=1; $x<5; $x++) {
			if ($role >= $x) { $roles[] = $x; }
		}
		return $roles;
	}

	/**
	 * List of available roles for a person, list ready in select roll-up form
	 *
	 * @param  int   $user_id
	 * @param  int   $project_id
	 * @return bool
	 */
	public static function list_roles($user_id, $project_id, $userRole) {
		$role = User::check_role($user_id, $project_id);
		$liste = '<select name="roles['.$project_id.']">';
		$liste .= '<option value="0">NULL</option>';
		$roles = \Role::where('id','<=',max($role))->get(array('id', 'name'));
		foreach($roles as $ind => $val) {
			$liste .= '<option value="'.$val->id.'" '.(($val->id == $userRole) ? 'selected="selected"' : '').'>'.$val->name.'</option>';
		}
		$liste .= '</select>';
		return $liste;
	}

	/**
	* Build a dropdown of all users in the project
	*
	* @param  object  $users
	* @return array
	*/
	public static function dropdown($users) {
		$return = array();
		foreach($users as $row) {
			$return[$row->id] = $row->firstname . ' ' . $row->lastname;
		}

		return $return;
	}

	/**
	 * Returns issues assigned to the given user
	 *
	 * @param  \User  $user
	 * @return array
	 */
	public static function users_issues($user = null) {
		if(is_null($user)) {
			$user = \Auth::user();
		}

		$projects = array();

		foreach(static::active_projects(true) as $project) {
			$project = array(
				'detail' => $project,
				'issues' => \Tag::find(1)->issues()
					->where('project_id', '=', $project->id)
					->where('assigned_to', '=', $user->id)
					->order_by('status', 'DESC')
					->get()
			);

			if(count($project['issues']) > 0) {
				$projects[] = $project;
			}
		}

		return $projects;
	}

	/**
	 * Returns  active projects for the given user
	 *
	 * @param  bool   $all
	 * @param  \User  $user
	 * @return array
	 */
	public static function active_projects($all = false, $user = null) {
		if(is_null($user)) { $user = \Auth::user(); }

		if($all) {
			if($user->permission('project-all')) {
				return \Project::where('status', '=', 1)
					->order_by('name', 'ASC')
					->get();
			}
		}

		$projects = array();
		foreach(static::with('project')->where('user_id', '=', $user->id)->get() as $row) {
			if($row->project->status != 1) { continue; }
			$projects[] = $row->project;
		}

		return $projects;
	}

	/**
	 * Returns inactive projects for the given user
	 *
	 * @param  bool   $all
	 * @param  \User  $user
	 * @return array
	 */
	public static function inactive_projects($all = false, $user = null)
	{
		if(is_null($user)) {
			$user = \Auth::user();
		}

		if($all) {
			if($user->permission('project-all')) {
				return \Project::where('status', '=', 0)
					->order_by('name', 'ASC')
					->get();
			}
		}

		$projects = array();

		foreach(static::with('project')->where('user_id', '=', \Auth::user()->id)->get() as $row) {
			if($row->project->status != 0) {
				continue;
			}
			$projects[] = $row->project;
		}

		return $projects;
	}

}