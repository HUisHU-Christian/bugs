<?php 
	if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
//		echo '<script>document.location.href="'.URL::to().'";</script>';
	}
	$NbIssues = $config_app["TodoNbItems"] ?? 25;
	
?>
<h3>
	<?php 
//Gestion des droits basée sur le rôle spécifique à un projet
//Selon l'analyse du 13 novembre 2021, il n'est pas néssaire de changer le calcul du droit ci-bas
	if (Auth::user()->role_id != 1) { ?>
   	<a href="<?php echo Project::current()->to('issue/new'); ?>" class="newissue"><?php echo __('tinyissue.new_issue');?>
   <?php } ?> 
   <a href="<?php echo Project::current()->to(); ?>"><?php echo Project::current()->name; ?></a>
	<span><?php echo __('tinyissue.project_overview');?></span>
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
//				if ($active == 'open') { Project::current()->nextissuesThisTab('?tag_id=1', $open_count, $NbIssues); }
			?>
			
		</li>
		<li <?php echo $active == 'closed' ? 'class="active"' : ''; ?>>
			<?php
				echo '<a href="'.Project::current()->to('issues').'?tag_id=2">';
				echo $closed_count.' '.($closed_count < 2 ? __('tinyissue.closed_issue') : __('tinyissue.closed_issues')).'</a>';
//				if ($active == 'closed') { Project::current()->nextissuesThisTab('?tag_id=2', $closed_count, $NbIssues); }
			?>
			
		</li>
		<li <?php echo $active == 'assigned' ? 'class="active"' : ''; ?>>
			<a href="<?php echo Project::current()->to('issues'); ?>?tag_id=1&amp;assigned_to=<?php echo Auth::user()->id; ?>">
			<?php echo $assigned_count.' '.($assigned_count < 2 ? __('tinyissue.issue_assigned_to_you') : __('tinyissue.issues_assigned_to_you')); ?>
			</a>
		</li>
		<li <?php echo $active == 'future' ? 'class="active"' : ''; ?>>
			<?php 
				echo '<a href="'.Project::current()->to('issues').'?tag_id=3">';
				echo $future_count.' '.($future_count < 2 ? __('tinyissue.issue_avenir') : __('tinyissue.issues_avenir')).'</a>'; 
//				if ($active == 'future') { Project::current()->nextissuesThisTab('?tag_id=3', $future_count, $NbIssues); }
			?>
		</li>
	</ul>

	<div class="inside-tabs">
		<?php echo $page; ?>
	</div>
</div>