<?php namespace Project;

use Application\librairies\mail;

class Issue extends \Eloquent {

	public static $table = 'projects_issues';
	public static $timestamps = true;

	/******************************************************************
	* Methods to use against loaded project
	******************************************************************/

	/**
	* @return User
	*/
	public function user() {
		return $this->belongs_to('\User', 'created_by');
	}

	/**
	* @return User
	*/
	public function assigned() {
		return $this->belongs_to('\User', 'assigned_to');
	}

	/**
	* @return User
	*/
	public function updated() {
		return $this->belongs_to('\User', 'updated_by');
	}

	/**
	* @return User
	*/
	public function closer() {
		return $this->belongs_to('\User', 'closed_by');
	}

	/**
	* @return Collection
	*/

	public function tags() {
		return $this->has_many_and_belongs_to('\Tag', 'projects_issues_tags', 'issue_id', 'tag_id');
	}

	public function activity($activity_limit = 5) {

		$users = $comments = $activity_type = array();

		$issue = $this;
		$project_id = $this->project_id;
		$project = \Project::find($project_id);

		foreach(\Activity::all() as $row) {
			$activity_type[$row->id] = $row;
		}


		$activities = array();


		foreach(\User\Activity::where('item_id', '=', $issue->id)->order_by('created_at', 'ASC')->get() as $activity) {
			$activities[] = $activity;

			switch($activity->type_id) {
				case 2:
					if(!isset($users[$activity->user_id])) 		{ $users[$activity->user_id] = \User::find($activity->user_id); }
					if(!isset($comments[$activity->action_id]))	{ $comments[$activity->action_id] = \Project\Issue\Comment::find($activity->action_id); }
					break;

				case 5:
					if(!isset($users[$activity->user_id]))		{ $users[$activity->user_id] = \User::find($activity->user_id); }
					if(!isset($users[$activity->action_id])) 	{ $users[$activity->action_id] = \User::find($activity->action_id); }
					break;

				case 7:
					if(!isset($users[$activity->user_id]))		{ $users[$activity->user_id] = \User::find($activity->user_id); }
					if(!isset($users[$activity->action_id])) 	{ $users[$activity->action_id] = \User::find($activity->action_id); }
					break;

				default:
					if(!isset($users[$activity->user_id])) 	{ $users[$activity->user_id] = \User::find($activity->user_id); }
					break;
			}
		}

		/* Loop through the projects and activity again, building the views for each activity */
		$return = array();
		foreach($activities as $row) {
			switch($row->type_id) {
				case 2:
					//using project/issue/activity/comment.php
					//according to db table activity, field activity's value for id = 2
					$return[] = \View::make('project/issue/activity/' . $activity_type[$row->type_id]->activity, array(
						'issue' => $issue,
						'project' => $project,
						'user' => $users[$row->user_id],
						'comment' => $comments[$row->action_id],
						'activity' => $row
					));
					break;

				case 3:
					//using project/issue/activity/close-issue.php
					//according to db table activity, field activity's value for id = 3 
					$return[] = \View::make('project/issue/activity/' . $activity_type[$row->type_id]->activity, array(
						'issue' => $issue,
						'project' => $project,
						'user' => $users[$row->user_id],
						'activity' => $row
					));
					break;

				case 5:
					//using project/issue/activity/reassing-issue.php
					//according to db table activity, field activity's value for id = 5 
					$return[] = \View::make('project/issue/activity/' . $activity_type[$row->type_id]->activity, array(
						'issue' => $issue,
						'project' => $project,
						'user' => $users[$row->user_id],
						'assigned' => $users[$row->action_id],
						'activity' => $row
					));
					break;

				case 6:
					//using project/issue/activity/update-issue-tags.php
					//according to db table activity, field activity's value for id = 6
					if ($row->data === NULL) { break; }
					$tag_diff = json_decode($row->data, true);
					if (strlen($row->data) > 10) { 
						$prem = strpos($row->data, "[");
						$pren = strpos($row->data, "]");
						$deux = strpos($row->data, "[", $prem+1);
						$deuy = strpos($row->data, "]", $deux);
						$valadd = trim(substr($row->data, $prem+1, ($pren-$prem)-1));
						$numtag = ($valadd != '') ? $valadd : trim(substr($row->data, $deux+1, ($deuy-$deux)-1));
								$tag_info = \DB::table('tags')->where('id', '=', $numtag)->get();
								$return[] = \View::make('project/issue/activity/' . $activity_type[$row->type_id]->activity, array(
									'issue' => $issue,
									'project' => $project,
									'user' => $users[$row->user_id],
									'tag_diff' => $tag_diff,
									'bgcolor' => (isset($tag_info[0]->bgcolor) ? $tag_info[0]->bgcolor : 'green'),
									'ftcolor' => (isset($tag_info[0]->ftcolor) ? $tag_info[0]->ftcolor : 'black'),
									'activity' => $row
								));
					}
					break;
				case 8:
					//using project/issue/activity/ChangeIssue-project.php
					$contenuChangeIssueProject = $row;
					$return[] = \View::make('ChangeIssue-project', array(
						'user' => $users[$row->user_id],
						'activity' => $row
					));
					break;
				case 9:
					//using project/issue/activity/following
					//according to db table activity, field activity's value for id = 9 
					if ($row->data === NULL) { break; }
					$tag_diff = json_decode($row->data, true);
					$return[] = \View::make('Follow', array(
						'issue' => $issue,
						'project' => $project,
						'user' => $users[$row->user_id],
						'activity' => $row
					));
					break;
				case 10:
					//using project/issue/activity/IssueEdit.php
					//according to db table activity, field activity's value for id = 10 
					if ($row->data === NULL) { break; }
					$tag_diff = json_decode($row->data, true);
					$return[] = \View::make('IssueEdit', array(
						'issue' => $issue,
						'project' => $project,
						'user' => $users[$row->user_id],
						'start_at' => $issue->start_at,
						'activity' => $row
					));
					break;
				default:
					$return[] = \View::make('project/issue/activity/' . $activity_type[$row->type_id]->activity, array(
						'issue' => $issue,
						'project' => $project,
						'user' => $users[$row->user_id],
						'activity' => $row
					));
					break;
			}
		}
		return $return;

	}

