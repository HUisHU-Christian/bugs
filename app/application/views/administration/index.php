<?php
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) { $prefixe .= "../"; }
//	require $prefixe."app/application/controllers/ajax/administration.php"; 
	$config = require $prefixe."config.app.php";
	$dir = $prefixe.$config['attached']['directory']."/";

	$Lng = require_once($prefixe."app/application/language/en/install.php"); 
	if ( file_exists($prefixe."app/application/language/".\Auth::user()->language."/install.php") && \Auth::user()->language != 'en') {
		$LnT = require_once ($prefixe."app/application/language/".\Auth::user()->language."/install.php");
		$LngSRV = array_merge($Lng, $LnT);
	} else {
		$LngSRV = $Lng;
	}

	echo '<h3>';
	echo __('tinyissue.administration');
	echo '.<span>'.__('tinyissue.administration_description').'</span>';
	echo '</h3>';

	$Def = array('mailerrormsg' => 1, 'intro' => '', 'bye' => '', 'encoding' => 'UTF-8',);
	foreach ($Def as $ind => $val) {
		$Conf[$ind] = isset($Conf[$ind]) ? $Conf[$ind] : $val;
	}
?>

<div class="pad">
<details id="details_main" open="open">
	<summary><?php echo __('tinyissue.admin_head'); ?></summary>
	<div class="pad2">
		<table class="table" width="60%">
			<tr>
				<th style="border-top: 1px solid #ddd;"><a href="administration/users"><?php echo __('tinyissue.total_users'); ?></a></th>
				<td style="border-top: 1px solid #ddd;"><b><?php echo $users; ?></b></td>
			</tr>
			<tr>
				<th><a href="<?php echo URL::to('roles'); ?>"><?php echo __('tinyissue.role'); ?>s</a></th>
				<td><b><?php echo @$roles; ?></b></td>
			</tr>
			<tr>
				<th><a href="projects"><?php echo __('tinyissue.projects'); ?></a>
				<div class="adminListe">
					<?php echo ($active_projects < 2) ? __('tinyissue.active_project') : __('tinyissue.active_projects'); ?><br />
					<?php echo ($archived_projects < 2) ? __('tinyissue.archived_project') : __('tinyissue.archived_projects'); ?><br />
				</div>
				</th>
				<td>
					<b><?php echo ($active_projects + $archived_projects); ?></b><br />
					<?php echo ($active_projects == 0) ? __('tinyissue.no_one') : $active_projects; ?><br />
					<?php echo ($archived_projects == 0) ? __('tinyissue.no_one') : $archived_projects; ?><br />
				</td>
			</tr>
			<tr>
			</tr>

			<tr>
				<th><a href="<?php echo URL::to('tags'); ?>"><?php echo __('tinyissue.tags'); ?></a></th>
				<td><b><?php echo $tags; ?></b></td>
			</tr>
			<tr>
				<th><a href="user/issues"><?php echo __('tinyissue.issues'); ?></a>
					<div class="adminListe">
						<?php echo __('tinyissue.open_issues'); ?><br />
						<?php echo __('tinyissue.closed_issues'); ?><br />
					</div>
				</th>
				<td><b><?php echo ($issues['open']+$issues['closed']); ?></b><br />
				<?php echo $issues['open']; ?><br />
				<?php echo $issues['closed']; ?><br />
				</td>
			</tr>
			<tr>
				<th><a href="https://github.com/pixeline/bugs/" target="_blank"><?php echo $LngSRV["version"]; ?></a>
					<div class="adminListe">
					<?php echo $LngSRV["version"]; ?><br />
					<?php echo $LngSRV["version_release_numb"]; ?><br />
					<?php echo $LngSRV["version_release_date"]; ?><br />
					</div>
				</th>
				<td>
					<b><?php echo Config::get('tinyissue.version').Config::get('tinyissue.release'); ?></b><br />
					<?php echo Config::get('tinyissue.version'); ?><br />
					<?php echo Config::get('tinyissue.release'); ?><br />
					<?php echo $release_date = Config::get('tinyissue.release_date'); ?><br />
				</td>
			</tr>
		</table>
	</div>
	<div class="pad2">
		<br />
		<?php
			//Vérification des mises à jour disponibles
			include "application/libraries/checkVersion.php";
			echo '<h4><b>'.$LngSRV["version_check"].'</b> : ';
			echo '<br /><br />';
			echo $LngSRV["version_actuelle"];
			echo ' : '.$verActu.'<br />'.$LngSRV["version_release_numb"].' : '.Config::get('tinyissue.release');
			echo '<br /><br />';
			if ($verActu == $verNum) {
				echo '<a name="Apprécions">'.$LngSRV["version_good"].'!</a>';
				echo '<br /></h4>';
			} else if ($verNum == 0) {
				echo $LngSRV["version_offline"];
				echo '<br /></h4>';
				echo '<a href="https://github.com/pixeline/bugs/" target="_blank">https://github.com/pixeline/bugs/</a>';
			} else if ($verNum < $verActu) {
				echo '<h4><b>'.$LngSRV["version_ahead"].'</b></h4>';
				echo '<br />';
				echo '<a href="https://github.com/pixeline/bugs/releases" target="_blank">'.$LngSRV["version_details"].'</a> <br />';
			} else {
				echo '<h4><a href="javascript: agissons.submit();">'.$LngSRV["version_need"].'.</a></h4>';
				echo __('tinyissue.release_disp').' : '.$verNum.'<br />';
				echo $LngSRV["version_commit"].' : '.$verCommit.'<br /><br />';
				echo $LngSRV["version_disp"].' : '.$verCod.'<br />';
				echo '<a href="https://github.com/pixeline/bugs/releases" target="_blank">'.$LngSRV["version_details"].'</a> <br />';
				echo '<form action="'.URL::to('administration/update').'" method="post" id="agissons">';
				echo '<input type="hidden" name="Etape" value="1" />';
				echo '<input type="hidden" name="versionYour" value="'.$verActu.'" />';
				echo '<input type="hidden" name="versionDisp" value="'.$verNum.'" />';
				echo '<input type="hidden" name="versionComm" value="'.$verCommit.'" />';
				echo '<br /><br />';
				echo '<input type="submit" value="'.__('tinyissue.updating').'" class="button	primary"/>';
				echo Form::token();
				echo '</form>';
			}
		?>
	</div>
	</details>
