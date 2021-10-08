<?php 
	if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
		echo '<script>document.location.href="'.URL::to().'";</script>';
	}
?>
<?php if ($activity) { ?>
<li id="comment<?php echo $activity->id; ?>" class="comment">
	<div class="insides">
		<div class="topbar">
			<label class="label warning"><?php echo __('tinyissue.label_reassigned'); ?></label> <?php echo __('tinyissue.to'); ?>
			<?php 
				echo '<strong>'.(($activity->action_id > 0) ?  $assigned->firstname . ' ' . $assigned->lastname : __('tinyissue.no_one')).'</strong>';
				echo ' '.__('tinyissue.by').' ';
				echo '<strong>'.(($activity->user_id > 0) ? $user->firstname . ' ' . $user->lastname :  __('tinyissue.no_one')).'</strong>';
			?>

			<span class="time">
				<?php echo ' &nbsp;&nbsp;'.date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->created_at)).''; ?>
			</span>
		</div>
	</div>
	<div class="clr"></div>
</li>
<?php } ?>
