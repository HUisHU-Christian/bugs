<?php
class Mail {
	
	public function __construct() {
		$courriel = array (
			$langue => 'FR',
			$message => 'Vous recevez ce message de la part de BUGS. Ceci est le message par défaut', 
			$projectID => 0, 
			$issueID => 0,
			$userID => 0
		);

	}	
	/**
	 * Send the requested message
	 *
	 * @param   string  $message
	 * @param   string  $to
	 * @param   string  $subject
	 * @return  int
	 */
	public static function send_email($message, $to, $subject) {
//		mail($to, $subject, $message);
//		include_once "../app/application/controllers/ajax/SendMail.php";
	}
	
	public static function letMailIt ($ProjectID = 0, $IssueID = 0, $SkipUser = false, $Type = 'Issue', $UserID = 0, $Langue = NULL) {
		$bons = $mals = 0;
		$courriel["issueID"] = $IssueID;
		$courriel["langue"] = $Langue;
		$courriel["projectID"] = $ProjectID;
		$courriel["userID"] = $UserID;
		//Préférences de l'usager
		if (isset($_GET["contenu"])) {
			if ($_GET["contenu"] == 'tagsADD' || $_GET["contenu"] == 'tagsOTE' || $_GET["contenu"] == 'assigned') { $contenu[] = $_GET["contenu"]; $src[] = $_GET["src"]; } 
			$contenu = $contenu ?? $_GET["contenu"] ?? "comment";
		} else {
			$contenu = $contenu ?? "comment";
		}
		$src = $src ?? $_GET["src"] ?? "tinyissue";
		$UserID = ($UserID === NULL) ? Auth::user()->id : $UserID;
		$values = array();

		if ($Type == 'User') {
			$resu = \DB::table('users')->where('email', '=', $UserID)->get();
		} else {
			$UserID = $UserID ?? (is_array($User) ? $User[0] : $User);
			$resu = \DB::table('users')->where('id', '=', $UserID)->get();
		}
		$QuelUser = $resu[0];
		$QuelUser->language = ($Langue === NULL)  ? $QuelUser->language : $Langue;
	
		//Chargement des fichiers linguistiques
		$emailLnE = require ("application/language/en/email.php");
		if ( file_exists("application/language/".$QuelUser->language."/email.php") && $QuelUser->language != 'en') {
			$LnE = require ("application/language/".$QuelUser->language."/email.php");
			$Lng['email'] = array_merge($emailLnE, $LnE);
		} else {
			$Lng['email'] = $emailLnE;
		}
	

		//Titre et corps du message selon les configurations choisies par l'administrateur
		$message = "";
		$contenu = $contenu ?? "";
		if (is_array($contenu)) {
			$subject = (file_exists(\Config::get('application.attached.director').$contenu[0].'_tit.html')) ? file_get_contents(\Config::get('application.attached.director').$contenu[0].'_tit.html') : $Lng[$src[0]]['following_email_'.strtolower($contenu[0]).'_tit'];
			foreach ($contenu as $ind => $val) {
				if ($src[$ind] == 'value') {
					$vals = explode(":", $val);
					$values[$vals[0]] = $vals[1];
				} else {
					$message .= (file_exists(\Config::get('application.attached.director').$val.'.html')) ? file_get_contents(\Config::get('application.attached.director').$val.'.html') : $Lng[$src[$ind]]['following_email_'.strtolower($val)];
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
			$message .= " ".$Lng['tinyissue']["email_test"].\Config('my_bugs_app.name').').';
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
				$query .= "AND issue_id = ".$IssueID." ";
				$query .= ($SkipUser) ? "AND FAL.user_id NOT IN (".$UserID.") " : "";
				//$query .= "AND FAL.project = 0 ";
			} else if ($Type == 'Project') {
				$query .= "AND FAL.project = 1 ";
			}
			$query .= " AND email IS NOT NULL AND email != '' ";
		}		
		$followers = \DB::query($query);
		

		$courriel["follower"] = $followers;

		if (count($followers) > 0) {
			foreach ($followers as $follower) {
				$subject = Mail::wildcards($subject, $follower, true, $values, $courriel);
				$passage_ligne = (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $follower->email)) ? "\r\n" : "\n";
				$message = str_replace('"', "``", $message);
				$message = stripslashes($message);
				$message = str_replace("'", "`", $message);

				if (\Config::get('application.mail.transport') == 'mail') { 
					$result = Mail::SendByMail($follower, $subject, $message, $values, $passage_ligne, $courriel);
				} else {
					$result = Mail::SendByPHPmailer ($follower, $subject, $message, $values, $passage_ligne, $courriel);
				}
				if ($result == "Email successfully sent!") { $bons = $bons + 1; } else { $mals = $mals + 1; }
			}
			if ($mals == 0) {
				return "Emails: All goods";
			} else {
				return "Emails: ".$bons." get good, but ".$mals." get wrong";
			}
		} else {
			return "Nothing to do";
		}	
	}

