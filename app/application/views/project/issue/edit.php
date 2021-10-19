<?php 
	if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
		echo '<script>document.location.href="'.URL::to().'";</script>';
	}
?>
<h3>
	<?php echo __('tinyissue.edit_issue').'&nbsp;&nbsp;&nbsp<a href="'.URL::to().'/project/'. Project::current()->id.'/issue/'.$issue->id.'" style="color: #138dc1;">'.Input::old('title', $issue->title).'</a>'; ?>
</h3>

<div class="pad">
	<form method="post" action="">

		<table class="form" style="width: 100%;">
			<tr>
				<th style="width: 10%"><?php echo __('tinyissue.title'); ?></th>
				<td>
					<input type="text" name="title" style="width: 98%;" value="<?php echo Input::old('title', $issue->title); ?>" />

					<?php echo $errors->first('title', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.issue'); ?></th>
				<td>
					<textarea name="body" style="width: 98%; height: 150px;"><?php echo Input::old('body', $issue->body); ?></textarea>
					<?php echo $errors->first('body', '<span class="error">:message</span>'); ?>
				</td>
			</tr>

			<tr>
				<th><?php echo __('tinyissue.tags'); ?><br /><span style="font-weight: lighter;">Joker : % *</span>
				</th>
				<td>
					<?php echo Form::text('tags', Input::old('tags', $issue_tags), array('id' => 'tags')); ?>
					<?php echo $errors->first('tags', '<span class="error">:message</span>'); ?>
					<script type="text/javascript">
					$(function(){
						$('#tags').tagit({
							autocomplete: {
								source: '<?php echo URL::to('ajax/tags/suggestions/edit'); ?>'
							}
						});
					});
					</script>
					&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.duration'); ?></th>
				<td>
					<input type="number" name="duration" style="width: 60px;" value="<?php echo Input::old('duration', $issue->duration); ?>" min="1" max="400" />&nbsp;<?php echo __('tinyissue.days'); ?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php echo __('tinyissue.issue_start_at'); ?> : <input name="start_at" id="input_start_at" type="date" value="<?php echo $issue->start_at; ?>" />
				</td>
			</tr>
			<?php if(Auth::user()->permission('issue-modify')): ?>
			<tr>
				<th><?php echo __('tinyissue.priority'); ?></th>
				<td>
					<?php 
						echo Form::select('status', array(5=>__('tinyissue.priority_desc_5'),4=>__('tinyissue.priority_desc_4'),3=>__('tinyissue.priority_desc_3'),2=>__('tinyissue.priority_desc_2'),1=>__('tinyissue.priority_desc_1')), $issue->status, array('id'=>'select_status', 'onmouseover'=>'document.getElementById(\'taglev\').style.display = \'block\';', 'onmouseout'=>'document.getElementById(\'taglev\').style.display = \'none\';')); 
					?>
				</td>
			</tr>

			<tr>
				<th><?php echo __('tinyissue.assigned_to'); ?>&nbsp;&nbsp;</th>
				<td>
					<?php echo Form::select('assigned_to', array(0 => '') + Project\User::dropdown($project->users()->get()), Input::old('asigned_to', $issue->assigned_to)); ?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th></th>
				<td><input type="submit" value="<?php echo __('tinyissue.update_issue'); ?>" class="button primary" /></td>
			</tr>
		</table>

		<?php echo Form::token(); ?>

	</form>

</div>

<?php
$active_projects =Project\User::active_projects();
if (count($active_projects)>1 && $issue->closed_by == 0 ) {
?>
<hr style="width: 80%; margin-top: 50px; margin-bottom: 40px;" />
<div id="ChangeProjectthisIssue" style="text-align: left; margin-left: 10%; width: 100%;">
<!-- 
	<form class="projects_selector" name="projectChanger">
 -->
<form method="GET" action="" name="projectChanger">
<fieldset class="sidebar_Projects_label"><label for="projects_select"><?php echo __('tinyissue.select_to_project');?> : </label>
<select name="projectNew" id="project_newSelect" onchange="Issue_ChgListMbre(this.value);" >
<?php
	$NbIssues = array();
	$Proj = array();
	$SansAccent = array();
	foreach($active_projects as $row) {
		$NbIssues[$row->id] = $row->count_open_issues();
		//$NbIssues[$row->to()] = $row->count_open_issues();
		//$Proj[$row->to()] = $row->name.' ('.$NbIssues[$row->to()].')';
		$Proj[$row->id] = $row->name.' ('.$NbIssues[$row->id].')';
	}
	foreach ($Proj as $ind => $val ){
		$SansAccent[$ind] = htmlentities($val, ENT_NOQUOTES, 'utf-8');
		$SansAccent[$ind] = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $SansAccent[$ind]);
		$SansAccent[$ind] = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $SansAccent[$ind]);
		$SansAccent[$ind] = preg_replace('#&[^;]+;#', '', $SansAccent[$ind]);
	}
	asort($SansAccent);

	foreach($SansAccent as $ind => $val) {
		$selected = ($ind == Project::current()->id) ? 'selected="selected"':'';
		echo '<option value="'.$ind.'" '.$selected.'>'.$Proj[$ind].'</option>';
	 }
?>
</select>
&nbsp;&nbsp;&nbsp;
<label for="projects_select_resp"><?php echo __('tinyissue.select_to_projectResp');?> : </label>
<select name="projectNewResp" id="project_newSelectResp" >
<?php 
	foreach(Project::current()->users()->get() as $row) { 
		echo '<option value="'.$row->id.'" ';
		echo ($row->id == $issue->assigned_to ) ? 'selected="selected"' : '';
		echo '>'.$row->firstname . ' ' . $row->lastname.'</option>';
	}
?>
</select>
&nbsp;&nbsp;&nbsp;
<input type="submit" value="<?php echo __('tinyissue.selected_to_project'); ?>" style="color: navy; padding: 3px 10px; border:none;" />
<input type="hidden" name="projetOld" value="<?php echo Project::current()->id; ?>" />
<input type="hidden" name="ticketNum" value="<?php echo $issue->id; ?>" />
<input type="hidden" name="ticketAct" value="changeProject" />
</fieldset>
</form>

</div>
<hr style="width: 80%; margin-top: 25px; margin-bottom: 50px;" />
<?php
}
?>

<script type="text/javascript">
var d = new Date();
var t = d.getTime();
var AllTags = "";


function AddTag (Quel,d) {
	return true;
}

<?php
	$wysiwyg = Config::get('application.editor');
	if (trim(@$wysiwyg['directory']) != '') {
		if (file_exists($wysiwyg['directory']."/Bugs_code/showeditor.js")) {
			include_once $wysiwyg['directory']."/Bugs_code/showeditor.js";
			if ($wysiwyg['name'] == 'ckeditor') {
				echo "
				setTimeout(function() {
					showckeditor ('body');
				} , 567);
				";
			}
		} 
	} 
?>

</script>