	public function comments() {
		return $this->has_many('Project\Issue\Comment', 'issue_id')
			->order_by('created_at', 'ASC');
	}

	public function comment_count() {
		return $this->has_many('Project\Issue\Comment', 'issue_id')->count();
	}

	public function attachments() {
		return $this->has_many('Project\Issue\Attachment', 'issue_id')->where('comment_id', '=', 0);
	}

	/**
	* Generate a URL for the active project
	*
	* @param  string  $url
	* @return string
	*/
	public function to($url = '') {
		return \URL::to('project/' . $this->project_id . '/issue/' . $this->id . (($url) ? '/'. $url : ''));
	}


	/**
	* Change the status of an issue
	*
	* @param  int   $status
	* @return void
	*/
	public function change_status($status) {
		/* Retrieve all tags */
		$tags = $this->tags;
		$tag_ids = array();
		foreach($tags as $tag) {
			$tag_ids[$tag->id] = $tag->id;
		}

		if($status == 0) {
			$this->closed_by = \Auth::user()->id;
			$this->closed_at = date('Y-m-d H:i:s');

			/* Update tags */
			$tag_ids[2] = 2;
			if(isset($tag_ids[1])) { unset($tag_ids[1]); }
			if(isset($tag_ids[8])) { unset($tag_ids[8]); }

			/* Add to activity log */
			\User\Activity::add(3, $this->project_id, $this->id);
			//$text = __('tinyissue.following_email_status_bis').__('email.closed').'.<br /><br />'.$text;
//			$this->Courriel ('Issue', true, \Project::current()->id, $this->id, \Auth::user()->id, array('closed','status','status_bis'), array('tinyissue','tinyissue','tinyissue'));
			\Mail::letMailIt(array(
				'ProjectID' => \Project::current()->id, 
				'IssueID' => $this->id, 
				'SkipUser' => true,
				'Type' => 'Issue', 
				'user' => \Auth::user()->id,
				'contenu' => array('closed','status','status_bis'),
				'src' => array('tinyissue','tinyissue','tinyissue')
				),
				\Auth::user()->id, 
				\Auth::user()->language
			);

		} else {
			$this->closed_by = NULL;
			$this->closed_at = NULL;

			/* Update tags */
			$tag_ids[1] = 1;
			if(isset($tag_ids[2])) {
				unset($tag_ids[2]);
			}

			/* Add to activity Log */
			\User\Activity::add(4, $this->project_id, $this->id);
			//Notify all followers about the new status
//			$this->Courriel ('Issue', true, \Project::current()->id, $this->id, \Auth::user()->id, array('reopened','status','status_bis'), array('tinyissue','tinyissue','tinyissue'));
			\Mail::letMailIt(array(
				'ProjectID' => \Project::current()->id, 
				'IssueID' => $this->id, 
				'SkipUser' => true,
				'Type' => 'Issue', 
				'user' => \Auth::user()->id,
				'contenu' => array('reopened','status','status_bis'),
				'src' => array('tinyissue','tinyissue','tinyissue')
				),
				\Auth::user()->id, 
				\Auth::user()->language
			);
			
		}
		$this->tags()->sync($tag_ids);
		$this->status = $status;
		$this->save();
	}

