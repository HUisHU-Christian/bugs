<h3>
	<?php echo __('tinyissue.your_todos'); ?>
	<span><?php echo __('tinyissue.your_todos_description'); ?></span>
</h3>
<?php
	$config_app = require path('public') . 'config.app.php';
	if(!isset($config_app['PriorityColors'])) { $config_app['PriorityColors'] = array("black","Orchid","Cyan","Lime","orange","red"); }
	if (!isset($config_app['Percent'])) { $config_app['Percent'] = array (100,0,10,80,100); }
	$config_app['Percent'][5] = 0;
	$NbIssues = $config_app["TodoNbItems"] ?? 25;
	$column = array(1,2,3,0);

	echo '<div class="pad" id="todo-lanes">
	';
	foreach ($column as $col) {
		echo '
		<div class="todo-lane blue-box'.(($col == 0) ? ' todo-closed' : '').'" id="lane-status-'.$col.'" data-status="'.$col.'" ondragover="dragOver(this.id);"  ondragleave="dragLeave(this.id);" ondrop="alert(\'Nous recevons ceci de la colonne\');dragDrop(this.id);">
		';
		$Combien = (isset($lanes[$col]) ? count($lanes[$col]) :  0);
		$rendu = 0;
		echo '<h4>'.$status_codes[$col].' ('.$config_app['Percent'][$col].(($col == 0) ? '' :  ' - '.($config_app['Percent'][$col+1]-1)).'% )<br />';
		echo '<span style="color: black; font-size: 75%; margin-left:0;">';
		echo '<b><span id="todo-list-span-'.$col.'" style="margin-left: 0px;">'.(($Combien > $NbIssues) ? ($rendu+1).'-'.($rendu+$NbIssues).'</span> / ' : 'Total : </span>').$Combien.'</b><br />';
		if ($Combien >= $NbIssues) { while ($rendu < $Combien) {
			echo '<a href="javascript: AffichonsAutres('.$col.', '.($rendu-0).');" style="font-size: 100%; font-weight: normal; ">'.(($rendu/$NbIssues)+1).'</a>&nbsp;&nbsp;';
			if (((($rendu+$NbIssues)/$NbIssues)/10) == round((($rendu+$NbIssues)/$NbIssues)/10)) { echo '<br />'; }
			$rendu = $rendu + $NbIssues;
		}}
		echo '</span>
		</h4>
		';
		echo '<div id="lane-details-'.$col.'">';
		if (isset($lanes[$col])) {
			$rendu = 0;
			foreach ($lanes[$col] as $lane) {
				echo '<div class="todo-list-item" id="todo-id-'.$lane->id.'" data-issue-id="'.$lane->id.'" draggable="true"  ondrag="dragStart(this.id);" ondragend="dragDrop(this.id);">';
				echo '	<div class="todo-list-item-inner">';
				echo '		<span><span class="colstate" style="color: '.$config_app['PriorityColors'][$lane->status].';" onmouseover="document.getElementById(\'taglev\').style.display = \'block\';" onmouseout="document.getElementById(\'taglev\').style.display = \'none\';">&#9899;</span>#'. $lane->id.'</span>';
				echo '			<a href="'.(\URL::to('project/' . $lane->project_id . '/issue/' . $lane->id)).'">'.$lane->title.'</a>&nbsp;<span>( '.$lane->weight.'%)</span>';
				echo '			<a class="todo-button del" title="'. __('tinyissue.todos_remove').'" data-issue-id="'.$lane->id.'" href="#">[X]</a>';
				echo '		<div>'.$lane->name.'</div>';
				echo '	</div>';
				echo '</div>
				';
				if (++$rendu > $NbIssues) { break; }
			}
		}
		echo '</div>';
		echo '</div>
		';
	}
//	echo '<div class="todo-line">&nbsp;</div>
//	';
	echo '</div>
	';
?>
<script type="text/javascript" >
	var msgFinal = "<?php echo __('tinyissue.issue_has_been_updated');?> ";
	var NbIssues = <?php echo $NbIssues; ?>;
	var Exactement = "<?php echo $config_app['url']; ?>"; 
	var usr = <?php echo Auth::user()->id; ?>; 
</script>