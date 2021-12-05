<h3>
	<?php echo __('tinyissue.my_settings'); ?>
	<span><?php echo __('tinyissue.my_settings_description'); ?></span>
</h3>
<?php
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }
	$config = require $prefixe."config.app.php";
	$dir = $prefixe.$config['attached']['directory']."/";
	$Lng = require_once($prefixe."app/application/language/en/install.php"); 
	if ( file_exists($prefixe."app/application/language/".\Auth::user()->language."/install.php") && \Auth::user()->language != 'en') {
		$LnT = require_once ($prefixe."app/application/language/".\Auth::user()->language."/install.php");
		$LngSRV = array_merge($Lng, $LnT);
	} else {
		$LngSRV = $Lng;
	}
?>
<div class="pad">
	<details id="details_main">
	<summary><?php echo $LngSRV["UserPref_compte"]; ?></summary>
	<form method="post" action="" autocomplete="off" >
	<input name="Quoi" value="account" type="hidden" />
		<table class="form">
			<tr>
				<th><?php echo __('tinyissue.first_name'); ?></th>
				<td>
					<input type="text" name="firstname" value="<?php echo Input::old('firstname', $user->firstname); ?>" autocomplete="off" style="width: 300px;" />

					<?php echo $errors->first('firstname', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.last_name'); ?></th>
				<td>
					<input type="text" name="lastname" value="<?php echo Input::old('lastname',$user->lastname);?>" autocomplete="off" style="width: 300px;" />

					<?php echo $errors->first('lastname', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.email'); ?></th>
				<td>
					<input type="text" name="email" value="<?php echo Input::old('email',$user->email)?>"  autocomplete="off" style="width: 300px;" />

					<?php echo $errors->first('email', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.language') ; ?></th>
				<td>
					<select name="language">				
						<?php
							//Language has added in nov 2016
							$Lng = scandir("application/language/");
							$Not = array(".", "..", "all.php");
							foreach ($Lng as $val) { if(!in_array(trim($val), $Not) && is_dir("application/language/".$val)) { echo '<option value="'.$val.'" '; if ($val == Input::old('language',$user->language)) { echo ' selected="selected" '; } echo '>'.$val.'</option>'; } }
						?>
					</select>
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
					<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
					<input style="display:none" type="text" name="fakeusernameremembered"/>
					<input style="display:none" type="password" name="fakepasswordremembered"/>
					<input type="password" name="password" value="" autocomplete="off" style="width: 300px;" />

					<?php echo $errors->first('password', '<span class="error">:message</span>'); ?>
				</td>
			</tr>
			<tr>
				<th><?php echo __('tinyissue.confirm'); ?></th>
				<td>
					<input type="password" name="password_confirmation" value="" autocomplete="off" style="width: 300px;" />
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input type="submit" value="<?php echo __('tinyissue.update_my'); ?>"  class="button	primary"/>
				</td>
			</tr>
		</table>
	</form>
	</details>

	<form method="post" action="settings" >
	<details id="details_main" open="open">
	<summary><?php echo $LngSRV["UserPref_prefer"]; ?></summary>
	<input name="Quoi" value="Preferences" type="hidden" />
	<?php
		//Define default preferences values
		$pref = \Auth::user()->pref();
	?>
		<br />
		<h4><?php echo $LngSRV["UserPref_modele"]; ?></h4>
		<br />
		<select name="template">
		<?php
			$pasCeuxCi = array(".","..","jquery.tagit.css","spectrum.css","tagit.ui-zendesk.css", "login.css");
			$canevas = scandir("assets/css");
			foreach ($canevas as $caneva) {
				if (in_array($caneva, $pasCeuxCi)) { continue; }
				echo '<option value="'.$caneva.'" '.((strtolower($pref['template']) == strtolower($caneva)) ? ' selected="selected" ' : '').'>'.$caneva.'</option>';
			}
		?>
		</select>
		<br /><br />
		<br /><br />

		<h4><?php echo $LngSRV["UserPref_projet"]; ?></h4>
		<?php
			echo $LngSRV["UserPref_projet_0"].' : '.$LngSRV["UserPref_projet_2a"].'<input type="radio" name="Roulbar" id="input_Roulbar_oui" value="true" '.(($pref["Roulbar"] == 'true') ? 'checked="chekcked"' : '').'  "/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$LngSRV["UserPref_projet_2b"].'<input type="radio"  name="Roulbar" id="input_Roulbar_non" value="false" '.(($pref["Roulbar"] == 'false') ? 'checked="chekcked"' : '').' /><br />';
			echo $LngSRV["UserPref_projet_2"].' : '.$LngSRV["UserPref_projet_2a"].'<input type="radio" name="sidebar" id="input_sidebar_oui" value="true" '.(($pref["sidebar"] == 'true') ? 'checked="chekcked"' : '').'  onclick="document.getElementById(\'input_numSidebar\').value = 100;"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$LngSRV["UserPref_projet_2b"].'<input type="radio"  name="sidebar" id="input_sidebar_non" value="false" '.(($pref["sidebar"] == 'false') ? 'checked="chekcked"' : '').' onclick="document.getElementById(\'input_numSidebar\').value = 0; document.getElementById(\'input_orderSidebar_desc\').checked = true;" /><br />';
			echo $LngSRV["UserPref_projet_1"].' : <input type="number" name="numSidebar" id="input_numSidebar" max="990" min="-990" step="10" value="'.$pref['numSidebar'].'" size="4" onchange="if(this.value == 0) { document.getElementById(\'input_sidebar_non\').checked = true; } else { document.getElementById(\'input_sidebar_oui\').checked = true;} if(this.value < 1) { document.getElementById(\'input_orderSidebar_desc\').checked = true; } else { document.getElementById(\'input_orderSidebar_asc\').checked = true;}" /><br />';
			echo $LngSRV["UserPref_projet_3"].' : '.$LngSRV["UserPref_projet_3a"].'<input type="radio"  name="orderSidebar" id="input_orderSidebar_asc" value="asc" '.(($pref["orderSidebar"] == 'asc') ? 'checked="chekcked"' : '').' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$LngSRV["UserPref_projet_3b"].'<input type="radio"  name="orderSidebar" id="input_orderSidebar_desc" value="desc" '.(($pref["orderSidebar"] == 'desc') ? 'checked="chekcked"' : '').' /><br />';
			echo $LngSRV["UserPref_projet_4"].' : '.$LngSRV["UserPref_projet_2a"].'<input type="radio" name="boutons" id="input_boutons_oui" value="true" '.(($pref["boutons"] == 'true') ? 'checked="chekcked"' : '').' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$LngSRV["UserPref_projet_2b"].'<input type="radio"  name="boutons" id="input_boutons_non" value="false" '.(($pref["boutons"] == 'false') ? 'checked="chekcked"' : '').'  /><br />';
			if ($user->role_id == 4) {
				echo '
					<br /><br />
					<br /><br />
				<h4>'.$LngSRV["UserPref_Notice"].'</h4>';
			
				echo $LngSRV["UserPref_NoticeOnLogIn"].' : '.$LngSRV["UserPref_projet_2a"];
				echo '<input name="noticeOnLogIn" id="radio_noticeOnLogIn_Oui" type="radio" value="true" '.(($pref['noticeOnLogIn']) ? 'checked="checked"' : '').' />';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$LngSRV["UserPref_projet_2b"];
				echo '
				';
				echo '<input name="noticeOnLogIn" id="radio_noticeOnLogIn_Non" type="radio" value="false" '.((!$pref['noticeOnLogIn']) ? 'checked="checked"' : '').'/>';
				echo '
				';
			} else {
				echo '<input name="noticeOnLogIn" type="hidden" value="false" />';
			}
		?>
		<br /><br />
		<div style="width:100%; left: 30%;position: relative; margin-top: 30px;">
		<input name="Lancer" type="submit" class="button2" value="<?php echo $LngSRV["UserPref_apply"]; ?>" id="input_databaseLancer" onclick="javascript: AppliquerPref();" />
		</div>
	</form>
	</details>
</div>