	/**
	* Update the given issue
	*
	* @param  array  $input
	* @return array
	*/
	public function update_issue($input) {
		$rules = array(
			'title' => 'required|max:200',
			'body' => 'required'
		);
		$validator = \Validator::make($input, $rules);

		if($validator->fails()) 	{
			return array(
				'success' => false,
				'errors' => $validator->errors
			);
		}

		$input['duration'] = $input['duration'] ?? 30;
		$input['start_at'] = (trim($input['start_at']) == '') ? date("Y-m-d") : $input['start_at'];
		$fill = array(
			'title' => $input['title'],
			'body' => $input['body'],
			'assigned_to' => $input['assigned_to'],
			'duration' => $input['duration'],
			'status' => $input['status'],
			'start_at' => $input['start_at']
		);
		\User\Activity::add(10, NULL, $this->id);

		/* Add to activity log for assignment if changed */
		if($input['assigned_to'] != $this->assigned_to) {
			\User\Activity::add(5, NULL, $this->id, $input['assigned_to']);
//			$this->Courriel ('Issue', true, \Project::current()->id, $this->id, \Auth::user()->id, array('assigned'), array('tinyissue'));
			\Mail::letMailIt(array(
				'ProjectID' => \Project::current()->id, 
				'IssueID' => $this->id, 
				'SkipUser' => true,
				'Type' => 'Issue', 
				'user' => \Auth::user()->id,
				'contenu' => array('assigned'),
				'src' => array('tinyissue')
				),
				\Auth::user()->id, 
				\Auth::user()->language
			);
		}
		$this->fill($fill);
		$this->save();

		/* Update tags */
		$this->set_tags('update');

		return array(
			'success' => true
		);
	}

