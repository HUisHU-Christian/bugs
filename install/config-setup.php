<script type="text/javascript">
function ChgLng(Lng = 'en') { document.location.href = 'index.php?Lng=' + Lng; }

</script>

<?php
foreach (@$_GET as $ind => $val) {
	if (strstr($val, "<") != '' || strstr(htmlspecialchars_decode($val), "<") != '' || strstr($val, "script") != '' || trim($val) == '') {
		unset($_GET[$ind]); 
	}
}
foreach (@$_POST as $ind => $val) {
	if (strstr($ind, "password") == '' && (strstr($val, "<") != '' || strstr(htmlspecialchars_decode($val), "<") != '' || strstr($val, "script") != '')  || trim($val) == '') {
		unset($_POST[$ind]); 
	}
}
if(isset($_POST['create_config']) && isset($_POST['database_host'])) {
	if(!file_exists('../config.app.example.php')) { die($NoConfigApp); }
	$config_file = file_get_contents('../config.app.example.php');

	/* Edit URL Information */
	$_POST['URL'] = (substr($_POST['URL'], -1) == '/') ? $_POST['URL'] : $_POST['URL'].'/';
	$config_file = str_replace("'url' => '',", "'url' => '".$_POST['URL']."',", $config_file);

	/* Edit Database Information */
	$config_file = str_replace('localhost', $_POST['database_host'], $config_file);
	$config_file = str_replace('database_user', $_POST['database_username'], $config_file);
	$config_file = str_replace('database_password', $_POST['database_password'], $config_file);
	$config_file = str_replace('database_name', $_POST['database_name'], $config_file);

	/* Edit E-mail Information */
	$config_file = str_replace('Your E-Mail Name', $_POST['email_name'], $config_file);
	$config_file = str_replace('name@domain.com', $_POST['email_address'], $config_file);
	$config_file = str_replace("'transport' => 'smtp'", "'transport' => '".$_POST['email_transport']."'", $config_file);
	$config_file = str_replace("'username' => 'xyzxyz'", "'username' =>  '".($_POST['email_username'] ?? 'BUGS')."'", $config_file);
	$config_file = str_replace("'server' => 'smtp.gmail.com'", "'server' => '".($_POST['email_server'] ?? 'smtp.gmail.com')."'", $config_file);
	$config_file = str_replace("'port' => 587", "'port' => ".($_POST['email_port'] ?? '665'), $config_file);
	$config_file = str_replace("'encryption' => 'tls'", "'encryption' =>  '".($_POST['email_encryption'] ?? 'SSL')."'", $config_file);
	$config_file = str_replace("'username' => 'xyzxyz'", "'username' =>  '".($_POST['email_username'] ?? $_POST['email_address'] ?? 'undefined')."'", $config_file);
	$config_file = str_replace("'password' => '******'", "'password' =>  '".($_POST['email_password'] ?? 'admin')."'", $config_file);

	/* Timezone */
	$config_file = str_replace('Europe/Brussels', $_POST['timezone'], $config_file);

	/* Key */
	$config_file = str_replace('yourrandomkey', md5(serialize($_POST) . time() . $_SERVER['HTTP_HOST']), $config_file);

	if(!is_writable(realpath('../'))){
		echo '
			<!DOCTYPE html>
			<html>
			<head>
				<link href="../app/assets/css/install.css" media="all" type="text/css" rel="stylesheet">
			</head>
			<body>
			<div class="InstallLogo"></div>

			<div id="container">
				<table class="form">
				<tr>
					<td colspan="2">
						<p>'.$MyLng['NoAPPfile_0'].'</p>
						<p>'.$MyLng['NoAPPfile_1'].'</p>

						<textarea cols="98" rows="15" class="code">'.htmlentities($config_file, ENT_COMPAT, 'UTF-8').'</textarea>

						<p>'.$MyLng['NoAPPfile_2'].'</p>
						<p><a href="index.php?Lng='.$_GET["Lng"].'" class="button primary">'.$MyLng['RunInstall'].'</a></p>
					</td>
				</tr>
			</div>
		';

	} else {

	file_put_contents('../config.app.php', $config_file);
	//Fill the database with basics
	require "./install.php";
	$install = new install();
	$install->create_database($_POST);
	$command='mysql --host='.$_POST["database_host"].' --user='.$_POST["database_username"] .' --password='.$_POST["database_password"] .' \''.$_POST["database_name"].'\' < MySQL_DB_Schema.sql';
	exec($command,$output,$worked);
	unset($output);
	switch($worked){
		case 0:
			//Success import of MySQL_DB_Schema.sql
			break;
	    case 1:
		//From the freshly made mysql-structure.php file, we'll create tables and default data along the install.php process
		$install->config = require '../config.app.php';
	    	//Import of MySQL_DB_Schema.sql didn't work, so we'll pass by long way
	    	if (file_exists('mysql-structure.php') && file_exists('MySQL_DB_Schema.sql')) { unlink('mysql-structure.php'); } 
			if (!file_exists('mysql-structure.php') && file_exists('MySQL_DB_Schema.sql') ) {
				$FILEsql = file('MySQL_DB_Schema.sql');
				$FILEphp = fopen('mysql-structure.php', 'w+');
				$linePHP  = '<?php ';
				$linePHP .= 'return array(';
				foreach ($FILEsql as $lgn => $cnt) {
					if (trim($cnt) == '#----- First line of this file .... please let it here, first with NO carriage return before nor after. -----') { $cnt = ''; continue;}
					if (trim($cnt) == '#----- Last line of this file .... Anything bellow this line will be lost. -----') { $cnt = ''; break;}
					if (substr($cnt, 0, 4) === '#--#') { $cnt = '"#'.substr($cnt, 4); }
					if (substr($cnt, 0, 3) === '#--' ) { $cnt = '",'.substr($cnt, 3); }
					$linePHP .= $cnt;
				}
				$linePHP .= ' " );';
				fwrite($FILEphp, $linePHP);
				fclose($FILEphp);
				//From the MySQL_DB_Schema.sql file, we create a usable php file for php install
				$database_check = $install->check_connect();
				$install->create_tables();
			}
			break;
	}
?>
<!DOCTYPE html>
<html>
<head>
<link href="../app/assets/css/install.css" media="all" type="text/css" rel="stylesheet">
</head>
<body>
<div class="InstallLogo"></div>
<div id="container">
	<form method="post" action="index.php?Lng=<?php echo $_GET["Lng"]; ?>" autocomplete="off">
		<table class="form">
			<tr>
				<td colspan="2">
				<?php
					echo '<h2>'.$MyLng['Installation'].'</h2>';
					echo $MyLng['Installation_Thanks'];
				?>

				<br /><br />
				</td>
			</tr>

			<tr>
				<th><label for="first_name"><?php echo $MyLng['Name_first']; ?></label>
					<input autocomplete="off" type="text" name="first_name" id="first_name" value="<?php echo $_POST['email_name']; ?>"/>
					<br />
				</th>
			</tr>
			<tr>
				<th><label for="last_name"><?php echo $MyLng['Name_last']; ?></label>
					<input autocomplete="off" type="text" name="last_name" id="last_name" value=""/>
					<br />
				</th>
			</tr>
			<tr>
				<th><label for="language"><?php echo $MyLng['Name_lang']; ?></label>
				<select name="language" id="language" style="background-color: #FFF;">
				<?php
					foreach ($Language as $ind => $lang) {
						echo '<option value="'.$ind.'" '.(($ind == $_GET["Lng"]) ? 'selected="selected"' : '').'>'.$lang.'</option>';
					}
				?>
				</select>
				<br /><br />
				</th>
			</tr>
			<tr>
				<th><label for="email"><?php echo $MyLng['Name_email']; ?></label>
					<input autocomplete="off" type="text" name="email" id="email" value="<?php echo $_POST['email_address']; ?>"/>
					<br />
				</th>
			</tr>
			<tr>
				<th><label for="password"><?php echo $MyLng['Name_pswd']; ?></label>
					<input type="password" name="autocompletion_off" value="" style="display:none;">
					<input autocomplete="off" type="password" name="password" id="password" />
					<br />
				</th>
			</tr>
			<tr>
				<td style="text-align: center;">
					<br />
					<input type="submit" value="<?php echo $MyLng['Name_finish']; ?>" class="button primary"/>
					<br /><br />
				</td>
			</tr>
		</table>
	</form>
</div>

<?php
	}
exit();
}
if(!file_exists('../config.app.php')){ ?>
<!DOCTYPE html>
<html>
<head>
	<link href="../app/assets/css/install.css" media="all" type="text/css" rel="stylesheet">

</head>
<body>
<div class="InstallLogo"></div>

<div id="container">
	<p style="text-align:center;">
	<select onchange="ChgLng(this.value);" style="background-color: #FFF;">
	<?php
		foreach ($Language as $ind => $lang) {
			echo '<option value="'.$ind.'" '.(($ind == $_GET["Lng"]) ? 'selected="selected"' : '').'>'.$lang.'</option>';
		}
	?>
	</select>
	</p>
	<form method="post" action="index.php?Lng=<?php echo $_GET["Lng"] ?? 'en'; ?>" autocomplete="off">
		<table class="form">
			<tr>
				<td colspan="2">
				<h2><?php echo $MyLng['SetupConfigFile']; ?></h2>

				<p>
					<?php echo $MyLng['OKconfAPPfile']; ?>
				</p>
				</td>
			</tr>
			<tr style="background-color: #DDD;">
				<td>
					<h3 style="font-weight: bold; font-size: 150%; ">URL</h3>
				</td>
				<td>
					<?php
						$url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
						$url = substr($url, 0, strpos($url, 'install'));
					?>
					<input type="text" name="URL" value="<?php echo $url; ?>" />
				</td>
			</tr>
			<tr style="background-color: #DDD;">
				<td colspan="2">
				<h3 style="font-weight: bold; font-size: 150%; ">SQL</h3>
				</td>
			</tr>
			<tr style="background-color: #DDD;">
				<th><?php echo $MyLng['SQL_Driver']; ?></th>
				<td>
					<select name="database_driver">
					<option value="mysql">MySQL</option>
					<option value="sqlsrv">MSSQL</option>
					<option value="pgsql">PostgreSQL</option>
					<option value="sqlite">SQLite</option>
					</select>
				</td>
			</tr>
			<tr style="background-color: #DDD;">
				<th><?php echo $MyLng['SQL_Host']; ?></th>
				<td><input type="text" name="database_host" value="localhost" /></td>
			</tr>
			<tr style="background-color: #DDD;">
				<th><?php echo $MyLng['SQL_Database']; ?></th>
				<td><input type="text" name="database_name" value="bugs" /></td>
			</tr>
			<tr style="background-color: #DDD;">
				<th><?php echo $MyLng['SQL_Username']; ?></th>
				<td><input type="text" name="database_username" value="" /></td>
			</tr>
			<tr style="background-color: #DDD;">
				<th><?php echo $MyLng['SQL_Password']; ?></th>
				<input type="password" name="autocompletion_off" value="" style="display:none;">
				<td><input type="password" name="database_password" value="" /></td>
			</tr>
			<tr>
				<td colspan="2">
				<h3 style="font-weight: bold; font-size: 150%; "><?php echo $MyLng['Email']; ?></h3>
				<p><?php echo $MyLng['Email_Desc']; ?></p>
				</td>
			</tr>
			<tr>
				<th><?php echo $MyLng['Email_Name']; ?></th>
				<td>
					<input type="text" name="email_name" value="" placeholder="My dear Bugs prog" />
				</td>
			</tr>
			<tr>
				<th><?php echo $MyLng['Email_Address']; ?></th>
				<td>
					<input type="text" name="email_address" value="" placeholder="you@domain.com" />
				</td>
			</tr>
			<tr>
				<th><?php echo $MyLng['Email_transport']; ?></th>
				<td>
					<select name="email_transport">
					<option value="smtp">smtp</option>
					<option value="mail" selected="selected">mail</option>
					<option value="sendmail">sendmail</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo $MyLng['Email_server']; ?></th>
				<td>
					<input type="text" name="email_server" value="" placeholder="smtp.gmail.com" />
				</td>
			</tr>
			<tr>
				<th><?php echo $MyLng['Email_port']; ?></th>
				<td>
					<select name="email_port">
					<option value="25"> 25 (default)</option>
					<option value="587">587 (gmail)</option>
					<option value="465">465 (SSL / TLS)</option>
					<?php
						for ($x=1; $x<999; $x++) {
							echo '<option value="'.$x.'">'.$x.'</option>';
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo $MyLng['Email_encryption']; ?></th>
				<td>
					<select name="email_encryption">
					<option value="">(none)</option>
					<option value="tsl">TSL</option>
					<option value="ssl">SSL</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php echo $MyLng['Email_username']; ?></th>
				<td>
					<input type="text" name="email_username" value="" placeholder="username@gmail.com" />
				</td>
			</tr>
			<tr>
				<th><?php echo $MyLng['Email_password']; ?></th>
				<td>
					<input type="text" name="email_password" value="" placeholder="email password" />
				</td>
			</tr>
			<tr>
				<td colspan="2" style="background-color: #DDD;">
				<h3 style="font-weight: bold; font-size: 150%; "><?php echo $MyLng['Time_Local']; ?></h3>
				</td>
			</tr>
			<tr style="background-color: #DDD;">
				<th><?php echo $MyLng['Time_Timezone']; ?></th>
				<td>
					<select name="timezone">
						<?php
						$timezones = timezone_identifiers_list();
						echo 'select name="timezone" size="10">' . "\n";
						foreach($timezones as $timezone) {
						  echo '<option';
						  echo $timezone == date("e") ? ' selected' : '';
						  echo '>' . $timezone . '</option>' . "\n";
						}
						echo '</select>' . "\n";
						?>
					</select>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="submit" class="button primary" name="create_config" value="<?php echo $MyLng['Button_CreateConfig']; ?>" />
					</td>
				</tr>
			</table>
		</form>
		<br /><br />
		<form method="post" action="restore.php?Lng=<?php echo $_GET["Lng"] ?? 'en'; ?>" enctype="multipart/form-data">
			<table class="form">
				<tr>
					<td colspan="2"><h2><?php echo $MyLng['restore']; ?></h2></td>
				</tr>
				<tr>
					<td colspan="2"><label><?php echo $MyLng['restore_srvr']; ?>        </label><input name="srvr" id="inputRestore_srvr" type="text" value="localhost" /></td>
				</tr>
				<tr>
					<td colspan="2"><label><?php echo $MyLng['restore_user']; ?>        </label><input name="user" id="inputRestore_user" type="text" value="" /></td>
				</tr>
				<tr>
					<td colspan="2"><label><?php echo $MyLng['restore_pswd']; ?>        </label><input name="pswd" id="inputRestore_pswd" type="text" value="" /></td>
				</tr>
				<tr>
					<td colspan="2"><label><?php echo $MyLng['restore_bdds']; ?> ( SQL )</label><input name="bdds" id="inputRestore_bdds"  type="file" value="" /></td>
				</tr>
				<tr>
					<td colspan="2"><label><?php echo $MyLng['restore_txte']; ?> ( zip )</label><input name="txte" id="inputRestore_txte"  type="file" value="" /></td>
				</tr>
				<tr>
					<td style="text-align: center;"><input name="butRestore" type="submit" value="<?php echo $MyLng['restore_butt']; ?>" class="button primary"/></td>
				</tr>
			</table>
		</form>
	</div>
<?php exit(); } ?>
