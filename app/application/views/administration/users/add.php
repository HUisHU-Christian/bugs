<h3>
	<?php echo __('tinyissue.add_user'); ?>
	<span><?php echo __('tinyissue.add_new_user'); ?></span>
</h3>

<div class="pad">

	<form method="post" action="">
		<table class="form">
			<tr>
				<th>
					<?php echo __('tinyissue.first_name'); ?>
				</th>
				<td>
					<input type="text" name="firstname" value="<?php echo Input::old('firstname'); ?>" />
					<?php echo $errors->first('firstname', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th>
					<?php echo __('tinyissue.last_name'); ?>
				</th>
				<td>
					<input type="text" name="lastname" value="<?php echo Input::old('lastname');?>" />
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
					<th>
						<?php echo __('tinyissue.project'); ?>
					</th>
					<td>
						<select name="Project"> 
						<?php 
							//Liste de tous les projects auxquels a accès l'admin qui inscrit le nouvel usager
							foreach(Project\User::active_projects() as $row) {
								$NbIssues[$row->to()] = $row->count_open_issues();
								$Proj[$row->to()] = $row->name.'&nbsp;<span class="info-open-issues" title="Number of Open Tickets">('.$NbIssues[$row->to()].')</span>';
								$idProj[$row->to()] = $row->id;
							}
							foreach ($Proj as $ind => $val ){
								$SansAccent[$ind] = htmlentities($val, ENT_NOQUOTES, 'utf-8');
								$SansAccent[$ind] = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $SansAccent[$ind]);
								$SansAccent[$ind] = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $SansAccent[$ind]);
								$SansAccent[$ind] = preg_replace('#&[^;]+;#', '', $SansAccent[$ind]);
							}
							
							////Tri des données affichées dans le panneau de gauche
							asort($SansAccent);
					
							//Affichage dans le panneau de gauche
							foreach($SansAccent as $ind => $val) {
								$id = $idProj[$ind];
								echo '<option value="'.$id.'">';
								echo $Proj[$ind];
								echo '</option>';
							}
						?>
						</select>
					</td>
				</tr>
			<tr>
				<th></th>
				<td>
					<input type="submit" value="<?php echo __('tinyissue.add_user'); ?>" class="button	primary"/>
				</td>
			</tr>
		</table>

		<?php echo Form::token(); ?>
	</form>

</div>