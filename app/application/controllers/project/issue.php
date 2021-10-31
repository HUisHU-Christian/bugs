<?php
class Project_Issue_Controller extends Base_Controller {

	public $layout = 'layouts.project';

	public function __construct() {
		parent::__construct();

		$this->filter('before', 'project');
		$this->filter('before', 'issue')->except('new');
		$this->filter('before', 'permission:issue-modify')
				->only(array('edit_comment', 'delete_comment', 'reassign', 'retag', 'status', 'edit', 'upload', 'checkExt'));
	}

	/**
	 * Create a new issue
	 * /project/(:num)/issue/new
	 *
	 * @return View
	 */
	public function get_new() {
		Asset::add('tag-it-js', '/app/assets/js/tag-it.min.js', array('jquery', 'jquery-ui'));
		Asset::add('tag-it-css-base', '/app/assets/css/jquery.tagit.css');
		Asset::add('tag-it-css-zendesk', '/app/assets/css/tagit.ui-zendesk.css');

		return $this->layout->nest('content', 'project.issue.new', array(
			'project' => Project::current()
		));
	}


	public function post_new() {
		$issue = Project\Issue::create_issue(Input::all(), Project::current());

		if(!$issue['success']) {
			return Redirect::to(Project::current()->to('issue/new'))
				->with_input()
				->with_errors($issue['errors'])
				->with('notice-error', __('tinyissue.we_have_some_errors'));
		}

		//Automatically enrole assignee AND creator into following this issue
		$followers =\DB::query("INSERT INTO following VALUES (NULL, ".Auth::user()->id.", ".Project::current()->id.", ".$issue['issue']->id.", 0, 1, 1)");

		//Email to followers
		$this->Courriel ('Project', true, Project::current()->id, $issue['issue']->id, \Auth::user()->id, array('project'), array('tinyissue'));

		return Redirect::to($issue['issue']->to())
			->with('notice', __('tinyissue.issue_has_been_created'));
	}

	/**
	 * View an issue
	 * /project/(:num)/issue/(:num)
	 *
	 * @return View
	 */
	public function get_index() {
		/* Delete a comment */
		if(Input::get('delete') && Auth::user()->permission('issue-modify')) {
			Project\Issue\Comment::delete_comment(str_replace('comment', '', Input::get('delete')));
			return true;
		}

		return $this->layout->nest('content', 'project.issue.index', array(
			'issue' => Project\Issue::current(),
			'project' => Project::current()
		));
	}

	/**
	 * Post a comment to an issue
	 *
	 * @return Redirect
	 */
	public function post_index() {
		if(!Input::get('comment')) {
			return Redirect::to(Project\Issue::current()->to() . '#new-comment')
				->with('notice-error', __('tinyissue.you_put_no_comment'));
		}
		$comment = \Project\Issue\Comment::create_comment(Input::all(), Project::current(), Project\Issue::current());

		//Email to followers
		$this->Courriel ("Issue", true, Project::current()->id, Project\Issue::current()->id, \Auth::user()->id, array('comment'), array('tinyissue'));

		$message = __('tinyissue.your_comment_added').(((Input::get('status') == 0 || Input::get('Fermons') == 0) && \Auth::user()->role_id != 1) ? ' --- '.__('tinyissue.issue_has_been_closed') : '');
//		$retour = (Input::get('Fermons') == 0) ? '/project/'.Project::current()->id.'/issues?tag_id=1' : Project\Issue::current()->to() . '#comment' . $comment->id;   
		$retour = '/project/'.Project::current()->id.'/issues?tag_id=1';   
		return Redirect::to($retour)->with('notice', $message);
	}