	private static function SendByMail ($follower, $subject, $message, $values, $passage_ligne, $courriel) {
		$boundary = md5(uniqid(microtime(), TRUE));
		$headers = 'From: "'.\Config::get('configuration.mail.from.name').'" <'.\Config::get('configuration.mail.from.email').'>'.$passage_ligne;
		$headers .= 'Reply-To: "'.\Config::get('configuration.mail.replyTo.name').'" <'.\Config::get('configuration.mail.replyTo.email').'>'.$passage_ligne;
		$headers .= 'Mime-Version: 1.0'.$passage_ligne;
		$headers .= 'Content-Type: multipart/mixed; charset="'.\Config::get('configuration.mail.encoding').'"; boundary="'.$boundary.'"';
		$headers .= $passage_ligne;

		$body = strip_tags( nl2br(str_replace("</p>", "<br /><br />", $message)));
		$body .= $passage_ligne;
		$body .= $passage_ligne;
		$body .= '--'.$boundary.''.$passage_ligne;
		$body .= 'Content-Type: text/html; charset="'.\Config::get('configuration.mail.encoding').'"'.$passage_ligne;
		$body .= $passage_ligne;
		$body .= '<p>'.((file_exists(\Config::get('application.attached.director')."intro.html")) ? file_get_contents(\Config::get('application.attached.director')."intro.html") : \Config::get('configuration.mail.intro')).'</p>';
		$body .= $passage_ligne;
		$body .= '<p>'.$message.'</p>';
		$body .= $passage_ligne;
		$body .= '<p>'.((file_exists(\Config::get('application.attached.director')."bye.html")) ? file_get_contents(\Config::get('application.attached.director')."bye.html") : \Config::get('configuration.mail.bye')).'</p>'; 
		$body .= $passage_ligne.'';
		$body = Mail::wildcards ($body, $follower, false, $courriel, $values);
		
		//Si l'usager est en ligne, nous tentons l'envoi d'un courriel
		////La fonction try est préparée ici, en ce 21 novembre 2021 afin de l'exploiter prochainement
		$result = "Success! Email sent.";
		try { 
			mail($follower->email, $subject, $body, $headers);
		} catch (\Exception $e) {
			return "Error! Email never found its way out.";
		};
		return "Email successfully sent!";
	}

