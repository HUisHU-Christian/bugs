			</div>
		</div> <!-- end content -->
	</div>
</div>
<br />
	<a href="javascript: this.click();" id="global-notice" class="global-notice <?php echo Session::has('notice-error')? 'global-error' : ''; ?>"><?php echo Session::get('notice', Session::get('notice-error')); ?></a>
	<a href="javascript: void(0);" id="global-saving" class="global-saving" style="display: none;"><span><?php echo __('tinyissue.saving');?></span></a>
	<div id="taglev" class="taglev" onclick="this.style.display = 'none';">
	<?php
		$statut = $issue->status ?? 99;
		for ($x=5; $x>0; $x--) {
			echo '<span id="span_statut_'.$x.'"><span  class="Affcolstate" style="color: '.\Config::get('application.pref.prioritycolors')[$x].'; font-size: 200%;">&#9899;</span>'.(($statut == $x) ? '<b>' : '').__('tinyissue.priority_desc_'.$x).(($statut == $x) ? '</b>' : '');
			echo '</span><br />
			';
		}
		$x = 0; 
		echo '<span id="span_statut_'.$x.'"><span class="Affcolstate" style="color: '.\Config::get('application.pref.prioritycolors')[$x].'; font-size: 200%;">&#9899;</span>'.(($statut == $x) ? '<b>' : '').__('tinyissue.priority_desc_'.$x).(($statut == $x) ? '</b>' : '');
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
