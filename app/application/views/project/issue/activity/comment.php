<?php 
	if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
		echo '<script>document.location.href="'.URL::to().'";</script>';
	}
if ($comment !== NULL) {
?>
<li id="comment<?php echo @$comment->id; ?>" class="comment">
	<div class="insides" id="div_comment_<?php echo @$comment->id; ?>">
		<div class="topbar">
			<?php if(Auth::user()->permission('issue-modify')): ?>
			<ul>
				<li class="edit-comment">
					<a href="javascript:AffichonsEditor('<?php  echo @$comment->id; ?>');" class="edit">Edit</a>
				</li>
				<li class="delete-comment">
				<a href="<?php echo $issue->to('delete_comment?comment='.@$comment->id); ?>" class="delete">Delete</a>
				</li>
			</ul>
			<?php endif; ?>
			<strong><?php echo $user->firstname . ' ' . $user->lastname; ?></strong>
			&nbsp;
			<?php 
				echo __('tinyissue.commented');
				echo '&nbsp;'; 
				echo date(Config::get('application.my_bugs_app.date_format'), strtotime(@$comment->created_at));
				echo '&nbsp;';
				echo (@$comment->updated_at == @$comment->created_at) ? '' : __('tinyissue.following_email_issue_tit').' : '.date(Config::get('application.my_bugs_app.date_format'), strtotime(@$comment->updated_at));  
			?>
		</div>

		<div class="issue" id="div_comment_<?php echo @$comment->id; ?>_issue">
			<?php echo Project\Issue\Comment::format(@$comment->comment); ?>
		</div>
		<?php
			if ($comment !== NULL) {
				$modifs = \DB::table('users_activity')->where('action_id', '=', @$comment->id)->where('type_id', '=', '12')->order_by('updated_at','DESC')->get(array('id', 'user_id', 'data', 'updated_at'));
				if (count($modifs) > 0) {
					echo '<details id="details_activity_'.@$comment->id.'" style="color:black; background-color: #e8e8e8;">';
					echo '<summary style="color:black; background-color: #e8e8e8; font-weight: normal;">'.__('tinyissue.comment_editinng_hist').'</summary>';
					foreach($modifs as $modif) {
						$auteur = \DB::table('users')->where('id', '=', $modif->user_id)->get(array('firstname', 'lastname'));
						echo '<hr / style="border-width: 1px;">';
						echo '<b>'.$auteur[0]->firstname.' '.$auteur[0]->lastname.'</b> '.$modif->updated_at.':<br />';
						echo $modif->data;
					}
						echo '<hr />';
					echo '</details>';
				}
			}
		?>

		<?php if(Auth::user()->permission('issue-modify')): ?>
		<div class="comment-edit" id="div_comment_<?php echo @$comment->id; ?>_Sdiv" >
			<form action="<?php echo $issue->to('edit_comment?id='.@$comment->id.'\','.@$comment->id); ?>" method="POST"  name="form_<?php echo @$comment->id; ?>">
			<textarea name="body"  id="textarea_<?php echo @$comment->id; ?>_SSdiv" style="width: 98%; height: 90px;"><?php echo stripslashes(@$comment->comment); ?></textarea>
			<div class="right">
				<input type="submit" id="button_<?php echo @$comment->id; ?>_enregi" class="action save" value="<?php echo __('tinyissue.save'); ?>">
				<a href="javascript:CachonsEditor(<?php echo @$comment->id; ?>);" id="button_<?php echo @$comment->id; ?>_cancel" class="action cancel"><?php echo __('tinyissue.cancel'); ?></a>
				<input name="id" value="<?php echo @$comment->id; ?>" type="hidden" />
			</div>
			</form>
		</div>
		<?php endif; ?>
		
		<ul class="attachments">
			<?php 
				if ($comment !== NULL) {
					foreach($comment->attachments()->get() as $attachment) {
						echo '<li>';
						if(in_array($attachment->fileextension, Config::get('application.image_extensions'))) {
							echo '<a href="'.URL::base().Config::get('application.attachment_path').rawurlencode($attachment->filename).'" title="'.$attachment->filename.'"><img src="'.URL::base() . Config::get('application.attachment_path') . $project->id . '/' . $attachment->upload_token . '/' . $attachment->filename.'" style="max-width: 100px;"  alt="'.$attachment->filename.'" /></a>';
						} else {
							echo '<a href="'.URL::base().Config::get('application.attachment_path').rawurlencode($attachment->filename).'" title="'.$attachment->filename.'">'.$attachment->filename.'</a>';
						}
						echo '</li>';
					}
				}
			?>
		</ul>
	</div>
	<div class="clr"></div>
</li>
<?php } ?>
<script type="text/javascript">
<?php
	$wysiwyg = Config::get('application.editor');
	if (trim(@$wysiwyg['directory']) != '') {
		if (file_exists($wysiwyg['directory']."/Bugs_code/showeditor.js")) {
			include_once $wysiwyg['directory']."/Bugs_code/showeditor.js"; 
			if ($wysiwyg['name'] == 'ckeditor') {
				echo "
				setTimeout(function() {
					//showckeditor ('body');
				} , 567);
				";
			}
		} 
	} 
?>

</script>