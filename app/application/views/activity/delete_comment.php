<li onclick="window.location='<?php echo $issue->to(); ?>#comment<?php echo $comment->id ?? ''; ?>';" class="activity-item">

	<div class="tag">
		<label class="label notice" ><?php echo __('tinyissue.comment_deleted'); ?></label>
	</div>

	<div class="data">
		<?php echo __('tinyissue.by'); ?>
		<strong><?php echo $user->firstname . ' ' . $user->lastname; ?></strong> <?php echo __('tinyissue.on_issue'); ?> <a href="<?php echo $issue->to(); ?>"><?php echo $issue->title; ?></a>
		<br clear="all" />
		<span class="time">
		 <?php echo $comment->status ?? ''; ?> 
			<?php echo date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->created_at)); ?>
		</span>
	</div>

	<div class="clr"></div>
</li>
