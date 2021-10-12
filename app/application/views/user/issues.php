<?php 
$config_app = require path('public') . 'config.app.php';  
if(!isset($config_app['PriorityColors'])) { $config_app['PriorityColors'] = array("black","Orchid","Cyan","Lime","orange","red"); }
?>
<h3>
	<?php echo __('tinyissue.your_issues'); ?>
	<span><?php echo __('tinyissue.your_issues_description'); ?></span>
</h3>

<div class="pad">

	<?php foreach($projects as $project): ?>

	<div class="blue-box">
		<div class="inside-pad">

			<h4><a href="<?php echo $project['detail']->to(); ?>/issues?tag_id=1"><?php echo $project['detail']->name; ?></a></h4>

			<ul class="issues">
				<?php foreach($project['issues'] as $row):  ?>
				<li>
					<a href="<?php echo $row->to(); ?>" class="comments"><?php echo $row->comment_count(); ?></a>
					
					<?php 
					if(!empty($row->tags)) {
						echo '<div class="tags">';
						foreach($row->tags()->order_by('tag', 'ASC')->get() as $tag) { 
							//2 sept 2021 recherche d'un bogue lié à ftcolor
							echo '<label class="label" style="'.($tag->ftcolor ? 'color: '.$tag->ftcolor . ';' : '').($tag->bgcolor ? 'background-color: '.$tag->bgcolor . ';' : '').'">' . $tag->tag . '</label>';
						}
						echo '</div>';
					} 
					?>

					<div style="width: 72px; float: left; text-align:center; "><a href="<?php echo $row->to(); ?>" class="id">#<?php echo $row->id; ?></a><br /><span class="colstate" style="color: <?php echo $config_app['PriorityColors'][$row->status]; ?>;"  onmouseover="document.getElementById(\'taglev\').style.display = \'block\';" onmouseout="document.getElementById('taglev').style.display = 'none';">&#9899;</span></div>
					<div class="data">
						<a href="<?php echo $row->to(); ?>"><?php echo $row->title; ?></a>
						<div class="info">
							<?php echo __('tinyissue.created_by'); ?>
							<strong><?php echo $row->user->firstname . ' ' . $row->user->lastname; ?></strong>
							<?php echo Time::age(strtotime($row->created_at)); ?>

							<?php if(!is_null($row->updated_by)): ?>
							- <?php echo __('tinyissue.updated_by'); ?>&nbsp;&nbsp;<strong><?php echo $row->updated->firstname . ' ' . $row->updated->lastname; ?></strong>
							<?php echo Time::age(strtotime($row->updated_at)); ?>
							<?php endif; ?>
						</div>
					</div>
					<?php
						$config_app = require path('public') . 'config.app.php';
						echo '<br /><br />'; 
						//Percentage of work done
						$SizeXtot = 500;
						$SizeX = $SizeXtot / 100;
						$Etat = Todo::load_todo($row->id);
						if (is_object($Etat)) { 
							$Percent = $Etat->weight;
							echo '<div style="position: relative; top: -11px; left: 70px; background-color: green; color:white; width: '.($Percent*$SizeX).'px; height: 4px; line-height:4px;" /></div>'; 
							echo '<div style="position: relative; top: -15px; left: '.(70 + ($Percent*$SizeX)).'px; margin-bottom: -4px; background-color: gray; color:white; width: '.($SizeXtot-($Percent*$SizeX)).'px; height: 4px; text-align: center; line-height:4px;" /></div>';
						} else { $Percent = 10; }
						//Time's going fast!
						//Timing bar, according to the time planified (field projects_issues - duration) for this issue
						$Deb = strtotime($row->created_at);
						$Dur = (time() - $Deb) / 86400;
						if (!isset($issue->duration) || $issue->duration === 0) { $row->duration = 30; }
						$DurRelat = round(($Dur / $row->duration) * 100);
						$Dur = round($Dur);
						$DurColor = ($DurRelat < 65) ? 'green' : (( $DurRelat > $config_app['Percent'][3]) ? 'red' : 'yellow') ;
						if ($DurRelat >= 50 && $Percent <= 50 ) { $DurColor = 'yellow'; } 
						if ($DurRelat >= 75 && $Percent <= 50 ) { $DurColor = 'red'; } 
						echo '<div style="position: relative; top: -10px; left: 70px; background-color: '.$DurColor.'; color:white; width: '.(($DurRelat >= 100) ? $SizeXtot : ($DurRelat*$SizeX)).'px; height: 4px; text-align: left; line-height:4px;" /></div>'; 
						if ($DurRelat < 100) { echo '<div style="position: relative; top: -14px; left: '.(70 + ($DurRelat*$SizeX)).'px; margin-bottom: -24px; background-color: gray; color:white; width: '.($SizeXtot-($DurRelat*$SizeX)).'px; height: 4px; text-align: right; line-height:4px;" /></div>'; }
						echo '<br clear="all" />';
					?>
				</li>
				<?php endforeach; ?>
			</ul>

		</div>
	</div>

	<?php endforeach; ?>

</div>