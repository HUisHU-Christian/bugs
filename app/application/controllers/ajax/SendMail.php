<?php
	include_once "db.php";

	//Préférences de l'usager
	if (isset($_GET["contenu"])) {
		if ($_GET["contenu"] == 'tagsADD' || $_GET["contenu"] == 'tagsOTE' || $_GET["contenu"] == 'assigned') { $contenu[] = $_GET["contenu"]; $src[] = $_GET["src"]; } 
		//if (@$_GET["contenu"] == 'contenutagsADD') { $contenu[] = $_GET["contenu"]; }
		$contenu = $contenu ?? $_GET["contenu"] ?? "comment";
	} else {
		$contenu = $contenu ?? "comment";
	}
	$dir = $prefixe.$config['attached']['directory'];
	$IssueID = $IssueID ?? $_GET["IssueID"] ?? 0;
	$ProjectID = $ProjectID ?? $_GET["ProjectID"] ?? 0;
	$src = $src ?? $_GET["src"] ?? "tinyissue";
	$SkipUser = $SkipUser ?? $_GET["SkipUser"] ?? false;
	$Type = $Type ?? $_GET["Type"] ?? 'Issue';
	$UserID = $User ?? $_GET["User"] ?? $_GET["UserID"] ?? Auth::user()->id ?? 1;
	$values = array();

	if ($Type == 'User') {
		$resu = Requis("SELECT * FROM users WHERE email = '".$UserID."'");
	} else {
		$UserID = $UserID ?? (is_array($User) ? $User[0] : $User);
		$resu = Requis("SELECT * FROM users WHERE id = ".$UserID);
	}
	$QuelUser = Fetche($resu);
	$QuelUser["language"] = $Langue ?? $QuelUser["language"];

	//Chargement des fichiers linguistiques
	$emailLng = require ($prefixe."app/application/language/en/tinyissue.php");
	$emailLnE = require ($prefixe."app/application/language/en/email.php");
	if ( file_exists($prefixe."app/application/language/".$QuelUser["language"]."/tinyissue.php") && $QuelUser["language"] != 'en') {
		$LnT = require ($prefixe."app/application/language/".$QuelUser["language"]."/tinyissue.php");
		$LnE = require ($prefixe."app/application/language/".$QuelUser["language"]."/email.php");
		$Lng['tinyissue'] = array_merge($emailLng, $LnT);
		$Lng['email'] = array_merge($emailLnE, $LnE);
	} else {
		$Lng['tinyissue'] = $emailLng;
		$Lng['email'] = $emailLnE;
	}

	$optMail = $config["mail"];
	$url = trim($config["url"]);

	//Titre et corps du message selon les configurations choisies par l'administrateur
	$message = "";
	$contenu = $contenu ?? "";
	if (is_array($contenu)) {
		$subject = (file_exists($dir.$contenu[0].'_tit.html')) ? file_get_contents($dir.$contenu[0].'_tit.html') : $Lng[$src[0]]['following_email_'.strtolower($contenu[0]).'_tit'];
		foreach ($contenu as $ind => $val) {
			if ($src[$ind] == 'value') {
				$vals = explode(":", $val);
				$values[$vals[0]] = $vals[1];
//				$message .= '<i>'.$val.'</i>';
//				if (strpos($val, '@') > 0) { $values["email"] = $val; } else { $values["static"] = $val; }
			} else {
				$message .= (file_exists($dir.$val.'.html')) ? file_get_contents($dir.$val.'.html') : $Lng[$src[$ind]]['following_email_'.strtolower($val)];
			}
		}
	} else {
		$message = ($contenu != 'comment') ? $contenu : "";
	}
	
	$subject = $subject ?? 'BUGS';

		//Select email addresses
	if ($Type == 'User') {
		$query  = "SELECT DISTINCT 0 AS project, 1 AS attached, 1 AS tages, USR.email, USR.firstname AS first, USR.lastname as last, CONCAT(USR.firstname, ' ', USR.lastname) AS user, USR.language, 'Welcome on BUGS' AS name, 'Welcome' AS title ";
		$query .= "FROM users AS USR WHERE ";
		$query .= (is_numeric($UserID)) ? "USR.id = ".$UserID : "USR.email = '".$UserID."' "; 
	} else if ($Type == 'TestonsSVP') {
		$query  = "SELECT DISTINCT 0 AS project, 1 AS attached, 1 AS tages, USR.email, USR.firstname AS first, USR.lastname as last, CONCAT(USR.firstname, ' ', USR.lastname) AS user, USR.language, 'Testing mail for any project' AS name, 'Test' AS title ";
		$query .= "FROM users AS USR WHERE USR.id = ".$UserID;
		$message .= " ".$Lng['tinyissue']["email_test"].$config['my_bugs_app']['name'].').';
		$subject = $Lng['tinyissue']["email_test_tit"];
		echo $Lng['tinyissue']["email_test_tit"];
	} else if ($Type == 'noticeonlogin') {
		$query  = "SELECT DISTINCT 0 AS project, 0 AS attached, 0 AS tages, USR.email, USR.firstname AS first, USR.lastname as last, CONCAT(USR.firstname, ' ', ";
		$query .= "USR.lastname) AS user, USR.language, ";
		$query .= "'Robot of BUGS system' AS name, 'A user just connected to BUGS' AS title ";
		$query .= "FROM users AS USR WHERE USR.role_id = 4 ORDER BY USR.id ASC LIMIT 0, 1";
	} else {
		$IssueID = $IssueID ?? 0;
		$query  = "SELECT DISTINCT FAL.project, FAL.attached, FAL.tags, ";
		$query .= "		USR.email, USR.firstname AS first, ";
		$query .= "		USR.lastname as last, CONCAT(USR.firstname, ' ', USR.lastname) AS user, USR.language, ";
		$query .= "		PRO.name, ";
		$query .= "	(SELECT title FROM projects_issues WHERE id = ".$IssueID.") AS title ";
		$query .= "FROM following AS FAL ";
		$query .= "LEFT JOIN users AS USR ON USR.id = FAL.user_id "; 
		$query .= "LEFT JOIN projects AS PRO ON PRO.id = FAL.project_id ";
		$query .= "LEFT JOIN projects_issues AS TIK ON TIK.id = FAL.issue_id ";
		$query .= "WHERE FAL.project_id = ".$ProjectID." ";
		if ($Type == 'Issue') {
			$query .= "AND FAL.project = 0 AND issue_id = ".$IssueID." ";
			$query .= ($SkipUser) ? "AND FAL.user_id NOT IN (".$UserID.") " : "";
			$query .= "AND FAL.project = 0 ";
		} else if ($Type == 'Project') {
			$query .= "AND FAL.project = 1 ";
		}
	}
	$followers = Requis($query);

	if (Nombre($followers) > 0) {
		while ($follower = Fetche($followers)) {
			$subject = wildcards($subject, $follower,$ProjectID, $IssueID, true, $url, $config["my_bugs_app"]["name"], $values);
			$passage_ligne = (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $follower["email"])) ? "\r\n" : "\n";
			$message = str_replace('"', "``", $message);
			$message = stripslashes($message);
			$message = str_replace("'", "`", $message);

			if ($optMail['transport'] == 'mail') {
				//Testons un ping afin de savoir si l'usager est en ligne ou non
//				$pingresult = shell_exec('ping -c 1 -w '.$optMail['smtp']['server']);
//				$EnLigne = (intval(substr($pingresult, strpos($pingresult, "transmitted")+12, 2)) == 1) ? true : false;
//				if (!$EnLigne) { echo '<script>alert("Serveur SMTP inateignable");</script>'; return false; }

				$boundary = md5(uniqid(microtime(), TRUE));
				$headers = 'From: "'.$optMail['from']['name'].'" <'.$optMail['from']['email'].'>'.$passage_ligne;
				$headers .= 'Reply-To: "'.$optMail['replyTo']['name'].'" <'.$optMail['replyTo']['email'].'>'.$passage_ligne;
				$headers .= 'Mime-Version: 1.0'.$passage_ligne;
				$headers .= 'Content-Type: multipart/mixed; charset="'.$optMail['encoding'].'"; boundary="'.$boundary.'"';
				$headers .= $passage_ligne;

				$body = strip_tags( nl2br(str_replace("</p>", "<br /><br />", $message)));
				$body .= $passage_ligne;
				$body .= $passage_ligne;
				$body .= '--'.$boundary.''.$passage_ligne;
				$body .= 'Content-Type: text/html; charset="'.$optMail['encoding'].'"'.$passage_ligne;
				$body .= $passage_ligne;
				$body .= '<p>'.((file_exists($dir."intro.html")) ? file_get_contents($dir."intro.html") : $optMail['intro']).'</p>';
				$body .= $passage_ligne;
				$body .= '<p>'.$message.'</p>';
				$body .= $passage_ligne;
				$body .= '<p>'.((file_exists($dir."bye.html")) ? file_get_contents($dir."bye.html") : $optMail['bye']).'</p>'; 
				$body .= $passage_ligne.'';
				$body = wildcards ($body, $follower,$ProjectID, $IssueID, false, $url, $config["my_bugs_app"]["name"], $values);
				
				//Si l'usager est en ligne, nous tentons l'envoi d'un courriel
				////La fonction try est préparée ici, en ce 21 novembre 2021 afin de l'exploiter prochainement
				try { 
					mail($follower["email"], $subject, $body, $headers);
				} catch {
					echo '<script>alert("Il fut impossible de confier votre courriel au serveur SMTP désigné.");</script>';
					return false;
				};
			} else {
				$mail = new PHPMailer();
				$mail->Mailer = $optMail['transport'];
				switch ($optMail['transport']) {
						//Please submit your code
						//On March 14th, 2017 I had no time to go further on these different types ( case 'PHP', 'sendmail', 'gmail', 'POP3' ) 
					case 'PHP':
						require_once  'application/libraries/PHPmailer/class.phpmailer.php';
						break;
					case 'sendmail':
						require_once '/application/libraries/PHPmailer/class.phpmaileroauth.php';
						break;
					case 'gmail':
						require_once '/application/libraries/PHPmailer/class.phpmaileroauthgoogle.php';
						break;
					case 'POP3':
						require_once '/application/libraries/PHPmailer/class.pop3.php';
						break;
					default:																		//smtp is the second default value after "mail" which has its own code up
						require_once '/application/libraries/PHPmailer/class.smtp.php';
						$mail->SMTPDebug = 1;												// 0 = no output, 1 = errors and messages, 2 = messages only.
						if ($optMail['smtp']['encryption'] == '') {
						} else {
							$mail->SMTPAuth = true;											// enable SMTP authentication
							$mail->SMTPSecure = $optMail['smtp']['encryption'];	// sets the prefix to the server
							$mail->Host = $optMail['smtp']['server'];
							$mail->Port = $optMail['smtp']['port'];
							$mail->Username = $optMail['smtp']['username'];
							$mail->Password = $optMail['smtp']['password'];
						}
						break;
				}

				$mail->CharSet = $optMail['encoding'] ?? 'windows-1250';
				$mail->SetFrom ($optMail['from']['email'], $optMail['from']['name']);
				$mail->Subject = $subject;
				$mail->ContentType = $optMail['plainHTML'] ?? 'text/plain';
				$body .= '<p>'.((file_exists($dir."intro.html")) ? file_get_contents($dir."intro.html") : $optMail['intro']).'</p>'; 
				$body .= '<br /><br />';
				$body .= $message;
				$body .= '<br /><br />';
				$body .= '<p>'.((file_exists($dir."bye.html")) ? file_get_contents($dir."bye.html") : $optMail['bye']).'</p>'; 
				$body = wildcards ($body, $follower,$ProjectID, $IssueID, false, $url, $config["my_bugs_app"]["name"], $values);
				if ($mail->ContentType == 'html') {
					$mail->IsHTML(true);
					$mail->WordWrap = (isset($optMail['linelenght'])) ? $optMail['linelenght'] : 80;
					$mail->Body = $body;
					$mail->AltBody = strip_tags($body);
				} else {
					$mail->IsHTML(false);
					$mail->Body = strip_tags($body);
				}
				$mail->AddAddress ($follower["email"]);
				$result = $mail->Send() ? "Successfully sent!" : "Mailer Error: " . $mail->ErrorInfo;
			}
		}
	}