	/**
	* Sets tags on an issue
	*
	* @param  string	Mode (create or update)
	* @return void
	*/
	public function set_tags($mode) {
		if ($mode == 'create') {
			/* Set old tag ids to contain only the status:open tag */
			$old_tag_ids = array(1);
		} else {
			/* Save old tags to determine if we need to record activity */
			$old_tag_ids = array();
			foreach($this->tags as $tag) {
				$old_tag_ids[] = $tag->id;
			}
		}

		/* Update tags */
		$tags = \Input::get('tags', '');
		$new_tag_ids = array();

		if(trim($tags) != '') {
			$tags = explode(',', $tags);

			/* Only users with administration permission should be able to add new tags */
			if(\Auth::user()->permission('administration')) {
				foreach($tags as $tag) {
					if (!\Tag::where('tag', '=', $tag)->first()) {
						$tag_object = new \Tag;
						$tag_object->fill(array('tag' => $tag));
						$tag_object->save();
					}
				}
			}

			$tag_records = \Tag::where_in('tag', $tags)->get();
			foreach($tag_records as $tag_record) {
				$new_tag_ids[] = $tag_record->id;
			}
		}

		//if ($this->status == 1) {
		if ($this->status > 0) {
			$force_tag = 1;
			$exclude_tag = 2;
		} else {
			$force_tag = 2;
			$exclude_tag = 1;
		}

		$found_tag = false;
		foreach($new_tag_ids as $key => $val) {
			if($val == $force_tag) {
				$found_tag = true;
			} else if($val == $exclude_tag) {
				unset($new_tag_ids[$key]);
			}
		}

		if (!$found_tag) {
			$new_tag_ids[] = $force_tag;
		}

		$this->tags()->sync($new_tag_ids);

		/* Add to activity log for tags if changed */
		$added_tags = array_diff($new_tag_ids, $old_tag_ids);
		$removed_tags = array_diff($old_tag_ids, $new_tag_ids);
		$has_tag_diff = (!empty($added_tags) || !empty($removed_tags));
		if ($has_tag_diff) {
			$tag_data = array();
			$tag_data_resource = \Tag::where_in('id', array_merge($added_tags, $removed_tags))->get();
			foreach($tag_data_resource as $tag) {
				$tag_data[$tag->id] = $tag->to_array();
			}
			
			//17 octobre 2021, j'annule ici la fonction de commentaire dans l'historique (relativement aux étiquettes), car ça alourdit beaucoup la présentation et l'historique du billet
			////Les six lignes ci-bas font fonctionnelles et remplacent la méthode initiale qui créaient des soucis d'affichage.
			////Les trois lignes ci-bas indiquent l'ajout d'étiquettes (lors de l'édition ou la création d'un billet)
//			foreach($added_tags as $tag) {
//				\User\Activity::add(6, $this->project_id, $this->id, null, '{"added_tags":['.$tag.'],"removed_tags":[],"tag_data":{"'.$tag.'":{"id":'.$tag.',"tag":"Test: un ami en veux aux renards roux","bgcolor":"#001f80","ftcolor":"#7dd6dd"}},"Creation billet":"Nous sommes ici en ligne 411 de project issue.php"}');
//			}
//			////Les trois lignes ci-bas indiquent la suppresion d'étiquettes (lors de l'édition)
//			foreach($removed_tags as $tag) {
//				\User\Activity::add(6, $this->project_id, $this->id, null, '{"added_tags":[],"removed_tags":['.$tag.'],"tag_data":{"'.$tag.'":{"id":'.$tag.',"tag":"Test: un ami en veux aux renards roux","bgcolor":"#001f80","ftcolor":"#7dd6dd"}},"Creation billet":"Nous sommes ici en ligne 411 de project issue.php"}');
//			}
		}
	}


	/******************************************************************
	* Static methods for working with issues
	******************************************************************/

	/**
	* Current loaded Issue
	*
	* @var Issue
	*/
	private static $current = null;

	/**
	* Return the current loaded Issue
	*
	* @return Issue
	*/
	public static function current() {
		return static::$current;
	}

	/**
	* Load a new Issue into $current, based on the $id
	*
	* @param   int   $id
	* @return  Issue
	*/
	public static function load_issue($id) {
		static::$current = static::find($id);
		return static::$current;
	}