	/**
	 * Edit an issue
	 *
	 * @return View
	 */
	public function get_edit() {
		$_GET["ticketAct"] = $_GET["ticketAct"] ?? '';
		if ($_GET["ticketAct"] == 'changeProject') {
			//Change the asssociation between this issue and its related project
			$ancProj = Project::current()->name;
			$msg = 0;
			$NumNew = intval(Input::get('projectNew'));
			$NumNewResp = intval(Input::get('projectNewResp'));
			if ($NumNewResp == 0) {
				$resu  = \DB::table('projects')->select(array('default_assignee'))->where('id', '=', $NumNew)->get();
				$NumResp = $resu[0];
				$NumNewResp = $resu[0];
			}
			$nomResp = \DB::table('users')->select(array('firstname','lastname'))->where('id', '=', $NumNewResp)->get();
			$nomuser = \DB::table('users')->select(array('firstname','lastname'))->where('id', '=', \Auth::user()->id)->get();

			$result  = __('tinyissue.edit_issue')." : ";
			$Modif = \DB::table('projects_issues_comments')->where('project_id', '=', intval(Input::get('projetOld')))->where('issue_id', '=', intval(Input::get('ticketNum')), 'AND')->update(array('project_id' => $NumNew, 'comment' => __('tinyissue.issue_chg_resp').' '.$nomResp[0]->firstname.' '.$nomResp[0]->lastname.'.  '.__('tinyissue.issue_chg_resp_dec').' '.$nomuser[0]->firstname.' '.$nomuser[0]->lastname.'.','created_at' => date("Y-m-d H:i:s"),'updated_at' => date("Y-m-d H:i:s")));
			$result .= ($Modif) ? "Succès" : "Échec";
			$Modif = Project\Issue::where('project_id', '=', intval(Input::get('projetOld')))->where('id', '=', intval(Input::get('ticketNum')))->update(array('project_id' => $NumNew, 'assigned_to' => $NumNewResp, 'updated_at' => date("Y-m-d H:i:s"), 'updated_by' => \Auth::user()->id));
			$result .= ($Modif) ? "Succès" : "Échec";
			if (\User\Activity::add(8, intval(Input::get('projetOld')), Input::get('ticketNum'), $NumNew, "From ".Input::get('projetOld')." to ".$NumNew )) { $msg = $msg + 1; } else { $msg = $TheFile["error"]; }

			//Email to followers
			$this->Courriel ('Issue', true, $NumNew, Project\Issue::current()->id, Auth::user()->id, array('issueproject', 'static:'.$ancProj), array('tinyissue', 'value'));

			return Redirect::to("project/".$NumNew."/issues?tag_id=1");

		} else {
			Asset::add('tag-it-js', '/app/assets/js/tag-it.min.js', array('jquery', 'jquery-ui'));
			Asset::add('tag-it-css-base', '/app/assets/css/jquery.tagit.css');
			Asset::add('tag-it-css-zendesk', '/app/assets/css/tagit.ui-zendesk.css');

			/* Get tags as string */
			$issue_tags = '';
			foreach(Project\Issue::current()->tags as $tag) {
				$issue_tags .= (!empty($issue_tags) ? ',' : '') . $tag->tag;
			}
			return $this->layout->nest('content', 'project.issue.edit', array(
				'issue' => Project\Issue::current(),
				'issue_tags' => $issue_tags,
				'project' => Project::current()
			));

			//Email to followers
			$this->Courriel ('Issue', true, Project::current()->id, Project\Issue::current()->id, Auth::user()->id, array('assigned'), array('tinyissue'));
		}
	}

	/**Update an issue
	*/
	public function post_edit() {
		$avant = Project\Issue::current()->title;
		$update = Project\Issue::current()->update_issue(Input::all());

		if(!$update['success']) {
			return Redirect::to(Project\Issue::current()->to('edit'))
				->with_input()
				->with_errors($update['errors'])
				->with('notice-error', __('tinyissue.we_have_some_errors'));
		}

		//Email to followers
		$this->Courriel ('Issue', true, Project::current()->id, Project\Issue::current()->id, Auth::user()->id, array('issue', 'static:'.$avant), array('tinyissue', 'value'));

		return Redirect::to(Project\Issue::current()->to())
			->with('notice', __('tinyissue.issue_has_been_updated'));
	}