	private static function SendByPHPmailer ($follower, $subject, $message, $values, $passage_ligne, $courriel) {
		$mail = new PHPMailer();
		$mail->Mailer = \Config::get('configuration.mail.transport');
		switch (\Config::get('configuration.mail.transport')) {
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
				if (\Config::get('configuration.mail.smtp.encryption') == '') {
				} else {
					$mail->SMTPAuth = true;											// enable SMTP authentication
					$mail->SMTPSecure = \Config::get('configuration.mail.smtp.encryption');	// sets the prefix to the server
					$mail->Host = \Config::get('configuration.mail.smtp.server');
					$mail->Port = \Config::get('configuration.mail.smtp.port');
					$mail->Username = \Config::get('configuration.mail.smtp.username');
					$mail->Password = \Config::get('configuration.mail.smtp.password');
				}
				break;
		}

		$mail->CharSet = \Config::get('configuration.mail.encoding') ?? 'UTF-8';
		$mail->SetFrom (\Config::get('configuration.mail.from.email'), \Config::get('configuration.mail.from.name'));
		$mail->Subject = $subject;
		$mail->ContentType = \Config::get('configuration.mail.plainHTML') ?? 'text/plain';
		$body .= '<p>'.((file_exists(\Config::get('application.attached.director')."intro.html")) ? file_get_contents(\Config::get('application.attached.director')."intro.html") : \Config::get('configuration.mail.intro')).'</p>'; 
		$body .= '<br /><br />';
		$body .= $message;
		$body .= '<br /><br />';
		$body .= '<p>'.((file_exists(\Config::get('application.attached.director')."bye.html")) ? file_get_contents(\Config::get('application.attached.director')."bye.html") : \Config::get('configuration.mail.bye')).'</p>'; 
		$body = wildcards ($body, $follower, false, $courriel, $values);
		if ($mail->ContentType == 'html') {
			$mail->IsHTML(true);
			$mail->WordWrap = (isset(\Config::get('configuration.mail.linelenght'))) ? \Config::get('configuration.mail.linelenght') : 80;
			$mail->Body = $body;
			$mail->AltBody = strip_tags($body);
		} else {
			$mail->IsHTML(false);
			$mail->Body = strip_tags($body);
		}
		$mail->AddAddress ($follower["email"]);

		try { 
			$mail->Send();
		} catch (\Exception $e) {
			return "Mailer Error: " . $mail->ErrorInfo;
		};
		return "Email successfully sent!";
	}

	private static function wildcards ($body, $follower, $tit, $courriel, $values = array()) {
		$link = (\Config::get('configuration.url') != '') ? \Config::get('configuration.url') : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		$lfin = $tit ? ' »' : '</a>';
		$liss = $tit ? ' « ' : '<a href="'.$link."project/".$courriel["projectID"]."/issue/".$courriel["issueID"]."/".'">';
		$lpro = $tit ? ' « ' : '<a href="'.$link."project/".$courriel["projectID"]."/issues?tag_id=1".'">';
		$body = str_replace('BUGS', \Config::get('configuration.my_bugs_app.name').' (BUGS)', $body);
		$body = str_replace('{frst}', ucwords($follower->first), $body);
		$body = str_replace('{firt}', ucwords($follower->first), $body);
		$body = str_replace('{firs}', ucwords($follower->first), $body);
		$body = str_replace('{first}', ucwords($follower->first), $body);
		$body = str_replace('{firsts}', ucwords($follower->first), $body);
		$body = str_replace('{lst}', ucwords($follower->last), $body);
		$body = str_replace('{lat}', ucwords($follower->last), $body);
		$body = str_replace('{las}', ucwords($follower->last), $body);
		$body = str_replace('{last}', ucwords($follower->last), $body);
		$body = str_replace('{lasts}', ucwords($follower->last), $body);
		$body = str_replace('{ful}', ucwords($follower->user), $body);
		$body = str_replace('{fll}', ucwords($follower->user), $body);
		$body = str_replace('{full}', ucwords($follower->user), $body);
		$body = str_replace('{fulls}', ucwords($follower->user), $body);
		$body = str_replace('{pjet}', 	$lpro.$follower->name.$lfin, $body);
		$body = str_replace('{prjet}', 	$lpro.$follower->name.$lfin, $body);
		$body = str_replace('{projet}', 	$lpro.$follower->name.$lfin, $body);
		$body = str_replace('{projets}', $lpro.$follower->name.$lfin, $body);
		$body = str_replace('{prject}', 	$lpro.$follower->name.$lfin, $body);
		$body = str_replace('{project}', $lpro.$follower->name.$lfin, $body);
		$body = str_replace('{projects}',$lpro.$follower->name.$lfin, $body);
		$body = str_replace('{issu}', 	$liss.$follower->title.$lfin, $body);
		$body = str_replace('{isue}', 	$liss.$follower->title.$lfin, $body);
		$body = str_replace('{issue}', 	$liss.$follower->title.$lfin, $body);
		$body = str_replace('{issues}',	$liss.$follower->title.$lfin, $body);
		if (isset($values["email"])) 	{ $body = str_replace('{email}',	 $values["email"], $body); } 
		if (isset($values["static"])) { $body = str_replace('{static}', $values["static"], $body);}
		return $body;
	}
	
}
