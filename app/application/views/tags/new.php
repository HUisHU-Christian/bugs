<h3>
	<?php echo __('tinyissue.create_tag'); ?>
</h3>

<div class="pad">

	<form method="post" action="">

		<table class="form" style="width: 100%;">
			<tr>
				<th style="width: 10%"><?php echo __('tinyissue.tag'); ?></th>
				<td>
					<input type="text" name="tag" style="width: 48%;" value="<?php echo Input::old('tag', ''); ?>" onkeyup="document.getElementById('span_exemple').innerHTML = this.value;" />
					<?php echo $errors->first('tag', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th style="width: 10%"><?php echo __('tinyissue.tags_ftcolor'); ?></th>
<<<<<<< ours
				<td>
					<input type="text" id="ftcolor" name="ftcolor" style="width: 98%;" value="<?php echo Input::old('ftcolor', 'purple'); ?>" />

					<?php echo $errors->first('ftcolor', '<span class="error">:message</span>'); ?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span id="span_exemple" style="border-radius: 7pt; font-weight:bold; padding: 9px; background-color: purple; color: white;">Exemple<br /></span>
				</td>
			</tr>
			<tr>
				<th style="width: 10%"><?php echo __('tinyissue.tags_bgcolor'); ?></th>
				<td>
=======
				<td>
					<input type="text" id="ftcolor" name="ftcolor" style="width: 98%;" value="<?php echo Input::old('ftcolor', 'purple'); ?>" />

					<?php echo $errors->first('ftcolor', '<span class="error">:message</span>'); ?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span id="span_exemple" style="border-radius: 7pt; font-weight:bold; padding: 9px; background-color: purple; color: white;">Exemple<br /></span>
				</td>
			</tr>
			<tr>
				<th style="width: 10%"><?php echo __('tinyissue.tags_bgcolor'); ?></th>
				<td>
>>>>>>> theirs
					<input type="text" id="bgcolor" name="bgcolor" style="width: 98%;" value="<?php echo Input::old('bgcolor', 'purple'); ?>" />

					<?php echo $errors->first('bgcolor', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><input type="submit" value="<?php echo __('tinyissue.create_tag'); ?>" class="button primary" /></td>
			</tr>
		</table>

		<?php echo Form::token(); ?>
		
		<script type="text/javascript">
		
			$(function() {		
				$("#bgcolor").spectrum({
					color: "purple",
					showInput: true,
					className: "full-spectrum",
					showInitial: true,
					showSelectionPalette: true,
					maxPaletteSize: 10,
					preferredFormat: "hex",
					change: function(color) {
						$('#bgcolor').val(color.toHexString());
						document.getElementById('span_exemple').style.backgroundColor = color.toHexString();
					}
				});
				$("#ftcolor").spectrum({
					color: "white",
					showInput: true,
					className: "full-spectrum",
					showInitial: true,
					showSelectionPalette: true,
					maxPaletteSize: 10,
					preferredFormat: "hex",
					change: function(color) {
						$('#ftcolor').val(color.toHexString());
						document.getElementById('span_exemple').style.color = color.toHexString();
					}
				});
			});
		</script>
	</form>
</div>