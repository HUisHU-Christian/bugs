<div class="blue-box">
	<div class="inside-pad">

		<?php if(!$activity): ?>
		<p>
			<?php echo __('tinyissue.no_activity');?>
		</p>
		<?php else: ?>
		<ul class="activity">
			<?php foreach($project->activity(\Config::get('application.pref.todoitems')) as $activity): ?>
			<?php echo $activity; ?>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

	</div>
</div>
