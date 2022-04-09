<div class="blue-box">
	<div class="inside-pad">
	<?php 
		if(!$activity) {
			echo '<p>'.__('tinyissue.no_activity').'</p>';
		} else {
			$page = $_GET["page"] ?? 1;
			$combien = $project->count_project_activity(); 
			if ($combien > \Config::get('application.pref.todoitems')) {
				echo '<br />';
				$compte = 0;
				$rendu = 0;
				echo '<ul class="tabs">';
				while ($rendu <= $combien) {
					echo '<li'.((++$compte == $page) ? ' class="active"' : '').'><a href="'.Project::current()->id.'?page='.$compte.'">'.$compte.'</a></li>';
					$rendu = $rendu + \Config::get('application.pref.todoitems');
					if ($compte >= \Config::get('application.pref.todoitems')) { break; }
				}
				echo '</ul>';
			}
			echo '<ul class="activity">';
			foreach($project->activity(\Config::get('application.pref.todoitems'),$page) as $activity) {
				echo $activity;
			} 
			echo '</ul>';
		} 
	?>
	</div>
</div>
