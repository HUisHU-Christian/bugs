<h3>
	<?php echo __('tinyissue.your_todos'); ?>
	<span><?php echo __('tinyissue.your_todos_description'); ?></span>
</h3>
<?php
	$config_app = require path('public') . 'config.app.php';
	if(!isset($config_app['PriorityColors'])) { $config_app['PriorityColors'] = array("black","Orchid","Cyan","Lime","orange","red"); }
	if (!isset($config_app['Percent'])) { $config_app['Percent'] = array (100,0,10,80,100); }
	$config_app['Percent'][5] = 0;
	$column = array(1,2,3,0);

	echo '<div class="pad" id="todo-lanes">
	';
	foreach ($column as $col) {
		echo '
		<div class="todo-lane blue-box" id="lane-status-'.$col.'" data-status="'.$col.'" ondragover="dragOver(this.id);"  ondragleave="dragLeave(this.id);" ondrop="alert(\'Nous recevons ceci de la colonne\');dragDrop(this.id);">
		';
		$Combien = (isset($lanes[$col]) ? count($lanes[$col]) :  0);
		$Combien = ($Combien > 25) ? $Combien.'; 25 montrés ici, <a href="lien.php" style="font-size: 100%; font-weight: normal; text-decoration: underline;">voir tous</a>' : $Combien;
		echo '<h4>'.$status_codes[$col].' ('.$config_app['Percent'][$col].(($col == 0) ? '' :  ' - '.($config_app['Percent'][$col+1]-1)).'% )<br /><span style="color: black; font-size: 75%; margin-left:-2px;">('.$Combien.')</span></h4>
		';
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
				if (++$rendu > 25) { break; }
			}
		}
		echo '</div>
		';
	}
	echo '<div class="todo-line">&nbsp;</div>
	';
	echo '</div>
	';
?>
<script type="text/javascript" >
	var msgFinal = "<?php echo __('tinyissue.issue_has_been_updated');?> ";
	var Exactement = "<?php echo $config_app['url']; ?>"; 
	var usr = <?php echo Auth::user()->id; ?>; 

</script>