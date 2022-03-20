<?php
	$prefixe = "";
	if (!isset($_SESSION)) { session_start(); }
	while (!file_exists($prefixe."config.app.php")) {
		$prefixe .= "../";
	}
	include $prefixe."app/application/language/all.php";
	$lng = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
	if (file_exists($prefixe."install/config-setup.php")) { unlink ($prefixe."install/config-setup.php"); }

	//Auto-update the database if conditions are fullfilled
	if (isset($_SERVER ["REDIRECT_SCRIPT_URL"]) && isset($_GET["MAJsql"])) {
		if (substr($_SERVER ["REDIRECT_SCRIPT_URL"], 0, -5) == substr($_SERVER["PHP_SELF"], 0, -9)  && substr($_SERVER ["REDIRECT_SCRIPT_URL"], -5) == 'login' && trim($_GET["MAJsql"]) != '' && substr($_GET["MAJsql"], 0, 7) == 'update_' && substr($_GET["MAJsql"], -4) == '.sql') {
			\Administration::AjourStructureBase("login");
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php if (!isset($sautons) || @$sautons == false) { ?>
		<link rel="apple-touch-icon" sizes="57x57" href="<?php echo URL::to_asset('/apple-touch-icon-57x57.png'); ?>">
		<link rel="apple-touch-icon" sizes="114x114" href="<?php echo URL::to_asset('/apple-touch-icon-114x114.png');?>">
		<link rel="apple-touch-icon" sizes="72x72" href="<?php echo URL::to_asset('/apple-touch-icon-72x72.png');?>">
		<link rel="apple-touch-icon" sizes="144x144" href="<?php echo URL::to_asset('/apple-touch-icon-144x144.png');?>">
		<link rel="apple-touch-icon" sizes="60x60" href="<?php echo URL::to_asset('/apple-touch-icon-60x60.png');?>">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo URL::to_asset('/apple-touch-icon-120x120.png');?>">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo URL::to_asset('/apple-touch-icon-76x76.png');?>">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo URL::to_asset('/apple-touch-icon-152x152.png');?>">
		<meta name="apple-mobile-web-app-title" content="Bugs">
		<link rel="icon" type="image/png" href="<?php echo URL::to_asset('/favicon-196x196.png');?>" sizes="196x196">
		<link rel="icon" type="image/png" href="<?php echo URL::to_asset('/favicon-160x160.png');?>" sizes="160x160">
		<link rel="icon" type="image/png" href="<?php echo URL::to_asset('/favicon-96x96.png');?>" sizes="96x96">
		<link rel="icon" type="image/png" href="<?php echo URL::to_asset('/favicon-16x16.png');?>" sizes="16x16">
		<link rel="icon" type="image/png" href="<?php echo URL::to_asset('/favicon-32x32.png');?>" sizes="32x32">
		<meta name="msapplication-TileColor" content="#39404f">
		<meta name="msapplication-TileImage" content="<?php echo URL::to_asset('/mstile-144x144.png');?>">
		<meta name="application-name" content="<?php Config::get('my_bugs_app.name'); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1">

		<title><?php echo Config::get('application.my_bugs_app.name'); ?></title>
		<?php echo Asset::styles(); 
		}
		?>
	</head>
<body>
	<div id="container">
		<div id="login">

			<h1><span id="span_Welcome"><?php echo (isset($Welcome[$lng])) ? $Welcome[$lng] : $Welcome["en"]; ?></span><br><img src="<?php echo URL::to_asset('app/assets/images/layout/tinyissue.svg');?>" alt="<?php echo Config::get('application.my_bugs_app.name'); ?>" style="width:350px;"></h1>
<?php
	$LngSRV = array("Database_Update_ok" => "Base de données vérifiée.", "Database_Update_need"=> "Besoin de mise à jour");
	$diff = \Administration::VerifDataBase();
//	if (count($diff) == 0) { 
?>
			<form method="post" id="form_Login">
				<table class="form" >
					<tr>
						<td colspan="2" style="color: #a31500;">
							<?php 
								if (Session::get('error') !== NULL) { echo (isset($WrongPwd[$lng])) ? $WrongPwd[$lng] : $WrongPwd["en"]; }
							?>
						</td>
					</tr>
					<tr><th colspan="2" id="th_Title"><?php echo (isset($Title[$lng])) ? $Title[$lng] : $Title["en"]; ?></th></tr>
					<tr>
						<th><label for="email" id="label_Email"><?php echo (isset($Email[$lng])) ? $Email[$lng] : $Email["en"]; ?></label></th>
						<td><input type="text" id="input_Email" name="email" id="email" autofocus value="<?php echo $_SESSION["usr"] ?? ''; ?>" /></td>
					</tr>
					<tr id="tr_form_password">
						<th><label for="password" id="label_Password"><?php echo (isset($Password[$lng])) ? $Password[$lng] : $Password["en"]; ?></label></th>
						<td><input type="password" id="password" name="password" value="<?php echo $_SESSION["psw"] ?? ''; ?>" ondragstart="dragCommence('password');" /></td>
					</tr>
					<tr id="tr_form_rappeler">
						<th></th>
						<td>
							<label><input type="checkbox" value="1" name="remember" /><span id="span_Remember"><?php echo (isset($Remember[$lng])) ? $Remember[$lng] : $Remember["en"]; ?>&nbsp;? &nbsp;&nbsp;</span></label>
							<input type="submit" id="input_submit" value="<?php echo (isset($Login[$lng])) ? $Login[$lng] : $Login["en"]; ?>" class="button primary"/>
						</td>
					</tr>
				</table>

				<?php echo Form::hidden('return', Session::get('return', '')); ?>
				<?php echo Form::token();
					if (isset($_SESSION["automatiquement"])) {
						if ($_SESSION["automatiquement"] == 'oui') {
							unset($_SESSION["automatiquement"]);
							echo '<script>document.getElementById("input_submit").click();</script>';
						}
					} 
				?>
		</div>
		<div id="div_ChxLng" style="text-align:center; padding-top: 50px;">
		<select name="ChxLng" id="select_ChxLng" onchange="ChgLng(this.value);">
			<?php
				foreach ($Language as $ind => $val) {
					echo '<option value="'.$ind.'" '.(($ind == $lng) ? 'selected="selected"' : '').'>'.$val.'</option>';
				}
			?>
		</select>
		</div>
		<div style="text-align:center; padding-top: 50px;">
			<input name="MonAdrs" type="hidden" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">
			<input name="CetAdrs" type="hidden" value="<?php echo $_SERVER['SERVER_ADDR']; ?>">
			<input name="retrouver" type="hidden" value="<?php echo time(); ?>">
			<input name="MotPasseOublie" id="input_MotPasseOublie" value="1" type="checkbox" onclick="fctMotPasseOublie(this.checked);"><span id="span_MotPasseOublie"><?php echo $Oublie[$lng]; ?></span>
			<div style="text-align:center; padding-top: 50px; display: none;" id="div_MotPasseOublie">
				<span id="span_MotPasseOublieQuoi" style="font-size: 150%;"><?php echo $OublieQuoi[$lng]; ?><br /></span>
				<img id="img_MotPasseOubliePoubelle" src="app/assets/css/images/poubelle_vide.png" alt="" border="2" ondragover="allowDrop(event);" ondrag="alert('Sommes en drag');" ondrop="dragFinisssons('poubelle');" />
			</div>
		</div>
		</form>
<?php
//		} else { 
//			echo '<h4 style="color: yellow; font-weight: bold; font-size: 110%;">'.$LngSRV["Database_Update_need"].'</h4>
//			';
//			$compte = 0;
//			$prem = "";
//			foreach ($diff as $nom) {
//				if (trim($nom) == '') { continue; }
//				if (in_array(substr(trim($nom), 0, 13), array("update_v1-1_1","update_v1-2_9","update_v1-3_1","update_v1-3_1","update_v1-3_2","update_v1-3_3","update_v1-3_4","update_v1-8_3","update_v1-8_4")) ) {
//					rename("../install/".$nom, "../install/"."OLD_".$nom);
//				} else { 
//					$prem = ($prem == '' && trim($nom) != '') ? $nom : $prem; 
//					echo '<form method="GET" id="form_MAJsql_'.$compte++.'"><input type="submit" name="MAJsql" value="'.$nom.'" class="update" /></form><br />
//					'; 
//				}
//			}
//			echo '<form method="GET" id="form_MAJsql_'.$compte.'"><input type="submit" name="MAJsql" id="input_MAJsql_'.$compte.'" value="'.$prem.'" class="update" /></form><br />';
//			echo '<script>document.getElementById(\'input_MAJsql_'.$compte.'\').click();</script>';
//		}  
?>
	</div>
</body>

<?php
	if (isset($_SESSION)) { 
		unset ($_SESSION["Msg"],$_SESSION["psw"],$_SESSION["usr"]);
	} 
?>
<?php echo Asset::scripts(); ?>
<script type="text/javascript">
var Langue = "<?php echo $lng; ?>";
var dragon = "";
var values = new Array();
var resu = "<?php echo $OublieResu[$lng]; ?> ";
var cour = "<?php echo $OublieCour[$lng]; ?> ";
var rendu = 10;
<?php
	foreach ($Language as $ind => $val) {
		echo 'values["'.$ind.'"] = new Array(); ';
		echo 'values["'.$ind.'"]["Email"] = "'.$Email[$ind].'";
		';
		echo 'values["'.$ind.'"]["Login"] = "'.$Login[$ind].'";
		';
		echo 'values["'.$ind.'"]["Password"] = "'.$Password[$ind].'";
		';
		echo 'values["'.$ind.'"]["Remember"] = "'.$Remember[$ind].'";
		';
		echo 'values["'.$ind.'"]["Title"] = "'.$Title[$ind].'";
		';
		echo 'values["'.$ind.'"]["Welcome"] = "'.$Welcome[$ind].'";
		';
		echo 'values["'.$ind.'"]["Oublie"] = "'.$Oublie[$ind].'";
		';
		echo 'values["'.$ind.'"]["OublieQuoi"] = "'.$OublieQuoi[$ind].'";
		';
		echo 'values["'.$ind.'"]["OublieResu"] = "'.$OublieResu[$ind].'";
		';
		echo 'values["'.$ind.'"]["OublieCour"] = "'.$OublieCour[$ind].'";
		';
	}
?>

function fctMotPasseOublie(etat) {
	if (etat == false) { document.location.href="index.php"; }
	document.getElementById('password').setAttribute('draggable', true);
	document.body.style.backgroundImage = "none";
	document.body.style.backgroundColor = "black";
	document.getElementById('div_MotPasseOublie').style.display = "block";
}
function allowDrop(ev) {
  ev.preventDefault();
}
function ChgLng(Lng) {
		document.getElementById('label_Email').innerHTML =  values[Lng]["Email"];
		document.getElementById('input_submit').value =  values[Lng]["Login"];
		document.getElementById('label_Password').innerHTML =  values[Lng]["Password"];
		document.getElementById('span_Remember').innerHTML =  values[Lng]["Remember"];
		document.getElementById('th_Title').innerHTML =  values[Lng]["Title"];
		document.getElementById('span_Welcome').innerHTML =  values[Lng]["Welcome"];
		document.getElementById('span_Welcome').innerHTML =  values[Lng]["Oublie"];
		document.getElementById('span_MotPasseOublie').innerHTML =  values[Lng]["Oublie"];
		document.getElementById('span_MotPasseOublieQuoi').innerHTML =  values[Lng]["OublieQuoi"];
		resu =  values[Lng]["OublieResu"];
		cour =  values[Lng]["OublieCour"];
		Langue = Lng;
}
function dragCommence(Quel) {
	dragon = Quel;
}
function dragFinisssons(Quel) {
  event.preventDefault();
	if (Quel == 'poubelle' && dragon == 'password') {
		document.getElementById('img_MotPasseOubliePoubelle').src = "app/assets/css/images/poubelle_pleine.png";
		document.getElementById('tr_form_password').style.display = "none"; 
		document.getElementById('tr_form_rappeler').style.display = "none";
		document.getElementById('div_ChxLng').style.display = "none";
		document.getElementById('input_MotPasseOublie').style.display = "none";
		document.getElementById('span_MotPasseOublie').style.fontSize = "150%";
		document.getElementById('span_MotPasseOublie').innerHTML = values[Langue]["OublieResu"] + " " + document.getElementById('input_Email').value;
		document.getElementById('span_MotPasseOublieQuoi').innerHTML =  "10";
		document.getElementById('password').value = "RechMotPasse";
		if (document.getElementById('input_Email').value == "") { 
			var person = prompt(cour, "");
			document.getElementById('input_Email').value = person;
			if (person == '') { resu = "<?php echo $OublieQuoi[$lng]; ?> "; }
		}
		setInterval(function(){ document.getElementById('span_MotPasseOublieQuoi').innerHTML = --rendu; if (rendu <= 0) {document.getElementById('form_Login').submit();} }, 1000);
	}
	dragon = "";
}

function rebours() {
	document.getElementById('span_MotPasseOublie').innerHTML = --rendu;
}


</script>
</html>