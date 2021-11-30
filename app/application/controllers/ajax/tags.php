<?php

class Ajax_Tags_Controller extends Base_Controller {

	/**
	 * Change issue's tags
	 *
	 * @request ajax
	 * @return string
	 */
	public function get_retag() {
			$content = "";
			$contenu = array('tagsadd');
//			$Issue = Input::get('IssueID');
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
				$IssueTagNum = \DB::table('projects_issues_tags')->where('issue_id', '=', Input::get('IssueID'))->where('tag_id', '=', $TagNum->attributes['id'], 'AND' )->first(array('id'));
				$now = date("Y-m-d H:i:s");
				if ($IssueTagNum == NULL) {
					\DB::table('projects_issues_tags')->insert(array('id'=>NULL,'issue_id'=>Input::get('IssueID'),'tag_id'=>$TagNum->attributes['id'],'created_at'=>$now,'updated_at'=>$now) );
				} else {
					\DB::table('projects_issues_tags')->where('issue_id', '=', Input::get('IssueID'))->where('tag_id', '=', $TagNum->attributes['id'], 'AND' )->update(array('updated_at'=>$now) );
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
				$IssueTagNum =\DB::table('projects_issues_tags')->where('issue_id','=',Input::get('IssueID'))->where('tag_id','=',$TagNum->id,'AND')->first('id');
				\DB::table('projects_issues_tags')->delete($IssueTagNum->id);
				$Action = Input::get('IssueID');
				$Modif = true;
				$Msg = '<span style="color:#F00;">'.__('tinyissue.tag_removed').'</span>';
				$Show = true;
				$added_tags = '"added_tags":[],';
				$removed_tags = '"removed_tags":['.$TagNum->attributes['id'].'],';
				$contenu = array('tagsote');
			}

			/**
			 * Update database
			 */
			if ($Show) { \User\Activity::add(6, Input::get('ProjectID'), Input::get('IssueID'), NULL, '{'.$added_tags.$removed_tags.'"tag_data":{"'.$TagNum->attributes['id'].'":{"id":'.$TagNum->attributes['id'].',"tag":"'.$TagNum->attributes['tag'].'","bgcolor":"'.$TagNum->attributes['bgcolor'].'","ftcolor":"'.$TagNum->attributes['ftcolor'].'"}},"tags_test":"Baboom en poudre"}' ); }

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
			
			//Email followers about these changes (add/remove tag)
			\Mail::letMailIt(array(
				'ProjectID' => Input::get('ProjectID'), 
				'IssueID' => Input::get('IssueID'), 
				'SkipUser' => true,
				'Type' => 'Issue', 
				'user' => \Auth::user()->id,
				'contenu' => $contenu,
				'src' => array('tinyissue')
				),
				\Auth::user()->id, 
				\Auth::user()->language
			);

		return $content;
	}

	public function get_suggestions($type = 'edit') {
		$retval = array();

		$term = Input::get('term', '');
		$term = (in_array($term, array("*"))) ? '%' : $term;
		if ($term) {
			$tags = Tag::where('tag', 'LIKE', '%' . $term . '%')->order_by('tag', 'ASC')->get();
			foreach ($tags as $tag) {
				if ($type == 'filter' && strpos($tag->tag, ':') !== false) {
					$tag_prefix = substr($tag->tag, 0, strpos($tag->tag, ':'));
				}
				$retval[] = $tag->tag;
			}
		}

		return json_encode($retval);
	}

}