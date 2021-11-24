<h3>
	<?php 
		echo 'Bienvenue dans la gestion des activités';
		//echo __('tinyissue.activitys') 
	?>
</h3>

<div class="pad">

	<?php 
	$lng = scandir('application/language');
	$PasCeuxCi = array('.','..','all.php');
	$lang = array();
	foreach ($lng as $val) {
		if (in_array($val, $PasCeuxCi)) { continue; }
		$lang[] = $val;
	}
	echo '<table width="100%" id="table_activitys_toutes">';
		echo '<th style="font-weight: bold; font-size: 120%; padding-bottom: 15px;">'.__('tinyissue.activity').'</th>';
		foreach ($lang as $l) { echo '<th style="font-weight: bold; font-size: 120%; padding-bottom: 15px;">'.strtoupper($l).'</th>'; }
		foreach($activities as $activity) {
			echo '<tr>';
			echo '<td style="padding-bottom: 15px;">';
			echo '<a href="' . URL::to('administration/activity/edit/' . $activity->id . '') . '" >'. $activity->activity . '</a>'; 
			echo '</td>';
			foreach ($lang as $l) { echo '<td>'. $activity->$l . '</td>'; }
			echo '</tr>';
		} 
	echo '</table>';
	?>
	
	<br />
	
	<form method="to" action="<?php echo URL::to('activity/new'); ?>">
<!-- 	
		<input type="submit" value="<?php echo __('tinyissue.create_activity'); ?>" class="button primary" />
		&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;
 -->
		<input type="button" value="<?php echo __('tinyissue.cancel'); ?>" class="button primary" onclick="document.location.href='<?php echo \Config::get('application.url'); ?>';" />
	</form>

</div>