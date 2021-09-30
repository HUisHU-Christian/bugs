<li onclick="window.location='<?php echo $issue->to(); ?>';">

	<div class="tag">
		<label class="label warning"><?php echo __('tinyissue.label_reassigned'); ?></label>
	</div>

	<div class="data">
		<a href="<?php echo $issue->to(); ?>"><?php echo $issue->title; ?></a> <?php echo __('tinyissue.was_reassigned_to'); ?>
		<?php
			echo '<strong>'; 
			echo  ($activity->action_id > 0) ? $assigned->firstname . ' ' . $assigned->lastname :  __('tinyissue.no_one');
			echo '</strong>';

			echo __('tinyissue.by');

			echo '<strong>'.$user->firstname . ' ' . $user->lastname.'</strong>';
		?>

		<span class="time">
			<?php echo date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->created_at)); ?>
		</span>
	</div>

	<div class="clr"></div>
</li>
