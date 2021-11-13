<h3>
	<?php echo __('tinyissue.add_user'); ?>
	<span><?php echo __('tinyissue.add_new_user'); ?></span>
</h3>

<div class="pad" style="postion: relative;">

	<form method="post" action="">
		<table class="form" style="float: left">
			<tr>
				<th></th>
				<td>
					<br /><br /><br /><br />
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __('tinyissue.first_name'); ?>
				</th>
				<td>
					<input type="text" name="firstname" value="<?php echo Input::old('firstname'); ?>" onblur="if (this.value !='') { document.getElementById('span_firstname').innerHTML = this.value; }" />
					<?php echo $errors->first('firstname', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __('tinyissue.last_name'); ?>
				</th>
				<td>
					<input type="text" name="lastname" value="<?php echo Input::old('lastname');?>" onblur="if (this.value !='') { document.getElementById('span_lastname').innerHTML = this.value; }" />
					<?php echo $errors->first('lastname', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __('tinyissue.email'); ?>
				</th>
				<td>
					<input type="text" name="email" value="<?php echo Input::old('email')?>" />
					<?php echo $errors->first('email', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __('tinyissue.language'); ?>
				</th>
				<td>	
					<select name="language">
					<?php
						//Language has added in nov 2016
						$Lng = scandir("application/language/");
						$Not = array(".", "..", "all.php");
						foreach ($Lng as $val) { if(!in_array(trim($val), $Not) && is_dir("application/language/".$val)) { echo '<option value="'.$val.'" '; if ($val == Auth::user()->language) { echo ' selected="selected" '; } echo '>'.$val.'</option>'; } }
					?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __('tinyissue.role'); ?>
				</th>
				<td>
					<?php echo Form::select('role_id',Role::dropdown(), Input::old('role_id')); ?>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<br /><br /><br /><br />
					<br /><br /><br /><br />
				</td>
			</tr>
			<tr>
				<th></th>
				<td style="padding-left: 50%;">
					<input type="submit" value="<?php echo __('tinyissue.add_user'); ?>" class="button	primary"/>
				</td>
			</tr>
		</table>

		<div id="projects_list" class="projectsList_user" style="right: 30%;">
			<?php
				$coul = array('FFFFFF','CCCCCC');
				$rang = 1;
				$affiche = __('tinyissue.project_roleuser');
				$affiche = str_replace('{last}', '<span id="span_lastname" style="position: relative; font-weight: bold; font-size: 100%;">...</span>', $affiche );
				$affiche = str_replace('{first}','<span id="span_firstname" style="position: relative; font-weight: bold; font-size: 100%;">...</span>',$affiche );
				echo '<h3>'.$affiche.'</h3>';
				$active_projects = Project\User::active_projects();
				echo '<table width="100%">';
				foreach($active_projects as $row) {
					$Proj[$row->to()] = $row->name;
					$roles = User::myPermissions_onThisProject($row->id);
					if (count($roles) == 0) { 
						continue; 
					} else {
						echo '<tr style="background-color: #'.$coul[$rang].'">';
						echo '<td>';
						echo $row->name;
						echo '</td>';
						echo '<td style="text-align: right; padding-bottom: 5px; padding-top: 7px;">';
						echo Project\User::list_roles(Auth::user()->id, $row->id, 0);
						echo '</td>';
						echo '</tr>';
						$rang = abs($rang-1);
					}
				}
				echo '</table>';
	
			?>
		</div>

		<?php echo Form::token(); ?>
	</form>

</div>