</div>
	<br />
	<div class="pad" style="border-top-style: solid; border-bottom-style: solid; border-color: grey; border-width: 2px;">
		<?php $Conf = Config::get('application.mail'); ?>
		<details id="details_email_head">
			<summary><?php echo __('tinyissue.email_head'); ?></summary>
			<br />
			<div class="pad2">
				<?php echo __('tinyissue.email_from').' : '.__('tinyissue.email_from_name'); ?> :<input name="email_from_name" id="input_email_from_name" value="<?php echo $Conf["from"]["name"]; ?>" onkeyup="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo __('tinyissue.email_from').' : '.__('tinyissue.email_from_email'); ?> : <input name="email_from_email" id="input_email_from_email" value="<?php echo $Conf["from"]["email"]; ?>" onkeyup="this.style.backgroundColor = 'yellow';" /><br /><br />
				<br />
				<?php echo __('tinyissue.email_replyto').' : '.__('tinyissue.email_from_name'); ?> : <input name="input_email_replyto_name" id="input_email_replyto_name" value="<?php echo $Conf["replyTo"]["name"]; ?>" onkeyup="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo __('tinyissue.email_replyto').' : '.__('tinyissue.email_from_email'); ?> : <input name="input_email_replyto_email" id="input_email_replyto_email" value="<?php echo $Conf["replyTo"]["email"]; ?>" onkeyup="this.style.backgroundColor = 'yellow';" /><br /><br />
				<br />
				<?php echo __('tinyissue.first_name'); ?> : <b>{first}</b> ex.: <?php echo Auth::user()->firstname; ?><br /><br />
				<?php echo __('tinyissue.last_name'); ?> : <b>{last}</b> ex.: <?php echo Auth::user()->lastname; ?><br /><br />
				<?php echo __('tinyissue.name').' ( '.__('tinyissue.first_name'). ' '.__('tinyissue.last_name').' ) '; ?> : <b>{full}</b> ex.: <?php echo Auth::user()->firstname; ?>  <?php echo Auth::user()->lastname; ?><br />
				<br />
				<br /><br />
				<br /><br />
				<br /><br />
				<br /><br />
				<div style="text-align: center;">
					<input type="button" value="Test" onclick="javascript: AppliquerTest(<?php echo Auth::user()->id; ?>);" class="button1"/>
					&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" value="<?php echo __('tinyissue.updating'); ?>" onclick="javascript: AppliquerCourriel();" class="button2"/>
				</div>
			</div>
			<div class="pad2">
				<?php echo __('tinyissue.email_intro'); ?> : 
					<textarea name="email_intro" id="select_email_intro">
					<?php
						if (file_exists($dir."intro.html")) {
							$f = file_get_contents($dir."intro.html");
							echo $f;
						} else {
							echo  $Conf["intro"];
						}
					?>
					</textarea>
				<br /><br />
				<?php echo __('tinyissue.email_bye'); ?> : 
					<textarea name="email_bye" id="select_email_bye">
					<?php
						if (file_exists($dir."bye.html")) {
							$f = file_get_contents($dir."bye.html");
							echo $f;
						} else {
							echo  $Conf["bye"];
						}
					?>
					</textarea>
			</div>
		</details>
		<details id="details_email_head2">
			<summary><?php echo __('tinyissue.email_head2'); ?></summary>
			<br />
			<div>
			<select name="ChxTxt" id="select_ChxTxt" onchange="ChangeonsText(this.value, <?php echo "'".\Auth::user()->language."','".__('tinyissue.following_email')."'"; ?>);" class="sombre">
			<?php
				$LesOptions = array(
					"assigned" 	=> __('tinyissue.following_email_assigned_tit'),
					"attached" 	=> __('tinyissue.following_email_attached_tit'),
					"comment" 	=> __('tinyissue.following_email_comment_tit'),
					"issue" 		=> __('tinyissue.following_email_issue_tit'),
					"issueproject" => __('tinyissue.following_email_issueproject_tit'),
					"noticeonlogin" => __('email.following_email_noticeonlogin_tit'),
					"project" 	=> __('tinyissue.following_email_project_tit'),
					"projectdel"=> __('tinyissue.following_email_projectdel_tit'),
					"projectmod"=> __('tinyissue.following_email_projectmod_tit'),
					"status" 	=> __('tinyissue.following_email_status_tit'),
					"tagsADD" 	=> __('tinyissue.following_email_tagsADD_tit'),
					"tagsOTE" 	=> __('tinyissue.following_email_tagsOTE_tit'),
					"useradded" => __('email.following_email_useradded_tit')
				);
				asort($LesOptions, SORT_LOCALE_STRING );
				foreach ($LesOptions as $ind => $val) {
					echo '<option value="'.$ind.'" '.(($ind == 'comment') ? ' selected="selected"' : '').'>'.$val.'</option>';
				}
			?>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<?php
				$con = __('tinyissue.tinyissue.following_email_comment');
				$tit = __('tinyissue.tinyissue.following_email_comment_tit');
				if (file_exists($dir."comment.html")) { $con = file_get_contents($dir."/comment.html"); }
				if (file_exists($dir."comment_tit.html")) { $tit = file_get_contents($dir."/comment_tit.html"); }
			?>
			<?php echo __('tinyissue.title'); ?> : <input name="TitreMsg" id="input_TitreMsg" value="<?php echo $tit; ?>" size="40" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;
				{first}, {last}, {full}, {project}, {issue}, {email}, {static}
			</div>
			<br />
			<textarea id="txt_contenu" name="contenu" ><?php echo $con; ?></textarea>
			<input name="Modifies" type="hidden" id="input_modifies" value="0" />
			<br />
			<div style="text-align: center;"><input type="button" value="<?php echo __('tinyissue.updating'); ?>" onclick="javascript: ChangeonsText(document.getElementById('select_ChxTxt').value, '<?php echo \Auth::user()->language; ?>', 'OUI');" class="button2"/></div>
		</details>
		<details id="details_emailserver_head">
			<summary><?php echo $LngSRV['UpdateConfigFile']; ?></summary>
			<br />
			<div class="pad2">
				<?php echo $LngSRV["Email_server"]; ?> : <input name="email_server" id="input_email_server" value="<?php echo $Conf["smtp"]["server"]; ?>" onkeyup="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["Email_port"]; ?> : <input name="email_port" type="number" size="6" id="input_email_port" value="<?php echo $Conf["smtp"]["port"]; ?>" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["Email_encryption"]; ?> : <input name="email_encryption" id="input_email_encryption" value="<?php echo $Conf["smtp"]["encryption"]; ?>" onkeyup="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["Email_username"]; ?> : <input name="email_username" id="input_email_username" value="<?php echo $Conf["smtp"]["username"]; ?>" onkeyup="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["Email_password"]; ?> : <input name="email_password" id="input_email_password" value="<?php echo $Conf["smtp"]["password"]; ?>" onkeyup="this.style.backgroundColor = 'yellow';" /><br />
			</div>
			<div class="pad2">
				<?php echo $LngSRV["Email_transport"]; ?> : 
					<select name="Email_transport" id="select_Email_transport" onchange="this.style.backgroundColor = 'yellow';">
						<?php
							$transp = array("gmail","mail","PHP","POP3","sendmail","smtp");
							asort($transp);
							foreach ($transp as $ind => $val) {
								echo '<option value="'.$val.'" '.(($Conf['transport'] == $val) ? 'selected="selected"' : '').'>'.$val.'</option>';
							} 
						?>
					</select> <br />
				<?php echo $LngSRV["Email_plainHTML"]; ?> : 
					<select name="Email_plainHTML" id="select_Email_plainHTML" onchange="this.style.backgroundColor = 'yellow';">
						<option value="text/plain" <?php echo ($Conf['plainHTML'] == 'text/plain') ? 'selected="selected"' : ''; ?>>text/plain</option>
						<option value="html" <?php echo ($Conf['plainHTML'] == 'html') ? 'selected="selected"' : ''; ?>>html</option>
						<option value="multipart/mixed" <?php echo ($Conf['plainHTML'] == 'multipart/mixed') ? 'selected="selected"' : ''; ?>>multipart/mixed</option>
					</select> <br />
				<?php 
					echo $LngSRV["Email_encoding"].' : ';		 
					echo '<select name="email_encoding" id="input_email_encoding" onchange="this.style.backgroundColor = \'yellow\';">';
					$Options = array("Big5", "EUC-KR", "GB18030", "ISO-8859-2", "ISO-8859-7", "ISO-8859-8", "ISO-2022-JP", "Shift JIS", "UTF-8", "UTF-16BE", "Windows-874", "Windows-1250", "Windows-1251", "Windows-1252", "Windows-1254", "Windows-1255", "Windows-1256", "Windows-1257", "Windows-1258" );
					foreach ($Options as $ind => $val) {
						echo '<option value="'.$val.'" 			'.(($Conf["encoding"] == $val) 			? 'selected="selected"' : '').' >'.$val.'</option>';
					}
					echo '</select> <br />';
				?> 
				<?php	echo $LngSRV["Email_mailerrormsg"]; ?> :
					<select name="email_mailerrormsg" id="input_email_mailerrormsg" onchange="this.style.backgroundColor = 'yellow';">
						<option value="0"	<?php echo ($Conf["mailerrormsg"] == '0') ? 'selected="selected"' : ''; ?> ><?php echo $LngSRV["Email_mailerrormsg_0"]; ?></option>
						<option value="1"	<?php echo ($Conf["mailerrormsg"] == '1') ? 'selected="selected"' : ''; ?> ><?php echo $LngSRV["Email_mailerrormsg_1"]; ?></option>
						<option value="2"	<?php echo ($Conf["mailerrormsg"] == '2') ? 'selected="selected"' : ''; ?> ><?php echo $LngSRV["Email_mailerrormsg_2"]; ?></option>
					</select>
					<br /><br />
				<?php echo $LngSRV["Email_linelenght"]; ?> : <input name="email_linelenght" id="input_email_linelenght" type="number" max="1000" min="25" size="6" value="<?php echo $Conf["linelenght"]; ?>" onchange="this.style.backgroundColor = 'yellow';" /><br /><br />
				<br />
				<input type="button" value="<?php echo __('tinyissue.updating'); ?>" onclick="javascript: AppliquerServeur();" class="button2"/>
			</div>
		</details>
		<details id="details_preferences_head">
			<summary><?php echo $LngSRV['preferences_gen']; ?></summary>
				<br />
				<?php 
					$config_app = require path('public') . 'config.app.php';
					$Conf = $config_app['PriorityColors'];
				 ?>
				<?php echo $LngSRV["preferences_coula"]; ?> : <input name="coula" id="input_coula" value="<?php echo ($Conf[1] == 'PaleGray') ? '#ACACAC' : $Conf[1]; ?>" type="color" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["preferences_coulb"]; ?> : <input name="coulb" id="input_coulb" value="<?php echo ($Conf[2] == 'DarkCyan') ? '#008B8B' : $Conf[2]; ?>" type="color" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["preferences_coulc"]; ?> : <input name="coulc" id="input_coulc" value="<?php echo ($Conf[3] == 'LimeGreen')? '#32CD32' : $Conf[3]; ?>" type="color" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["preferences_could"]; ?> : <input name="could" id="input_could" value="<?php echo ($Conf[4]=='Darkorange') ? '#FF8C00' : $Conf[4]; ?>" type="color" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["preferences_coule"]; ?> : <input name="coule" id="input_coule" value="<?php echo ($Conf[5] == 'Crimson')	? '#DC143C' : $Conf[5]; ?>" type="color" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["preferences_coulo"]; ?> : <input name="coulo" id="input_coulo" value="<?php echo ($Conf[0] == 'black') 	? '#000000' : $Conf[0]; ?>" type="color" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<br />
				<?php echo $LngSRV["preferences_duree"]; ?> (30) : <input name="duree" id="input_duree" value="<?php echo $config_app['duration']; ?>" size="4" type="number" max="365" min="2" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<br />
				<?php $Conf = $config_app['Percent']; ?>
				<span style="float: right; vertical-align: middle;">				<input type="button" value="<?php echo __('tinyissue.updating'); ?>" onclick="javascript: AppliquerPrefGen();" class="button2"/></span>
				<?php echo $LngSRV["preferences_pct_prog"]; ?> (10) : <input name="prog" id="input_prog" value="<?php echo $Conf[2]; ?>" type="number" size="3" min="2" max="85" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["preferences_pct_test"]; ?> (80) : <input name="test" id="input_test" value="<?php echo $Conf[3]; ?>" type="number" size="3" min="55" max="99" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["preferences_todonbitems"]; ?> (25) : <input name="TodoNbItems" id="input_TodoNbItems" value="<?php echo $config_app['TodoNbItems'] ?? 25; ?>" type="number" size="5" min="5" max="999" onchange="this.style.backgroundColor = 'yellow';" /><br />
				<?php echo $LngSRV["preferences_tempsfait"]; ?> (1) : <input name="TempsFait" id="input_TempsFait" value="<?php echo $config_app['TempsFait'] ?? 1; ?>" type="number" size="5" min="0" max="100" onchange="this.style.backgroundColor = 'yellow';" /><br />
		</details>

		<details id="details_sauvegardes">
			<summary><?php echo __('tinyissue.admin_backup'); ?></summary>
			<br /><br />
				<h4><strong><?php echo $LngSRV["Backup_BDD"]; ?></strong> : </h4>
				<span id="span_BackupBDD">
				<?php echo $LngSRV["Backup_BDDemail"]; ?> : 	 <input name="Courriel" id="input_databaseCourriel" value="" type="email" />
				&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo $LngSRV["Backup_BDDpassword"]; ?> : <input name="MotPasse" id="input_databaseMotPasse" value="" type="password" />
				<br />
				<?php echo $LngSRV["Backup_BDDosOS"]; ?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="OS" id="input_databaseOSl" value="Linux" type="radio" <?php if (strtolower(substr(php_uname('s'), 0, 3)) != 'win') { echo ' checked="checked"'; } ?> />
				<?php echo $LngSRV["Backup_BDDosLIN"]; ?> 
				&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="OS" id="input_databaseOSw" value="Windows" type="radio" <?php if (strtolower(substr(php_uname('s'), 0, 3)) == 'win') { echo ' checked="checked"'; } ?>/>
				<?php echo $LngSRV["Backup_BDDosWIN"]; ?> 
				<br />
				<span style="float: right; vertical-align: middle;">
				<input name="Lancer" type="button" class="button2" value="<?php echo $LngSRV["SQL_DatabaseGo"]; ?>" id="input_databaseLancer" onclick="javascript: BackupBDD();" />
				</span>
				</span>
				<br /><br />
			<br /><br />
			<h4><strong><?php echo $LngSRV["TXT_Database"]; ?></strong> : </h4>
			<div id="div_divBackupTXT" style="margin-left: 20%; margin-right: 20%;">
			<?php
				foreach ($LesOptions as $ind => $val) {
					echo '<input name="ChxTxt_'.$ind.'" id="input_ChxTxt_'.$ind.'" type="checkbox" checked="checked" value="'.$ind.'" />'.$val.'<br />';
				}
				echo '<input name="ChxTxt_config" id="input_ChxTxt_config" type="checkbox" checked="checked" value="config" />BUGS config file<br />';
			?>
			<span style="float: right; vertical-align: middle; margin-top: -150px;">
			<input name="Lancer" type="button" class="button2" value="<?php echo $LngSRV["TXT_DatabaseGo"]; ?>" id="input_databaseLancer" onclick="javascript: BackupTXT();" />
			</span>
			</div>
			<br /><br />
			<br /><br />
		</details>

		<details id="details_erreurs">
			<summary><?php echo $LngSRV["err_tit"]; ?></summary>
			<br /><br />
				<h4><strong><?php echo $LngSRV["err_tit"]; ?></strong> : </h4>
				<span id="span_errors">
				<?php echo $LngSRV["err_detail"]; ?>
				<?php echo $LngSRV["UserPref_projet_2a"]; ?> : 	 <input name="ErrDet" id="input_err_detail" value="true" type="radio" <?php echo (Config::get('error.detail') ? ' checked="checked"' : ''); ?> />
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo $LngSRV["UserPref_projet_2b"]; ?> : 	 <input name="ErrDet" id="input_err_detail" value="false" type="radio" <?php echo (Config::get('error.detail') ? '' : ' checked="checked"'); ?> />
				<br />
				<?php echo $LngSRV["err_log"]; ?>
				<?php echo $LngSRV["UserPref_projet_2a"]; ?> : 	 <input name="ErrLog" id="input_err_log" value="true" type="radio" <?php echo (Config::get('error.log') ? ' checked="checked"' : ''); ?> />
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo $LngSRV["UserPref_projet_2b"]; ?> : 	 <input name="ErrLog" id="input_err_log" value="false" type="radio" <?php echo (Config::get('error.log') ? '' : ' checked="checked"'); ?> />
				<br />
				<?php echo $LngSRV["err_exit"]; ?>
				<?php echo $LngSRV["UserPref_projet_2a"]; ?> : 	 <input name="ErrExt" id="input_err_exit" value="true" type="radio" <?php echo (Config::get('error.exit') ? ' checked="checked"' : ''); ?> />
				&nbsp;&nbsp;&nbsp;&nbsp;
				<?php echo $LngSRV["UserPref_projet_2b"]; ?> : 	 <input name="ErrExt" id="input_err_exit" value="false" type="radio" <?php echo (Config::get('error.exit') ? '' : ' checked="checked"'); ?> />
				<br /><br />
				<?php echo $LngSRV["err_exittxt"]; ?> :  	 <input name="ErrExittxt" id="input_err_exittxt" value="<?php echo substr(Config::get('error.exit'), 0, strpos(Config::get('error.exit'), "<")-1); ?>" type="input" size="60" maxlength="100"  onkeyup="document.getElementById('span_exemple').innerHTML = this.value + ' <a href=\'todo\'>BUGS</a>';" />
				</span>
				<br /><br />
			<span style="float: right; vertical-align: middle; margin-top: -42px;">
			<input name="ErrLancer" type="button" class="button2" value="<?php echo __('tinyissue.updating'); ?>" id="input_errLancer" onclick="javascript: AppliquerErr();" />
			</span>
			<?php echo $LngSRV['err_result']; ?> : <span id="span_exemple"><?php echo substr(Config::get('error.exit'), 0, strpos(Config::get('error.exit'), "<")-1); ?>&nbsp;<a href="todo" >BUGS</a></span>
			<br /><br />
			</div>
			<br /><br />
			<br /><br />
		</details>
	</div>

<script type="text/javascript" src="app/assets/js/admin.js" async ></script>
<script type="text/javascript" >
<?php
	$wysiwyg = Config::get('application.editor');
	if (trim(@$wysiwyg['directory']) != '') {
		if (file_exists($wysiwyg['directory']."/Bugs_code/showeditor.js")) {
			include_once $wysiwyg['directory']."/Bugs_code/showeditor.js";
			if ($wysiwyg['name'] == 'ckeditor') {
				echo "
				setTimeout(function() {
					showckeditor ('email_intro', 7);
					showckeditor ('email_bye', 8);
					showckeditor ('contenu', 9);
				} , 567);
				";
			}
		}
	}
?>
</script>