	/**
	* Create a new issue
	*
	* @param  array    $input
	* @param  \Project  $project
	* @return Issue
	*/
	public static function create_issue($input, $project) {
		$rules = array(
			'title' => 'required|max:200',
			'body' => 'required'
		);

		$validator = \Validator::make($input, $rules);

		if($validator->fails()) {
			return array(
				'success' => false,
				'errors' => $validator->errors
			);
		}

		//Create the new issue into database
		$input['duration'] = $input['duration'] ?? 30;
		$input['start_at'] = $input['start_at'] ?? date("Y-m-d");
		$fill = array(
			'created_by' => \Auth::user()->id,
			'project_id' => $project->id,
			'title' => $input['title'],
			'body' => $input['body'],
			'duration' => $input['duration'],
			'start_at' => $input['start_at'],
			'temps_plan' => $input['temps_plan'],
			'status' => $input['status'],
			'assigned_to' => ( $input['assigned_to'] == NULL || $input['assigned_to'] < 1) ? \Auth::user()->id : $input['assigned_to']
		);

		if(\Auth::user()->permission('issue-modify')) {
			$issue = new static;
			$issue->fill($fill);
			$issue->save();
		} else {
			return false;
		}

		/* Create tags */
		$issue->set_tags('create');

		/* Add to user's activity log */
		\User\Activity::add(1, $project->id, $issue->id);

		/* Add attachments to issue */
			//Step 1 : Catch all files' names
			$url =\URL::home();
			$url ="../";
			$chemin = $url."uploads/New/".\Auth::user()->id."/";

			if (file_exists($chemin)) {
				$attacheds = scandir($chemin);
				$attached = array();
				$content = "";
				$rendu = 0;
				if (count($attacheds) >2) {
					foreach ($attacheds as $filename) {
						if (!in_array($filename , array(".", ".."))) { 
							$attached[$rendu] = $filename;
							$filesize[$rendu] = filesize($chemin.$filename);
							$fileextn[$rendu] = substr($filename, strrpos($filename, ".")+1);
							$content .= '<a href="'.$url.$issue->id.'/'.$filename.'" target="_blank">'.$filename.'</a><br />';
							$rendu = $rendu + 1; 
						}
					}
				}

				//Step 2: Create a first comment to this issue
				$fill = array(
					'created_by' => \Auth::user()->id,
					'project_id' => $project->id,
					'issue_id' => $issue->id,
					'comment' => $content,
					'created_at' => date("Y-m-d H:i:s"),
					'updated_at' => date("Y-m-d H:i:s")
				);
				$comment_id = \DB::table('projects_issues_comments')->insert_get_id($fill);

				$content = str_replace(date("Ymd"."_"), $comment_id."_", $content);
				\DB::table('projects_issues_comments')->where('id', '=', $comment_id)->update(array('comment' => $content));
	
				//Step 3 : move files from /uploads/New/id_user/date_ to /uplaods/id_issue/id_comment_
				////Prepare the sub-directory for files
				$newDir = $url."uploads/".$issue->id;
				if (!file_exists($newDir)) { mkdir($newDir); }
				////Moving files themselves
				foreach ($attached as $ind => $filename) {
					$nouvNom = str_replace(date("Ymd")."_", $comment_id."_", $filename);
					copy($chemin.$filename, $url."uploads/".$issue->id."/".$nouvNom);
					unlink($url."uploads/New/".\Auth::user()->id."/".$filename);

					////Create info into project_issues_attachments table
					$attached_id = \DB::table('projects_issues_attachments')->insert_get_id(array(
						'issue_id'=>$issue->id,
						'comment_id'=>$comment_id,
						'uploaded_by'=>\Auth::user()->id,
						'filesize'=>$filesize[$ind],
						'filename'=>"uploads/".$issue->id."/".$nouvNom,
						'fileextension'=>$fileextn[$ind],
						'upload_token'=>time(),
						'created_at'=>date("Y-m-d H:i:s"),
						'updated_at'=>date("Y-m-d H:i:s")
					));
					\User\Activity::add(7, $project->id, $issue->id, $attached_id, $nouvNom);
				}
			}
		/* End of Add attachments to issue */


		/* Return success and issue object */
		return array(
			'success' => true,
			'issue' => $issue
		);
	}

	public static function count_issues() {
		/* Count Open Issues - Project must be open */
		$count = \DB::table('projects_issues')
									->join('projects', 'projects.id', '=', 'projects_issues.project_id')
									->where('projects.status', '=', 1)
									->where('projects_issues.status', '>', 1)
									->count();
		$open_issues = !$count ? 0 : $count;

		/* Count Closed Issues - Open Projects */
		$count = \DB::table('projects_issues')
								->join('projects', 'projects.id', '=', 'projects_issues.project_id')
								->where('projects.status', '=', 1)
								->where('projects_issues.status', '<', 2)
								->count();
		$closed_issues_open_project = !$count ? 0 : $count;

		/* Count Issues - Closed Projects */
		$count = \DB::table('projects_issues')
								->join('projects', 'projects.id', '=', 'projects_issues.project_id')
								->where('projects.status', '=', 0)
								->count();
		$issues_closed_project = !$count ? 0 : $count;
		$closed_issues = ($closed_issues_open_project + $issues_closed_project);

		return array(
			'open' => $open_issues,
			'closed' => $closed_issues
		);
	}

	private function Courriel ($Type, $SkipUser, $ProjectID, $IssueID, $User, $contenu, $src) {
		include_once "application/controllers/ajax/SendMail.php";
	}
}