	/**
	 * Update / Edit a comment
	 * /project/(:num)/issue/(:num)/edit_comment
	 *
	 * @request ajax
	 * @return string
	 */
	public function post_edit_comment() {
//		Project\Issue\Comment::edit_comment(Input::get('id'), Project\Issue::current()->id,Input::get('content'));

		$idComment = static::find(Input::get('id'));
		if(!$idComment) { return false; }
		$Avant = \DB::table('projects_issues_comments')->where('id', '=', Input::get('id'))->first(array('id', 'project_id', 'issue_id', 'comment', 'created_at'));
		$Avant->comment = str_replace("`", "'", $Avant->comment );
		$Avant->comment = str_replace("<li>", "&nbsp;&nbsp;&nbsp;-&nbsp;", $Avant->comment );
		$Avant->comment = str_replace("</li>", "<br />", $Avant->comment );
		$Avant->comment = str_replace("<ol>", "<br />", $Avant->comment );
		$Avant->comment = str_replace("</ol>", "<br />", $Avant->comment );
		$Avant->comment = str_replace("<ul>", "<br />", $Avant->comment );
		$Avant->comment = str_replace("</ul>", "<br />", $Avant->comment );
		$edited_id = \DB::table('users_activity')->insert_get_id(array(
						'id'=>NULL,
						'user_id'=>\Auth::user()->id,
						'parent_id'=>$Avant->project_id,
						'item_id'=>$Avant->issue_id,
						'action_id'=>Input::get('id'),
						'type_id'=>12,
						'data'=>$Avant->comment,
						'created_at'=>$Avant->created_at,
						'updated_at'=>date("Y-m-d H:i:s")
					));

		\DB::table('projects_issues_comments')->where('id', '=', Input::get('id'))->update(array('comment' => Input::get('body'), 'updated_at' => date("Y-m-d H:i:s")));

		return Redirect::to(Project\Issue::current()->to())
			->with('notice', __('tinyissue.comment_edited'));
	}

	public function get_edit_comment($id, $contenu) {
		//Project\Issue\Comment::edit_comment(Project\Issue::current()->id,Input::get('id'));
		Project\Issue\Comment::edit_comment(Input::get('id'), Project\Issue::current()->id,Input::get('content'));

		return Redirect::to(Project\Issue::current()->to())
			->with('notice', __('tinyissue.comment_edited'));
	}


	/**
	 * Delete a comment
	 * /project/(:num)/issue/(:num)/delete_comment
	 *
	 * @return Redirect
	 */
	public function get_delete_comment() {
		Project\Issue\Comment::delete_comment(Input::get('comment'));

		return Redirect::to(Project\Issue::current()->to())
			->with('notice', __('tinyissue.comment_deleted'));
	}

	/**
	 * Change the status of a issue
	 * /project/(:num)/issue/(:num)/status
	 *
	 * @return Redirect
	 */
	public function get_status() {
		$status = Input::get('status', 0);

		$message = __('tinyissue.issue_has_been_reopened');
		$messagetit = __('tinyissue.issue_has_been_reopened');
		if($status == 0) {
			$message = __('tinyissue.issue_has_been_closed');
			$messagetit = __('tinyissue.issue_has_been_closed_tit');
		}

		Project\Issue::current()->change_status($status);

		$retour = (Input::get('Fermons') == 0) ? '/project/'.Project::current()->id.'/issues?tag_id=1' : Project\Issue::current()->to();   
		return Redirect::to($retour)->with('notice', $message);

	}

	/**
		*Show the issue's tags
	**/

