			</div>
		</div> <!-- end content -->
	</div>
</div>
<br />
	<a href="javascript: this.click();" id="global-notice" class="global-notice <?php echo Session::has('notice-error')? 'global-error' : ''; ?>"><?php echo Session::get('notice', Session::get('notice-error')); ?></a>
	<a href="javascript: this.click();" id="global-taglev" class="global-taglev">
	<?php
		$config_app = require path('public') . 'config.app.php';
		for ($x=1; $x<6; $x++) {
			echo '<span style="color: '.$config_app['PriorityColors'][$x].'; font-size: 200%;">&#9899;</span>'.(($statut == $x) ? '<b>' : '').__('tinyissue.priority_desc_'.$x).(($statut == $x) ? '</b>' : '');
		}
		$x = 0; 
		echo '<span style="color: '.$config_app['PriorityColors'][$x].'; font-size: 200%;">&#9899;</span>'.(($statut == $x) ? '<b>' : '').__('tinyissue.priority_desc_'.$x).(($statut == $x) ? '</b>' : '');
	?>
	</a>
	<a href="javascript: void(0);" id="global-saving" class="global-saving"><span><?php echo __('tinyissue.saving');?></span></a>
	<br />
<footer>
	<small class="bugs-version-number"><a href="administration">&nbsp;</a></small>
</footer>
<br />
</body>
</html>