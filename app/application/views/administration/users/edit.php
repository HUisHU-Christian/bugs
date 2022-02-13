<h3>
	<?php echo __('tinyissue.update_user'); ?>
	<span><?php echo __('tinyissue.update_user_description'); ?></span>
</h3>

<div class="pad">

	<div id="preferences" style="border: none black 2px; padding-right: 100px; width:50%; float:left; ">

	<form method="post" action="">

		<table class="form" style="float: left">
			<tr>
				<th><?php echo __('tinyissue.first_name'); ?></th>
				<td>
					<input type="text" name="firstname" value="<?php echo Input::old('firstname', $user->firstname); ?>" autocomplete="off" />

					<?php echo $errors->first('firstname', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.last_name'); ?></th>
				<td>
					<input type="text" name="lastname" value="<?php echo Input::old('lastname',$user->lastname);?>" autocomplete="off" />

					<?php echo $errors->first('lastname', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.email'); ?></th>
				<td>
					<input type="text" name="email" value="<?php echo Input::old('email',$user->email)?>"  autocomplete="off" />

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
						$Lng = scandir("application/language/");
						$Not = array(".", "..", "all.php");
						foreach ($Lng as $val) { if(!in_array(trim($val), $Not) && is_dir("application/language/".$val)) { echo '<option value="'.$val.'" '; if ($val == Input::old('language',$user->language)) { echo ' selected="selected" '; } echo '>'.$val.'</option>'; } }
					?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.role'); ?></th>
				<td>
					<?php echo Form::select('role_id',Role::dropdown(),$user->role_id); ?>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<?php echo __('tinyissue.only_complete_if_changing_password'); ?>
				</th>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.new_password'); ?></th>
				<td>
					<input type="password" name="password" value="" autocomplete="off" />

					<?php echo $errors->first('password', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.confirm'); ?></th>
				<td>
					<input type="password" name="password_confirmation" value="" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input type="submit" value="<?php echo __('tinyissue.update'); ?>" class="button	primary"/>
				</td>
			</tr>
		</table>
	</div>

	<div id="projects_list" class="projectsList_user" style="border: none black 2px; padding-right: 100px; width:40%; float:left; max-height: 600px; overflow-y: auto;">
		<?php
			$coul = array('FFFFFF','CCCCCC');
			$rang = 1;
			$affiche = __('tinyissue.project_roleuser');
			$affiche = str_replace('{last}', $user->lastname, $affiche );
			$affiche = str_replace('{first}', $user->firstname, $affiche);
			echo '<h3>'.$affiche.'</h3>';
			$active_projects = Project\User::active_projects();
			echo '<table width="100%">';
			foreach($active_projects as $row) {
				$Proj[$row->to()] = $row->name;
				$roles = User::myPermissions_onThisProject($row->id);
				if (count($roles) == 0) { 
					continue; 
				} else {
					$userRole = Project\User::check_role($user->id, $row->id, 0);
					echo '<tr style="background-color: #'.$coul[$rang].'; color: black;">';
					echo '<td>';
					echo $row->name;
					echo '</td>';
					echo '<td style="text-align: right; padding-bottom: 5px; padding-top: 7px;">';
					echo Project\User::list_roles(Auth::user()->id, $row->id, $userRole);
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