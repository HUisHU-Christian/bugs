<?php
	$Rol = \DB::table('roles')->order_by('id','ASC')->get();
	$roles = array();
	foreach($Rol as $R) {  $roles[$R->id] = $R->name;  }
	$MonRole = Project\User::GetRole(Project::current()->id);
?>

<h3>
	<?php echo __('tinyissue.update'); ?> <em><?php echo Project::current()->name; ?></em>
	<?php
	if ($MonRole != 1) {
		echo '<a href="'.Project::current()->to('issue/new').'" class="newissue">'.__('tinyissue.new_issue').'</a>';
   }
   echo '<span>'.__('tinyissue.update_project_description').'</span>';
   ?>
</h3>

<div class="pad">
	<h3>
		<?php echo __('tinyissue.thisproject_members'); ?> 
	</h3>
	<table id="table_ListUsers" class="form" style="width: 50%;">
		<th class="project-user"><?php echo __('tinyissue.name'); ?></th>
		<th class="project-user"><?php echo __('tinyissue.role'); ?></th>
		<th class="project-user"><?php echo __('tinyissue.following'); ?></th>
		<th class="project-user">&nbsp;</th>
		
		<?php 
		foreach(Project::current()->users()->get() as $row) { 
			$Deja[] = Auth::user()->id;
			$userrole = \DB::table('projects_users')->where('project_id','=',Project::current()->id)->where('user_id','=',$row->id)->get();
			$follower = \DB::table('following')->where('project','=',1)->where('project_id','=',Project::current()->id)->where('user_id','=',$row->id)->count();
			$follower = ($follower > 0) ? 1 : 0;
			echo '<tr id="project-user_'.$row->id.'">';
			echo '	<td width="60%" class="project-user">'.$row->firstname . ' ' . $row->lastname.'</td>';
			echo '	<td width="20%" class="project-user">';
			if ($row->id == \Auth::user()->id) {
				echo $roles[$MonRole];
			} else {
				echo '	<select name="role[]" onchange="ChgRoleUser(this.value, '.Project::current()->id.', '.$row->id.');">';
				foreach($roles as $ind => $val) { echo '<option value="'.$ind.'" '.(($ind == $userrole[0]->role_id) ? ' selected="selected" ' : '').' >'.$val.'</option>'; }
				echo '	</select>';
			}
			echo '</td>';
			echo '	<td width="10%" class="project-user"><input type="checkbox" value="1" id="input_user_'.$row->id.'" '.(($follower) ? 'checked' : '' ).' onclick="Following(this.checked, '.Project::current()->id.', '.$row->id.');" /></td>';
			echo '	<td width="10%" class="project-user">';
			if(Auth::user()->permission('project-modify') && count(Project::current()->users()->get())  > 1 && $MonRole > 2 && $row->id != \Auth::user()->id) {
				echo '<a href="javascript:void(0);" onclick="remove_project_user('.$row->id.', '.Project::current()->id.', \''.__('tinyissue.projsuppmbre').'\', \'page\');" class="delete">'.__('tinyissue.remove').'</a>';
			}
			echo '	</td>';
			echo '</tr>
			';
		}
		?>

	</table>
	<br />

<?php if(Auth::user()->permission('project-modify')): ?>
	<div style="width: 50%;">
	<input type="text" id="input_rechNom" placeholder="" onkeyup="if(this.value.length > 2) { propose_project_user(this.value, <?php echo Project::current()->id; ?>, 'page', <?php echo $MonRole; ?>); }" style="margin-left: 0; border-color: grey; border-style: solid; border-width: 3px;" />
	</div>
	<div id="projetProsedNamesPage" class="projetProsedNamesPage">
	</div>
<?php endif; ?>

</div>

<div class="pad"><h3><?php echo __('tinyissue.thisproject_details'); ?></h3></div>

<?php
	$project_WebLnks = \DB::table('projects_links')->where('id_project', '=', $project->id)->order_by('category','ASC')->get();
	$WebLnk = array();
	foreach($project_WebLnks as $WebLnks) { 
		if (trim($WebLnks->desactivated) == '') { $WebLnk[$WebLnks->category] = $WebLnks->link; } 
	}
?>

<div class="pad">

	<form method="post" action="">

		<table class="form" style="width: 80%;">
			<tr>
				<th style="width: 10%;"><?php echo __('tinyissue.name'); ?></th>
				<td><input type="text" style="width: 98%;" name="name" value="<?php echo Input::old('name', Project::current()->name); ?>" /></td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.status') ?></th>
				<td><?php echo Form::select('status', array(1 => __('tinyissue.active'), 0 => __('tinyissue.archived')), Project::current()->status); ?></td>
			</tr>
			<tr>
			<th><?php echo __('tinyissue.default_assignee'); ?></th>
				<td>
					<?php echo Form::select('default_assignee', array(0 => '') + Project\User::dropdown(Project::current()->users()->get()), Project::current()->default_assignee); ?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<h3>
				<?php echo __('tinyissue.website_title'); ?>
				</h3>
				</td>
			</tr>
			<tr>
			<th><?php echo __('tinyissue.website_dev'); ?></th>
				<td>
					<input size="50" name="Dev" value="<?php echo ((isset($WebLnk['dev'])) ? $WebLnk['dev'] : ''); ?>" placeholder="http://127.0.0.1/<?php echo Project::current()->name; ?>" />
				</td>
			</tr>
			<tr>
			<th><?php echo __('tinyissue.website_git'); ?></th>
				<td>
					<input size="50" name="Git" value="<?php echo ((isset($WebLnk['git'])) ? $WebLnk['git'] : ''); ?>" placeholder="http://github.com/<?php echo Auth::user()->firstname; ?>/<?php echo Project::current()->name; ?>" />
				</td>
			</tr>
			<tr>
			<th><?php echo __('tinyissue.website_prod'); ?></th>
				<td>
					<input size="50" name="Prod" value="<?php echo ((isset($WebLnk['prod'])) ? $WebLnk['prod'] : ''); ?>" placeholder="http://www.<?php echo Project::current()->name; ?>" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<h3>&nbsp;&nbsp;</h3>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input type="submit" value="<?php echo __('tinyissue.update').' << '.Project::current()->name; ?> >>" />
					<br /><br />
					<br /><br />
					<br /><br />
					<input type="submit" name="delete" value="<?php echo __('tinyissue.delete').' << '.Project::current()->name; ?> >>" onclick="return confirm('<?php echo __('tinyissue.delete_project_confirm'); ?>');" style="background-color: red; border-color: black;" />
				</td>
			</tr>
		</table>

	</form>

</div>
<script type="text/javascript" >
function Following(etat, Project, Qui) {
	etat = (etat) ? 0 : 1;
	Follows(2, Qui, Project, 0, etat);
 
}
</script>