//return true;
	
function wildcards ($body, $follower,$ProjectID, $IssueID, $tit = false, $url = NULL, $appName = "BUGS", $values = array()) {
	$link = ($url != '') ? $url : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	$lfin = $tit ? ' »' : '</a>';
	//$liss = $tit ? ' « ' : '<a href="'.(str_replace("issue/new", "issue/".$IssueID."/", $link)).'">';
	//$lpro = $tit ? ' « ' : '<a href="'.substr($link, 0, strpos($link, "issue"))."issues?tag_id=1".'">';
	$liss = $tit ? ' « ' : '<a href="'.$link."project/".$ProjectID."/issue/".$IssueID."/".'">';
	$lpro = $tit ? ' « ' : '<a href="'.$link."project/".$ProjectID."/issues?tag_id=1".'">';
	$body = str_replace('BUGS', $appName.' (BUGS)', $body);
	$body = str_replace('{frst}', ucwords($follower["first"]), $body);
	$body = str_replace('{firt}', ucwords($follower["first"]), $body);
	$body = str_replace('{firs}', ucwords($follower["first"]), $body);
	$body = str_replace('{first}', ucwords($follower["first"]), $body);
	$body = str_replace('{firsts}', ucwords($follower["first"]), $body);
	$body = str_replace('{lst}', ucwords($follower["last"]), $body);
	$body = str_replace('{lat}', ucwords($follower["last"]), $body);
	$body = str_replace('{las}', ucwords($follower["last"]), $body);
	$body = str_replace('{last}', ucwords($follower["last"]), $body);
	$body = str_replace('{lasts}', ucwords($follower["last"]), $body);
	$body = str_replace('{ful}', ucwords($follower["user"]), $body);
	$body = str_replace('{fll}', ucwords($follower["user"]), $body);
	$body = str_replace('{full}', ucwords($follower["user"]), $body);
	$body = str_replace('{fulls}', ucwords($follower["user"]), $body);
	$body = str_replace('{pjet}', 	$lpro.$follower["name"].$lfin, $body);
	$body = str_replace('{prjet}', 	$lpro.$follower["name"].$lfin, $body);
	$body = str_replace('{projet}', 	$lpro.$follower["name"].$lfin, $body);
	$body = str_replace('{projets}', $lpro.$follower["name"].$lfin, $body);
	$body = str_replace('{prject}', 	$lpro.$follower["name"].$lfin, $body);
	$body = str_replace('{project}', $lpro.$follower["name"].$lfin, $body);
	$body = str_replace('{projects}',$lpro.$follower["name"].$lfin, $body);
	$body = str_replace('{issu}', 	$liss.$follower["title"].$lfin, $body);
	$body = str_replace('{isue}', 	$liss.$follower["title"].$lfin, $body);
	$body = str_replace('{issue}', 	$liss.$follower["title"].$lfin, $body);
	$body = str_replace('{issues}',	$liss.$follower["title"].$lfin, $body);
	if (isset($values["email"])) 	{ $body = str_replace('{email}',	 $values["email"], $body); } 
	if (isset($values["static"])) { $body = str_replace('{static}', $values["static"], $body);}
	return $body;
}
?>