	private function show_tag ($Content) {
		$result = "
		<script>
			parent.document.getElementById('div_currentlyAssigned_name').style.backgroundColor = '#e8e8e8';
			parent.document.getElementById('div_currentlyAssigned_name').style.padding = '12px 10px';
			parent.document.getElementById('div_currentlyAssigned_name').style.verticalAlign = 'middle';
			parent.document.getElementById('div_currentlyAssigned_name').style.borderRadius = '6x';
			var ad = parent.document.createElement(\"SPAN\");
			var adTxt = parent.document.createTextNode(".$Content.");
			ad.appendChild(adTxt);
			parent.document.getElementById('div_currentlyAssigned_name').appendChild(ad);
		</script>
		";
		return $result;
	}


	/**
	 * Check if an extension file icon exists
	 * /project/(:num)/issue/index
	 *
	 * @request ajax
	 * @return string
	 */
	public function get_checkExt() {
		return (file_exists("../app/assets/images/upload_type/".strtolower(Input::get('ext').".png"))) ? "yes" : "non";
	}


	/**
	 * Change an issue's assignation
	 * /project/(:num)/issue/index
	 *
	 * @request ajax
	 * @return string
	 */
	public function get_reassign() {
		if (Input::get('Suiv') == 0 ) { $result = false; } else {
			//Let note that into the issue table
			$result  = __('tinyissue.edit_issue')." : ";
			$Modif = Project\Issue::where('id', '=', Input::get('Issue'))->update(array('assigned_to' => Input::get('Suiv')));
			$result .= ($Modif) ? "Succès" : "Échec";

			//Let note that into the activity table so it cans show the reassigning history
			$result .= "\\n";
			$result .= __('tinyissue.activity')." : ";
			$Modif = (\User\Activity::add(5, Input::get('Prec'), Input::get('Issue'), Input::get('Suiv') ));
			$result .= ($Modif) ? "Succès" : "Échec";

			//Search for new assignee infos
			$Who = \User::where('id', '=', Input::get('Suiv') )->get(array('firstname','lastname','email'));
			$WhoName = $Who[0]->attributes["firstname"].' '.$Who[0]->attributes["lastname"];
			$WhoAddr = $Who[0]->attributes["email"];
			$thisIssue = \Project\Issue::where('id', '=', Input::get('Issue'))->get('*');
			$Issue_title = $thisIssue[0]->attributes["title"];
			$Project = \Project::where('id', '=', $thisIssue[0]->attributes["project_id"])->get(array('id', 'name'));
			$project = \Project::find($Project[0]->attributes["id"]);

			if ($Modif) {  //Send mail to the new assignee
				$text  = __('tinyissue.following_email_assigned').' « '.$thisIssue[0]->attributes["title"].' » ( '.__('tinyissue.on_project').' « '.$Project[0]->attributes["name"].' » )';
				$text .= "<br /><br />";
				$text .= __('email.reassigned_by').' '.\Auth::user()->firstname.' '.\Auth::user()->lastname.'.';
				$text .= "<br />";
				$text .= __('tinyissue.assigned_to').' '.$WhoName.'.';
				$text .= "<br /><br />";
				//$this->Courriel ('Issue', true, Project::current()->id, Project\Issue::current()->id, Auth::user()->id, $text, __('tinyissue.following_email_assigned_tit'));
				//2 sept 2021 : ceci bloque les fonctions javascript
				//$this->Courriel ('Issue', true, Project::current()->id, Project\Issue::current()->id, Auth::user()->id, array('assigned', 'reassigned_by', 'reassigned_to', $WhoName), array('tinyissue', 'email', 'tinyissue', 'variable'));
			}

			//Show on screen what did just happened
			$t = time();
				$content  = '<div class="insides"><div class="topbar"><div class="data">';
				$content .= '<label class="label warning">';
				$content .= __('tinyissue.label_reassigned').'</label>&nbsp;';
				$content .= __('tinyissue.to') . ' ';
				$content .= ' <b>'.$WhoName.'</b> ';
				$content .= __('tinyissue.by') . ' ';
				$content .= \Auth::user()->firstname . ' ' . \Auth::user()->lastname;
				$content .= ' --- '.date("Y-m-d H:s").'</b> ';
				$content .= '</div></div></div>';
				$result = $content;
		}
		return $result;
	}

	/**
	 * Change issue's tags
	 *
	 * @request ajax
	 * @return string
	 */
	public function get_retag() {
			$content = "";
			$Issue = Project\Issue::current()->id;
			$Msg = "";
			$Show = false;

			$Modif = (Input::get('Modif') !== NULL) ? Input::get('Modif') :  false;
			$Quel = (Input::get('Quel')  !== NULL ) ? Input::get('Quel') : "xyzxyz";
			$TagNum = Tag::where('tag', '=', $Quel )->first(array('id','tag','bgcolor','ftcolor'));
			if (!isset($TagNum) || @$TagNum == '' ) { $Modif = false; $Quel = "xyzxyz"; }


			/**
			 * Edit an issue
			 * Adding a tag
			 */
			if ($Modif == 'AddOneTag' ) {
				$IssueTagNum = \DB::table('projects_issues_tags')->where('issue_id', '=', $Issue)->where('tag_id', '=', $TagNum->attributes['id'], 'AND' )->first(array('id'));
				$now = date("Y-m-d H:i:s");
				if ($IssueTagNum == NULL) {
					\DB::table('projects_issues_tags')->insert(array('id'=>NULL,'issue_id'=>$Issue,'tag_id'=>$TagNum->attributes['id'],'created_at'=>$now,'updated_at'=>$now) );
				} else {
					\DB::table('projects_issues_tags')->where('issue_id', '=', $Issue)->where('tag_id', '=', $TagNum->attributes['id'], 'AND' )->update(array('updated_at'=>$now) );
				}
				$Action = NULL;
				$Msg = __('tinyissue.tag_added');
				$Show = true;
				$added_tags = '"added_tags":['.$TagNum->attributes['id'].'],';
				$removed_tags = '"removed_tags":[],';
			}

			/**
			 * Edit an issue
			 * Taking a tag off
			 */
			if ($Modif == 'eraseTag') {
				$IssueTagNum =\DB::table('projects_issues_tags')->where('issue_id','=',$Issue)->where('tag_id','=',$TagNum->id,'AND')->first('id');
				\DB::table('projects_issues_tags')->delete($IssueTagNum->id);
				$Action = $Issue;
				$Modif = true;
				$Msg = '<span style="color:#F00;">'.__('tinyissue.tag_removed').'</span>';
				$Show = true;
				$added_tags = '"added_tags":[],';
				$removed_tags = '"removed_tags":['.$TagNum->attributes['id'].'],';
			}


			/**
			 * Update database
			 */
			if ($Show) { \User\Activity::add(6, Project::current()->id, $Issue, NULL, '{'.$added_tags.$removed_tags.'"tag_data":{"'.$TagNum->attributes['id'].'":{"id":'.$TagNum->attributes['id'].',"tag":"'.$TagNum->attributes['tag'].'","bgcolor":"'.$TagNum->attributes['bgcolor'].'","ftcolor":"'.$TagNum->attributes['ftcolor'].'"}},"tags_test":"Baboom en poudre"}' ); }

			/**
			 * Show on screen what just happened
			 */
			if (isset($TagNum) && $Quel != "xyzxyz") {
				$content .= '<div class="insides"><div class="topbar"><div class="data">';
				$content .= '<label style="color: '.$TagNum->attributes['ftcolor'].'; background-color: '.$TagNum->attributes['bgcolor'].'; padding: 5px 10px; border-radius: 8px;">';
				$content .= $TagNum->attributes['tag'].'</label>';
				$content .= ' : <b>'.$Msg.'</b> ';
				$content .= __('tinyissue.by') . ' ';
				$content .= \Auth::user()->firstname . ' ' . \Auth::user()->lastname;
				$content .= '</div></div></div>';
				$t = time();
				$result = $content;
			}

		return $content;
	}

	/**
	 * Add document to an existant issue
	 *
	 * upload file
	 * $_FILE contains name, type, tmp_name,error,size
	 *
	 * @request ajax
	 * @return string
	 */
	public function post_upload() {
		$pref = Config::get('application.attached');
		$url =\URL::home();
		$Qui = \Auth::user()->id;
		$msg = 0;
		$now = date("Y-m-d H:i:s");
		$Issue = Project\Issue::current()->id;
		$Project = Project::current()->id;
		$rep = (substr($pref["directory"], 0, 1) == '/') ? $pref["directory"] : "../".$pref["directory"];

		//Common data for the insertion into database: file's type, date, ect
		if ($Issue == 1) {
			//Attach a file to a new issue
			////We'll keep uploaded files in uploads/New/date directory until the issue will be created 
			$Issue = 'New/'.$Qui;
			$idComment = date("Ymd");
			if (!file_exists($rep."New")) {
				if (mkdir ($rep."New", 0775)) { $msg = $msg + 1; }
			}
		} else {
			//Attach a file to an existing issue
			$Quel = \DB::table('projects_issues_comments')->where('issue_id', '=', $Issue)->order_by('id','DESC')->get();
			$idComment = (isset($Quel[0]->id)) ? $Quel[0]->id : NULL ;
		}

		//Preparing the name and directories' names according to user preferences
		///First step: preparing the directories
		$TheFile	= $_FILES["Loading"];
		if($pref["method"] == 'i') {
			if (!file_exists($rep."/".$Issue."/".$idComment)) {
				if (!file_exists($rep.$Issue)) {
					if (mkdir ($rep.$Issue, 0775)) { $msg = $msg + 1; }
				}
			}
			$rep = $rep.$Issue."/";
		}

		////Second step: setting the file's name
		$fileName = (($pref["method"] == 'i') ? "" : $Issue."_").$idComment."_".$_GET["Nom"];	//Default value  ( 'ICN' )
		switch ($pref["format"]) {
			case "NCI":
				$fileName = $_GET["Nom"]."_".$idComment.(($pref["method"] == 'i') ? "" : "_".$Issue);
				break;
			case "CIN":
				$fileName = $idComment."_".(($pref["method"] == 'i') ? "" : $Issue."_").$_GET["Nom"];
				break;
		}

		//Third step: process the file
		if(move_uploaded_file($TheFile["tmp_name"], $rep.$fileName)) {
			$msg = $msg + 1;
			//Make sure the file will be openable to all users, not only the php engine
			////  5: Read and execute  (not write)
			////  6: Read and write (not execute)
			////  7: Read, write, execute
			////755 = Everything for owner, read and execute for strangers
			////775 = Everything for owner and group, read and execute for strangers
			////776 = Everything for owner and group, read and write for strangers
			if (chmod($rep.$fileName, "0775")) { $msg = $msg + 1; }
		} else {
			return 0;
		}
		//Forth step: Store it into database
		if ($Issue != 'New/'.$Qui) {
			//Modifié le 23 juin 2019, retrait des  "../" imposés dans l'enregistrement de l'adresse
			\DB::table('projects_issues_attachments')->insert(array('id'=>NULL,'issue_id'=>$Issue,'comment_id'=>$idComment,'uploaded_by'=>$Qui,'filesize'=>$TheFile["size"],'filename'=>str_replace("../", "", $rep).$fileName,'fileextension'=>$_GET["ext"],'upload_token'=>$TheFile["tmp_name"],'created_at'=>$now,'updated_at'=>$now) );
			$Quel = \DB::table('projects_issues_attachments')->where('issue_id', '=', $Issue)->order_by('id','DESC')->get();
			if (\User\Activity::add(7, $Project, $Issue, $Quel[0]->id, $fileName )) { $msg = $msg + 1; } else { $msg = $TheFile["error"]; }
		}

		//Fifth step: Show on user's desk
		if (is_numeric($msg)) {
			$rep = (substr($rep, 0, 3) == '../') ? substr($rep, 3) : $rep;
			$msg .= ';';
			$msg .= '<div class="insides"><div class="topbar"><div class="data">';
			$msg .= '<span style="font-weight: bold; color: #090;">'.__('tinyissue.fileuploaded').'</span>';
			$msg .= '<a href="'.$url.$rep.$fileName.'?'.$now.'" target="_blank" />';
			$msg .= '<img src="'.((in_array(strtolower($_GET["ext"]), array("jpg","png","gif","jpeg"))) ? $url.$rep.$fileName : $_GET["icone"]).'" height="30" align="right" border="0" />';
			$msg .= '</a>';
			$msg .= '<a href="'.$url.$rep.$fileName.'?'.$now.'" target="_blank" />';
			$msg .= '<b>'.$fileName.'</b>';
			$msg .= '</div></div></div>';

			//Sixth step: Notice the followers
			$this->Courriel ('Issue', true, Project::current()->id, $Issue, Auth::user()->id, array('attached'), array('tinyissue'));

		}
		return $msg;
	}

	private function Courriel ($Type, $SkipUser, $ProjectID, $IssueID, $User, $contenu, $src) {
		include_once "../app/application/controllers/ajax/SendMail.php";
	}
}
