<h3>
	<?php 
		echo  __('tinyissue_activity_edit').' « '.Input::old('activity', $activity->activity).' »'; 
	?>
</h3>

<div class="pad">

	<form method="post" action="">

		<table class="form" style="width: 100%;">
			<tr>
				<th style="vertical-align: middle; text-align: right; padding-right: 20px; font-size: 120%;"><?php echo __('tinyissue.language'); ?></th>
				<td style="font-size: 120%;">
					<b><?php echo $activity->attributes["description"]; ?></b>
				</td>
			</tr>
			<?php
			foreach ($activity->attributes as $lng => $desc) {
				if (in_array(strtolower($lng), array('id','description','activity','created_at','updated_at'))) { continue; }
				echo '<tr><td style="vertical-align: middle; text-align: right; padding-right: 20px;">';
				echo ((\Auth::user()->language == $lng) ? '<b>' : '').strtoupper($lng).((\Auth::user()->language == $lng) ? '</b>' : '');
				echo '</td><td>';
				echo '<input name="desc[\''.$lng.'\']" value="'.$desc.'" style="background-color:'.((\Auth::user()->language == $lng) ? 'transparent' : '#CCCCCC').'; height: 40px; border-radius: 5px;" size="100" />';
				echo '</td></tr>';
			}
			?>
			<tr>
				<th></th>
				<td>
					<input type="submit" value="<?php echo __('tinyissue.edit'); ?>" class="button primary" />
					&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" value="<?php echo __('tinyissue.cancel'); ?>" class="button primary" onclick="document.location.href='<?php echo \Config::get('application.url'); ?>administration/activity';" />
					<input name="id" type="hidden" value="<?php echo $activity->attributes["id"]; ?>"  />
				</td>
			</tr>
		</table>

		<?php echo Form::token(); ?>
		

	</form>

</div>