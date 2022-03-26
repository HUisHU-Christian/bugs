<?php 
	if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
//		echo '<script>document.location.href="'.URL::to().'";</script>';
	}
	$NbIssues = \Config::get('application.pref.todonbitems');
	
?>
<h3>
	<?php 
	//Gestion des droits basée sur le rôle spécifique à un projet
	//Selon l'analyse du 13 novembre 2021, il n'est pas nécessaire de changer le calcul du droit ci-bas
	if (Auth::user()->role_id != 1) { 
   	echo '<a href="'.Project::current()->to('issue/new').'" class="newissue">'.__('tinyissue.new_issue');
	} 
   echo '<a href="'.Project::current()->to().'">'.Project::current()->name.'</a>
			<span>';
			$lesProj = array(Project::current()->id);
			$mesAdmin = \Project\User::where('projects_users.project_id', '=', Project::current()->id)->where('projects_users.role_id', '=', 4)->join('users', 'users.id', '=', 'projects_users.user_id')->get(array('users.firstname', 'users.lastname', 'users.id', 'projects_users.user_id'));
			$lien = " ";
			echo __('tinyissue.project_overview').' : '; 
			foreach ($mesAdmin as $id_admin) {
				echo $lien.$id_admin->firstname.' '.$id_admin->lastname;
				$lien = ", ";
			}
			echo '</span>';
   ?>
</h3>

<div class="pad">
	<ul class="tabs">
		<li <?php echo $active == 'activity' ? 'class="active"' : ''; ?>>
			<a href="<?php echo Project::current()->to(); ?>"><?php echo __('tinyissue.activity'); ?></a>
		</li>
		<li <?php echo $active == 'open' ? 'class="active"' : ''; ?>>
			<?php 
				echo '<a href="'.Project::current()->to('issues').'?tag_id=1">';
				echo $open_count.' '.($open_count < 2 ? __('tinyissue.open_issue') : __('tinyissue.open_issues')).'</a>';
			?>
			
		</li>
		<li <?php echo $active == 'closed' ? 'class="active"' : ''; ?>>
			<?php
				echo '<a href="'.Project::current()->to('issues').'?tag_id=2">';
				echo $closed_count.' '.($closed_count < 2 ? __('tinyissue.closed_issue') : __('tinyissue.closed_issues')).'</a>';
			?>
			
		</li>
		<li <?php echo $active == 'assigned' ? 'class="active"' : ''; ?>>
			<a href="<?php echo Project::current()->to('issues'); ?>?tag_id=5&amp;assigned_to=<?php echo Auth::user()->id; ?>">
			<?php echo $assigned_count.' '.($assigned_count < 2 ? __('tinyissue.issue_assigned_to_you') : __('tinyissue.issues_assigned_to_you')); ?>
			</a>
		</li>
		<li <?php echo $active == 'future' ? 'class="active"' : ''; ?>>
			<?php 
				echo '<a href="'.Project::current()->to('issues').'?tag_id=3">';
				echo $future_count.' '.($future_count < 2 ? __('tinyissue.issue_avenir') : __('tinyissue.issues_avenir')).'</a>'; 
			?>
		</li>
	</ul>

	<div class="inside-tabs">
		<?php echo $page; ?>
	</div>
</div>
