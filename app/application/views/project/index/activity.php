<div class="blue-box">
	<div class="inside-pad">

	<?php 
		if(!$activity) {
			echo '<p>';
			echo __('tinyissue.no_activity');
			echo '</p>';
		} else {
			echo '<ul class="activity">';
			foreach($project->activity(10) as $activity) {
				echo $activity;
			} 
			echo '</ul>';
		} 
	?>
	</div>
</div>
