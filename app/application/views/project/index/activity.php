<div class="blue-box">
	<div class="inside-pad">
	<?php 
		if(!$activity) {
			echo '<p>'.__('tinyissue.no_activity').'</p>';
		} else {
			$ParPage =  \Config::get('application.pref.todoitems');
			$page = $_GET["page"] ?? 0;
			$debut = $_GET["debut"] ?? ($page * $ParPage) + 1;
			$combien = $project->count_project_activity();
			if ($combien > $ParPage) {
				echo '<br />';
				$compte = 0;
				$rendu = 0;
				echo '<ul class="tabs">';
				if ($page > 0) { echo '<li><a href="'.Project::current()->id.'?page='.($page-1).'"> < </a></li>'; }
				while ($rendu <= $combien) {
					$compte = $compte + 1;
					echo '<li'.((($page*$ParPage + $compte) == $debut) ? ' class="active"' : '').'><a href="'.Project::current()->id.'?page='.$page.'&debut='.(($page*$ParPage) + $compte).'">'.(($page*\Config::get('application.pref.todoitems')) + $compte).'</a></li>';
					$rendu = $rendu + $ParPage;
					if ($compte >= $ParPage || (($page*$ParPage) + $compte) >= $combien / $ParPage) { break; }
				}
				if ((($page*$ParPage) + $compte) <= $combien / $ParPage) { echo '<li><a href="'.Project::current()->id.'?page='.($page+1).'"> > </a></li>'; }
				echo '</ul>';
			}
			echo '<ul class="activity">';
			foreach($project->activity($ParPage,$debut) as $activity) {
				echo $activity;
			} 
			echo '</ul>';
		} 
	?>
	</div>
</div>
