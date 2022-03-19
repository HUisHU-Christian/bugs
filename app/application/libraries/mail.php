<?php
class Mail {
	
	private $detail = array();
	private $bye = array();
	private $intro = array();
	private $message = array();
	private $subject = array();
	private $values = array();

	
	public function __construct() {
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
	
	public static function letMailIt ($details, $UserID = 0, $Langue = NULL) {
		global $bye, $detail, $intro, $message, $subject, $values;
		$detail = $details;
		$bons = $mals = 0;
		$UserID = ($UserID === NULL) ? (( $detail['user'] !== NULL ) ? $detail['user'] : \Auth::user()->id) : $UserID;
		$values = array();

		if ($detail['Type'] == 'User') {
			$resu = \DB::table('users')->where('email', '=', $UserID)->get();
		} else {
			$UserID = $UserID ?? (is_array($User) ? $User[0] : $User);
			$resu = \DB::table('users')->where('id', '=', $UserID)->get();
		}
		$QuelUser = $resu[0];
		$QuelUser->language = ($Langue === NULL)  ? $QuelUser->language : $Langue;
		//Chargement des fichiers linguistiques
		$Lng['tinyissue'] = require ("application/language/en/tinyissue.php");
		$Lng['email'] = require ("application/language/en/email.php");
		if ( file_exists("application/language/".$QuelUser->language."/tinyissue.php") && $QuelUser->language != 'en') {
			$LnA = require ("application/language/".$QuelUser->language."/tinyissue.php");
			$Lng['tinyissue'] = array_merge($Lng['tinyissue'], $LnA);
		}
		if ( file_exists("application/language/".$QuelUser->language."/email.php") && $QuelUser->language != 'en') {
			$LnA = require ("application/language/".$QuelUser->language."/email.php");
			$Lng['email'] = array_merge($Lng['email'],$LnA);
		}

		//Titre et corps du message selon les configurations choisies par l'administrateur
		$message = "";
		$subject = (file_exists('../uploads/'.$detail["contenu"][0].'_tit.html')) 
					? file_get_contents('../uploads/'.$detail["contenu"][0].'_tit.html')
					: $Lng[$detail['src'][0]]['following_email_'.strtolower($detail["contenu"][0]).'_tit'];
		$byeCnt = file_exists('../'.\Config::get('application.attached.directory')."bye.html") 
					? file_get_contents('../'.\Config::get('application.attached.directory')."bye.html") 
					: \Config::get('application.mail.bye');
		$introCnt = file_exists('../'.\Config::get('application.attached.directory')."intro.html") 
					? file_get_contents('../'.\Config::get('application.attached.directory')."intro.html") 
					: \Config::get('application.mail.intro'); 

		foreach ($detail["contenu"] as $ind => $val) {
			if ($detail['src'][$ind] == 'value') {
				$vals = explode(":", $val);
				$values[$vals[0]] = $vals[1];
			} else {
				$message .= (file_exists('../uploads/'.$val.'.html')) 
							? file_get_contents('../uploads/'.$val.'.html') 
							: $Lng[$detail['src'][$ind]]['following_email_'.strtolower($val)];
			}
		}

		//Select email addresses
		if ($detail['Type'] == 'User') {
			$followers = \DB::table('users')
				->select(array(DB::raw("0 as project"), DB::raw("1 as attached"), DB::raw("1 as tages"), 'email','firstname','lastname','language',DB::raw("'Welcome on BUGS' as name"),DB::raw("'Welcome' as title")))
				->where('id', '<>', \Auth::user()->id)
				->whereNotNull('email')
				->where(((is_numeric($UserID)) ? "id" : "email"), "=", $UserID)
				->order_by('id')
				->get();
		} else if ($detail['Type'] == 'TestonsSVP') {
			$followers = \DB::table('users')
				->select(array(DB::raw("0 as project"), DB::raw("1 as attached"), DB::raw("1 as tages"), 'email','firstname','lastname','language',DB::raw("'Testing mail for any project' as name"),DB::raw("'Test' as title")))
				->where('id', '=', \Auth::user()->id)
				->whereNotNull('email')
				->order_by('id')
				->get();
			$message .= " ".$Lng['tinyissue']["email_test"].\Config::get('application.my_bugs_app.name').').';
			$subject = $Lng['tinyissue']["email_test_tit"];
			echo $Lng['tinyissue']["email_test_tit"];
		} else if ($detail['Type'] == 'noticeonlogin') {
			$followers = \DB::table('users')
				->select(array(DB::raw("0 as project"), DB::raw("0 as attached"), DB::raw("0 as tages"), 'email','firstname','lastname','language',DB::raw("'Robot of BUGS system' as name"),DB::raw("'A user just connected to BUGS' as title")))
				->where('id', '<>', \Auth::user()->id)
				->where('role_id', '=', 4)
				->where('preferences', 'LIKE', '%noticeOnLogIn=true%')
				->where('email', '<>', '')
				->whereNotNull('email')
				->order_by('id')
				->get();
		} else {
			$detail['IssueID'] = $detail['IssueID'] ?? 0;
			$followers = \DB::table('following')
				->select(array(DB::raw("0 as project"), DB::raw("0 as attached"), DB::raw("0 as tages"), 'users.email','users.firstname','users.lastname','users.language','projects.name', 'projects_issues.title'))
				->join('users', 'users.id', '=', 'following.user_id')
				->join('projects', 'projects.id', '=', 'following.project_id')
				->join('projects_issues', 'projects_issues.id', '=', 'following.issue_id')
				->where('following.id', '"', $detail['ProjectID'])
				->where('users.email', '<>', '')
				->whereNotNull('users.email')
				->where('following.issue_id', '=', $detail['IssueID'])
				->whereNotIn('following.user_id', (($detail['Type'] == 'Issue' && $detail['SkipUser']) ? $UserID : array(0)))
				->where('following.project', '=', (($detail['Type'] == 'Issue') ? 0 : 1 ))
				->order_by('users.id')
				->get();
		}		

		if ($followers) {
			foreach ($followers as $follower) {
				$subject = Mail::wildcards($subject, $follower, true);
				$passage_ligne = (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $follower->email)) ? "\r\n" : "\n";

				$intro = Mail::wildcards ('<p>'.$introCnt.'</p>', $follower, false);
				$bye = Mail::wildcards ('<p>'.$byeCnt.'</p>', $follower, false);
		
				$message = str_replace('"', "``", $message);
				$message = stripslashes($message);
				$message = str_replace("'", "`", $message);
				$contenu = Mail::wildcards ($message, $follower, false);

				if (\Config::get('application.mail.transport') == 'mail') {
					$result = Mail::SendByMail($contenu, $follower, $passage_ligne);
				} else {
					$result = Mail::SendByPHPmailer($contenu, $follower, $passage_ligne);
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

	private static function SendByMail ($contenu, $follower, $passage_ligne) {
		global $bye, $detail, $intro, $subject, $values;
		$boundary = md5(uniqid(microtime(), TRUE));
		$headers = 'From: "'.\Config::get('application.mail.from.name').'" <'.\Config::get('application.mail.from.email').'>'.$passage_ligne;
		$headers .= 'Reply-To: "'.\Config::get('application.mail')['replyTo']['name'].'" <'.\Config::get('application.mail')['replyTo']['email'].'>'.$passage_ligne;
		$headers .= 'Mime-Version: 1.0'.$passage_ligne;
		$headers .= 'Content-Type: multipart/mixed; charset="'.\Config::get('application.mail.encoding').'"; boundary="'.$boundary.'"';
		$headers .= $passage_ligne;
		$body  = $passage_ligne;
		$body .= strip_tags( nl2br(str_replace("</p>", "<br /><br />", $intro)));
		$body .= $passage_ligne;
		$body .= strip_tags( nl2br(str_replace("</p>", "<br /><br />", $contenu)));
		$body .= $passage_ligne;
		$body .= strip_tags( nl2br(str_replace("</p>", "<br /><br />", $bye)));
		$body .= $passage_ligne;
		$body .= $passage_ligne;
		$body .= $contenu;
		$body .= $passage_ligne;
		$body .= $passage_ligne;
		$body .= '--'.$boundary.''.$passage_ligne;
		$body .= 'Content-Type: text/html; charset="'.\Config::get('application.mail.encoding').'"'.$passage_ligne;
		$body .= $passage_ligne;
		$body .= $intro;
		$body .= $passage_ligne;
		$body .= '<p>'.$contenu.'</p>';
		$body .= $passage_ligne;
		$body .= $bye;
		$body .= $passage_ligne.'';
		
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

	private static function SendByPHPmailer ($follower, $passage_ligne) {
		global $bye, $detail, $intro, $subject, $values;
		$mail = new PHPMailer();
		$mail->Mailer = \Config::get('application.mail.transport');
		switch (\Config::get('application.mail.transport')) {
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
				$mail->SMTPDebug = 1;																		// 0 = no output, 1 = errors and messages, 2 = messages only.
				break;
		}

		if (\Config::get('application.mail.smtp.encryption') != '') {
			$mail->SMTPAuth = true;																	// enable SMTP authentication
			$mail->SMTPSecure = \Config::get('application.mail.smtp.encryption');	// sets the prefix to the server
			$mail->Host = \Config::get('application.mail.smtp.server');
			$mail->Port = \Config::get('application.mail.smtp.port');
			$mail->Username = \Config::get('application.mail.smtp.username');
			$mail->Password = \Config::get('application.mail.smtp.password');
		}
		$mail->CharSet = \Config::get('application.mail.encoding') ?? 'UTF-8';
		$mail->SetFrom = \Config::get('application.mail.from.email');
		$mail->FromName = \Config::get('application.mail.from.name');
		$mail->Subject = $subject;
		$mail->ContentType = \Config::get('application.mail.plainHTML') ?? 'text/plain';
		$body .= $intro; 
		$body .= '<br /><br />';
		$body .= $contenu;
		$body .= '<br /><br />';
		$body .= $bye; 
		if ($mail->ContentType == 'html') {
			$mail->IsHTML(true);
			$mail->WordWrap = (\Config::get('application.mail.linelenght') !== NULL) ? \Config::get('application.mail.linelenght') : 80;
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

	private static function wildcards ($body, $follower, $tit) {
		global $bye, $detail, $intro, $message, $subject, $values;
		$link = (\Config::get('application.url') != '') ? \Config::get('application.url') : ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http")."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		$lfin = $tit ? ' »' : '</a>';
		$liss = $tit ? ' « ' : '<a href="'.$link."project/".$detail["ProjectID"]."/issue/".$detail["IssueID"]."/".'">';
		$lpro = $tit ? ' « ' : '<a href="'.$link."project/".$detail["ProjectID"]."/issues?tag_id=1".'">';
		$body = str_replace('BUGS', \Config::get('application.my_bugs_app.name').' (BUGS)', $body);
		$body = str_replace('{frst}', ucwords($follower->firstname), $body);
		$body = str_replace('{firt}', ucwords($follower->firstname), $body);
		$body = str_replace('{firs}', ucwords($follower->firstname), $body);
		$body = str_replace('{first}', ucwords($follower->firstname), $body);
		$body = str_replace('{firsts}', ucwords($follower->firstname), $body);
		$body = str_replace('{lst}', ucwords($follower->lastname), $body);
		$body = str_replace('{lat}', ucwords($follower->lastname), $body);
		$body = str_replace('{las}', ucwords($follower->lastname), $body);
		$body = str_replace('{last}', ucwords($follower->lastname), $body);
		$body = str_replace('{lasts}', ucwords($follower->lastname), $body);
		$body = str_replace('{ful}',  ucwords($follower->firstname.' '.$follower->lastname), $body);
		$body = str_replace('{fll}',  ucwords($follower->firstname.' '.$follower->lastname), $body);
		$body = str_replace('{full}', ucwords($follower->firstname.' '.$follower->lastname), $body);
		$body = str_replace('{fulls}',ucwords($follower->firstname.' '.$follower->lastname), $body);
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
