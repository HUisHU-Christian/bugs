<?php
session_start();
foreach (@$_GET as $ind => $val) {
	if (strstr($val, "<") != '' || strstr(htmlspecialchars_decode($val), "<") != '' || strpos($val, "script")  !== false) {
		unset($_GET[$ind]); 
	}
}
include_once "../app/application/language/all.php";
$EnLng = require_once("../app/application/language/en/install.php");
if (!isset($_GET["Lng"]) || !file_exists("../app/application/language/".@$_GET["Lng"]."/install.php")) { $_GET["Lng"] = 'en'; }
if (@$_GET["Lng"] != 'en' ) { $MyLng = require_once("../app/application/language/".$_GET["Lng"]."/install.php"); $MyLng = array_merge($EnLng, $MyLng); } else {$MyLng = $EnLng; }
if (!file_exists('./config-setup.php')) {
	echo '<script>';
	echo 'alert("'.$MyLng["Already_installed"].'");';
	echo 'document.location.href="../index.php";';
	echo '</script>';
	die();
} else {
	require './config-setup.php';
}
require '../app/laravel/hash.php';
require '../app/laravel/str.php';

$first_name_error = '';
$last_name_error = '';
$email_error = '';
$pass_error = '';

require './install.php';

$install = new install();
$database_check = $install->check_connect();
$requirement_check = $install->check_requirements();

?>
<!DOCTYPE html>
<html>
<title>Installation BUGS</title>
<head>

	<link href="../app/assets/css/install.css" media="all" type="text/css" rel="stylesheet">

</head>
<body>
<div class="InstallLogo"></div>

<?php
if($database_check) {
	//La base de données est peut-être déjà là
	$prefixe = "";
	while (!file_exists($prefixe."config.app.php")) {
		$prefixe .= "../";
	}
	$config = require $prefixe."config.app.php";
	$dataSrc = mysqli_connect($config['database']['host'], $config['database']['username'], $config['database']['password'], $config['database']['database']);
	$resuUSER = mysqli_query($dataSrc, "SELECT * FROM users");
	//Puisque la base de données est déjà installée, procédons à l'ouverture d'une session d'usager.
	if (mysqli_num_rows($resuUSER) > 0) {
		echo '<script>document.location.href = "../";</script>';
		die();
	}

	//Comme la base de données n'est pas encore installée, procédons à l'inscription de l'administrateur du système et l'installation de la BDD
	if(isset($_POST['email'])) {
		if($_POST['email'] != ''&& $_POST['first_name'] != '' && $_POST['last_name'] != '' && $_POST['password'] != '') {
			$finish = $install->create_tables($_POST);
			if($finish) {
				$_SESSION["Msg"]  = '<h2 style="color: #060;">'.$MyLng['Complete_awesome'].'</h2>';
				$_SESSION["Msg"] .= '<p style="color: #060;">'.$MyLng['Complete_presentation'].'</p>';
				$_SESSION["usr"] = $_POST['email'];  
				$_SESSION["psw"] = $_POST['password'];  
				echo '<script>document.location.href = "../";</script>';
				die();
			}
		} else {
			if(trim($_POST['email']) == '' || $_POST['email'] == "you@domain.com") 		{ $email_error = $MyLng['InitError_email']; }
			if(trim($_POST['first_name']) == '') 	{ $first_name_error = $MyLng['InitError_fname']; }
			if(trim($_POST['last_name']) == '') 	{ $last_name_error = $MyLng['InitError_lname']; }
			if(trim($_POST['password']) == '')		{ $pass_error = $MyLng['InitError_pswrd']; }
		}
	} else {
		$_POST['email'] = '';
		$_POST['first_name'] = '';
		$_POST['last_name'] = '';
	}
}
?>


<div id="container">
	<form method="post" action="index.php?Lng=<?php echo $_GET["Lng"]; ?>" autocomplete="off">
		<table class="form">
			<tr>
				<td colspan="2">
				<?php
					echo '<h2>'.$MyLng['Installation'].'</h2>';
					if(count($requirement_check) > 0) {
						echo '<strong>'.$MyLng['Requirement_Check'].'</strong><br />';
						foreach ($requirement_check as $key => $value) { echo ' - '.$value.'<br />'; }
						die();
					}
					if(@$database_check['error']) {
						echo $MyLng['Database_check'].$database_check['error'];
						die();
					}
					echo $MyLng['Installation_Thanks'];
				?>

				<br /><br />
				</td>
			</tr>

			<tr>
				<th><label for="first_name"><?php echo $MyLng['Name_first']; ?></label>
					<input autocomplete="off" type="text" name="first_name" id="first_name" value="<?php echo $_POST['first_name']; ?>"/>
					<span class="error"><?php echo $first_name_error ?></span>
					<br />
				</th>
			</tr>
			<tr>
				<th><label for="last_name"><?php echo $MyLng['Name_last']; ?></label>
					<input autocomplete="off" type="text" name="last_name" id="last_name" value="<?php echo $_POST['last_name']; ?>"/>
					<span class="error"><?php echo $last_name_error ?></span>
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
					<input autocomplete="off" type="text" name="email" id="email" value="<?php echo $_POST['email']; ?>"/>
					<span class="error"><?php echo $email_error ?></span>
					<br />
				</th>
			</tr>
			<tr>
				<th><label for="password"><?php echo $MyLng['Name_pswd']; ?></label>
					<input type="password" name="autocompletion_off" value="" style="display:none;">
					<input autocomplete="off" type="password" name="password" id="password" />
					<span class="error"><?php echo $pass_error ?></span>
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

</body>
</html>
