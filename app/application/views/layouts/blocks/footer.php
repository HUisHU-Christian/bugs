			</div>
		</div> <!-- end content -->
	</div>
</div>
<br />
	<a href="javascript: this.click();" id="global-notice" class="global-notice <?php echo Session::has('notice-error')? 'global-error' : ''; ?>"><?php echo Session::get('notice', Session::get('notice-error')); ?></a>
	<a href="javascript: this.click();" id="global-taglev" class="global-taglev">
	<?php
		$statut = $issue->status ?? 99;
		$config_app = require_once path('public') . 'config.app.php';
		for ($x=1; $x<6; $x++) {
			echo '<span style="color: '.$config_app['PriorityColors'][$x].'; font-size: 200%;">&#9899;</span>'.(($statut == $x) ? '<b>' : '').__('tinyissue.priority_desc_'.$x).(($statut == $x) ? '</b>' : '');
		}
		$x = 0; 
		echo '<span style="color: '.$config_app['PriorityColors'][$x].'; font-size: 200%;">&#9899;</span>'.(($statut == $x) ? '<b>' : '').__('tinyissue.priority_desc_'.$x).(($statut == $x) ? '</b>' : '');
	?>
	</a>
	<a href="javascript: void(0);" id="global-saving" class="global-saving"><span><?php echo __('tinyissue.saving');?></span></a>
	<div id="taglev" class="taglev">
	<?php
		$statut = $issue->status ?? 99;
		$config_app = require path('public') . 'config.app.php';
		for ($x=1; $x<6; $x++) {
			echo '<span id="span_statut_'.$x.'"><span  class="Affcolstate" style="color: '.$config_app['PriorityColors'][$x].'; font-size: 200%;">&#9899;</span>'.(($statut == $x) ? '<b>' : '').__('tinyissue.priority_desc_'.$x).(($statut == $x) ? '</b>' : '');
			echo '</span><br />
			';
		}
		$x = 0; 
		echo '<span id="span_statut_'.$x.'"><span class="Affcolstate" style="color: '.$config_app['PriorityColors'][$x].'; font-size: 200%;">&#9899;</span>'.(($statut == $x) ? '<b>' : '').__('tinyissue.priority_desc_'.$x).(($statut == $x) ? '</b>' : '');
		echo '</span>';
	?>
	
	</div>
	<br />
<footer>
	<small class="bugs-version-number"><a href="administration">&nbsp;</a></small>
</footer>
<br />
</body>
</html>