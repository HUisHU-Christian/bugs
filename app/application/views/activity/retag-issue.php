<?php if (isset($issue)) { ?>
<li onclick="window.location='<?php echo $issue->to(); ?>';">
	<div class="tag">
		<label class="label warning"><?php echo __('tinyissue.tag_has_been_updated'); ?></label>
	</div>

	<div class="data">
		<?php 
			echo '<a href="'.$issue->to().'">'.$issue->title.'</a>'.__('tinyissue.tag_added');
			echo '<a href="'.$issue->to().'">'.$issue->title.'</a>'.__('tinyissue.tag_removed');
			echo '<strong>';
			echo ($activity->action_id > 0) ? $assigned->firstname . ' ' . $assigned->lastname : __('tinyissue.no_one'); 
			echo '</strong>';
			echo __('tinyissue.by'); 
			echo '<strong>'.$user->firstname . ' ' . $user->lastname.'</strong>';
			echo '<span class="time">';
			echo date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->created_at));
			echo '</span>';
		 ?>
	</div>

	<div class="clr"></div>
</li>
<?php } ?>
