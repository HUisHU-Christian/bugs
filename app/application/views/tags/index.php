<h3>
	<?php echo __('tinyissue.tags') ?>
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
	echo '<table width="100%" id="table_tags_toutes">';
		echo '<th style="font-weight: bold; font-size: 120%; padding-bottom: 15px;" onclick="javascript: document.location.href=\''. URL::to('tags').'?orderby=id&sens='.((!isset($_GET["sens"])) ? 'ASC' : (($_GET["sens"] == 'ASC' && $_GET["orderby"] == 'id') ? 'DESC': 'ASC')).'\';">'.__('tinyissue.tag').'</th>';
		foreach ($lang as $l) { echo '<th style="font-weight: bold; font-size: 120%; padding-bottom: 15px;" onclick="javascript: document.location.href=\''. URL::to('tags').'?orderby='.$l.'&sens='.((!isset($_GET["sens"])) ? 'ASC' : (($_GET["sens"] == 'ASC' && $_GET["orderby"] == $l) ? 'DESC': 'ASC')).'\';">'.strtoupper($l).'</th>'; }
		foreach($tags as $tag) {
			echo '<tr>';
			echo '<td style="padding-bottom: 15px;">';
			echo '<a href="' . URL::to('tag/' . $tag->id . '/edit') . '"><label id="tag' . $tag->id . '" class="label" style="' . ($tag->bgcolor ? ' background-color: ' . $tag->bgcolor.';' : '') . ($tag->ftcolor ? ' color: ' . $tag->ftcolor . ';' : '') . '">' . $tag->tag . '</label></a>'; 
			echo '</td>';
			foreach ($lang as $l) { echo '<td><a href="' . URL::to('tag/' . $tag->id . '/edit') . '"><label id="tag' . $tag->id . '_'.$l.'" class="label" style="' . ($tag->bgcolor ? ' background-color: ' . $tag->bgcolor.';' : '') . ($tag->ftcolor ? ' color: ' . $tag->ftcolor . ';' : '') . '">' . $tag->$l . '</label></a></td>'; }
			echo '</tr>';
		} 
	echo '</table>';
	?>
	
	<br />
	
	<form method="to" action="<?php echo URL::to('tag/new'); ?>">
		<input type="submit" value="<?php echo __('tinyissue.create_tag'); ?>" class="button primary" />
		&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" value="<?php echo __('tinyissue.cancel'); ?>" class="button primary" onclick="document.location.href='<?php echo \Config::get('application.url'); ?>';" />
	</form>

</div>