<li onclick="window.location='<?php echo $issue->to(); ?>';">
	<div class="tag">
		<label class="label info"><?php echo __('tinyissue.tag_update'); ?></label>
	</div>

	<div class="data">
		<div class="tag-activity">
			<?php 
				if($tag_counts['added'] > 0) {
					foreach($tag_diff['added_tags'] as $tag) { 
						echo '<label class="label notice" style="color:'.$tag_diff['tag_data'][$tag]['ftcolor'].';background-color:'.$tag_diff['tag_data'][$tag]['bgcolor'].';">' . $tag_diff['tag_data'][$tag]['tag'] . '</label>'; 
					} 
					echo __($tag_counts['added'] > 1 ? 'tinyissue.tags_added' : 'tinyissue.tag_added');
					echo ' '; 
					echo __('tinyissue.in'); 
					echo ' '; 
					echo '<a href="'.$issue->to().'">'.$issue->title.'</a>&nbsp;';
					echo __('tinyissue.by'); 
					echo '&nbsp;<strong>'.$user->firstname . ' ' . $user->lastname.'</strong>&nbsp;';
				} 
			
			if($tag_counts['added'] > 0 && $tag_counts['removed'] > 0) { echo '<div class="tag-activity-spacer"></div>'; } 
			
			if($tag_counts['removed'] > 0) {
				foreach($tag_diff['removed_tags'] as $tag) { 
					echo '<label class="label notice" style="color:'.$tag_diff['tag_data'][$tag]['ftcolor'].';background-color:'.$tag_diff['tag_data'][$tag]['bgcolor'].';">' . $tag_diff['tag_data'][$tag]['tag'] . '</label>'; 
				}
				echo __($tag_counts['removed'] > 1 ? 'tinyissue.tags_removed' : 'tinyissue.tag_removed'); 
					echo ' '; 
				echo __('tinyissue.in'); 
					echo ' '; 
				echo '<a href="'.$issue->to().'">'.$issue->title.'</a>';
				echo __('tinyissue.by');
				echo '<strong>'.$user->firstname . ' ' . $user->lastname.'</strong>';
			} 
			?>
		</div>
		<span class="time">
			<?php echo date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->created_at)); ?>
		</span>
	</div>

	<div class="clr"></div>
</li>