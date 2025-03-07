<?php

class Home_Controller extends Base_Controller {

	public function get_index() {
		return $this->layout->with('active', 'dashboard')->nest('content', 'activity.dashboard');
	}

	public function post_new() {
		//Projet
		\DB::table('projects')->insert(array(
			'id'=>1, 
			'name'=>$_POST["projectName"],
			'status'=>1, 
			'created_at'=>date("Y-m-d H:i:s"),
			'updated_at'=>date("Y-m-d H:i:s"),
			'default_assignee'=>1
		));
		//Issue
		\DB::table('projects_issues')->insert(array(
			'id'=>1, 
			'project_id'=>1,
			'title'=>$_POST["ticketName"],
			'body'=>$_POST["body"],
			'status'=>3, 
			'created_at'=>date("Y-m-d"),
			'start_at'=>date("Y-m-d")
		));
		//projects_users
		\DB::table('projects_users')->insert(array(
			'id'=>1, 
			'user_id'=>1,
			'project_id'=>1,
			'role_id'=>4,
			'created_at'=>date("Y-m-d")
		));
		//tags
		\DB::table('projects_issues_tags')->insert(array(
			'id'=>1, 
			'issue_id'=>1,
			'tag_id'=>1,
			'created_at'=>date("Y-m-d")
		));
		//Activity
		\DB::table('users_activity')->insert(array(
			'id'=>1, 
			'user_id'=>1,
			'parent_id'=>1,
			'item_id'=>1,
			'action_id'=>1,
			'type_id'=>1,
			'created_at'=>date("Y-m-d")
		));
		//Users_todos
		\DB::table('users_todos')->insert(array(
			'id'=>1, 
			'issue_id'=>1,
			'user_id'=>1,
			'status'=>3,
			'created_at'=>date("Y-m-d")
		));
		//Automatically enrole project's followers into following this issue and the assignee
		\DB::query("INSERT INTO following (user_id, project_id, issue_id, project, attached, tags ) VALUES (1, 1, 1, 1, 0, 1)");

		return Redirect::to("/")->with('notice', __('tinyissue.issue_has_been_created'));
	}
}
