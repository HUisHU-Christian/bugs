<?php 
	if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
		echo '<script>document.location.href="'.URL::to().'";</script>';
	}
?>
<?php if ($activity) { ?>
<li id="comment<?php echo $activity->id; ?>" class="comment activity-item">
	<div class="insides">
		<div class="topbar">
				<?php
					if (trim($activity->attributes['data']) == '') {
					 	echo '<label class="label notice">'.__('tinyissue.comment_edited').'</label><b>';
					} else {
					 	echo '<ul>';
					 	echo '<li class="edited-comment">';
					 	echo '<a href="javascript: alert(\''.$activity->data.'\');">Infos</a>';
					 	echo '</li>';
					 	echo '</ul>';
					 	echo '<label class="label notice">'.__('tinyissue.comment_edited').'</label><b>';

					 	echo ' ';
						echo ' '.__('tinyissue.by').' '.$user->attributes["firstname"].' '.$user->attributes["lastname"].'</b></a> ';
						echo ' &nbsp;&nbsp; '.date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->attributes['updated_at'])).'';
					}
				?>
		</div>
	</div>
	<div class="clr"></div>
</li>
<?php } ?>
