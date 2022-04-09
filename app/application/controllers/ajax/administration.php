<?php

class Ajax_Administration_Controller extends Base_Controller {

	public $layout = null;

	public function __construct() {
		parent::__construct();

		$this->filter('before', 'permission:administration');
		
		$this->config_app = require("../config.app.php");
		$this->prefixe = Administration::prefixe();

		$Lng = require ("application/language/en/install.php"); 
		if ( file_exists("application/language/".\Auth::user()->language."/install.php") && \Auth::user()->language != 'en') {
			$LnT = require ("application/language/".\Auth::user()->language."/install.php");
			$LngSRV = array_merge($Lng, $LnT);
		} else {
			$LngSRV = $Lng;
		}
		$Lng = require ("application/language/en/email.php"); 
		if ( file_exists("application/language/".\Auth::user()->language."/email.php") && \Auth::user()->language != 'en') {
			$LnT = require ("application/language/".\Auth::user()->language."/email.php");
			$LngSRV = array_merge($LngSRV, $Lng, $LnT);
		} else {
			$LngSRV = array_merge($LngSRV, $Lng);
		}
		$this->Langue = $LngSRV;
	}

	public function post_AjourDataBase() {
		\Log::write(2,'Admin management : Update BDD');
		$_GET["MAJsql"] = Input::get('MAJsql');
		Administration::AjourStructureBase("admin");
		return true;
	}


	/**
	* Backup the database into temp directory  
	* @return text			message | count
	*/
	public function post_backupbdd() {
		\Log::write(2,'Admin management : backup BDD');
		$compte = 0;
		$retour = "Non";
		$sortie = "";
		$fichier = $this->prefixe."temp/database_".date("YmdHis");
			if(\DB::table('users')->where('email', '=', Input::get('courriel'))->where('role_id', '=', 4)->count() == 1) {
				$resuUSER = \DB::table('users')->where('email', '=', Input::get('courriel'))->get();
				$QuelUSER = $resuUSER[0];
				if (Input::get('mysystos') == 'Linux') {
					$commande = "mysqldump -u ".$this->config_app['database']['username']." --password=".$this->config_app['database']['password']." ".$this->config_app['database']['database']." > ".$fichier.".sql";
					exec($commande);
				} else {
					$sortie = "-- BUGS 
						SET NAMES utf8;
						SET time_zone = '+00:00';
						SET foreign_key_checks = 0;
						SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
		
					CREATE DATABASE `".$this->config_app['database']['database']."` /*!40100 DEFAULT CHARACTER SET utf8 */;
					USE `".$this->config_app['database']['database']."`;
					";
		
					$resuTABL = \DB::query("SHOW TABLES FROM ".$this->config_app['database']['database']);
					$LesTables = array();
					foreach($resuTABL as $ind => $QuelTABL) {
						foreach ($QuelTABL as $nom => $val) { $tab = $val; }
						$LesTables[] = $val;
						////Récupération de la structure
						$key = "";
						$resuCOLS = \DB::query("SHOW COLUMNS FROM ".$tab);
						$sortie .= "CREATE TABLE IF NOT EXISTS ".$tab." (";
						$lesChampsDeCetteTable = array();
						foreach ($resuCOLS as $QuelCOLS) {
							if ($QuelCOLS->key == 'PRI') { $key = $QuelCOLS->field; }
							$sortie .= "`".$QuelCOLS->field.'` '.$QuelCOLS->type.' '.(($QuelCOLS->null=='NO') ? 'NOT NULL' : 'DEFAULT NULL').''.((trim($QuelCOLS->extra) !='') ? ' ': '').$QuelCOLS->extra.', ';
							$lesChampsDeCetteTable[] = $QuelCOLS->field;
							$lesTypesChampsCetteTa[] = $QuelCOLS->type;
						}
						$sortie .= "PRIMARY KEY (`".$key."`)";
						$sortie .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;
						";
						$sortie .= "
						";
						////Récupération des données
						$compte = 0;
						$lien = "";
						if (\DB::table($tab)->count() > 0 && $tab != 'update_history') {
							$resuVALS = \DB::query("SELECT * FROM ".$tab." ");
							$sortie .= "INSERT INTO `".$tab."` VALUES";
							$sortie .= $lien." (".implode(",", $lesChampsDeCetteTable).") VALUES 
							";
							$lien2 = "";
							foreach ($resuVALS as $QuelVALS) {
								$lien3 = "";
								$sortie .= $lien2." (";
								foreach ($QuelVALS as $val) {
									$sortie .= $lien3;
									$sortie .= (trim($val) == '') ? NULL : "'".addslashes($val)."'";
									$lien3 = ", ";
								}
								$sortie .= ");
								";
								$lien2 = ", ";
							}
						}
						unset($resuCOLS);
					}
					$f = fopen($fichier.".sql", "w");
					fwrite($f, $sortie);
					fclose($f);
				}
				if (file_exists($fichier.".sql")) {
					$zip = new ZipArchive ();
					$zip-> open ($fichier.".zip", ZipArchive :: CREATE); 
					$zip-> addFile ($fichier.".sql"); 
					$zip-> close ();
					$compte = $compte + 1;
					$retour = $this->Langue['Backup_BDDresuSQL'].' : <a href="'.$fichier.'.sql">'.$fichier.'.sql</a><br />';
				}
				if (file_exists($fichier.".zip")) {
					$compte = $compte + 1;
					$retour .= $this->Langue['Backup_BDDresuZIP'].' : <a href="'.$fichier.'.zip">'.$fichier.'.zip</a><br />';
				}
			}
		return Administration::retournons($compte,$retour);
	}

	/**
	* Backup personalized files into the temp directory
	* @return message | count
	*/
	public function post_backuptxt() {
		\Log::write(2,'Admin management : backup options');
		$compte = 0;
		$retour = "Non";
		$namedir = date("YmdHis");
		$MesChoix = Input::all();
		$zip = new ZipArchive ();
		$zip-> open ($this->prefixe.'temp/emails_'.$namedir.'.zip', ZipArchive :: CREATE);

		foreach($MesChoix as $ind => $val) {
			if (file_exists($this->prefixe."/uploads/".$val.".html")) {
					$zip-> addFile ($this->prefixe."/uploads/".$val.".html"); 
					$zip-> addFile ($this->prefixe."/uploads/".$val."_tit.html"); 
					$compte = $compte + 2;
			}
		}
		if (Input::get('config') !== NULL) {
			if (trim(Input::get('config')) != '') {
					$zip-> addFile ($this->prefixe."config.app.php"); 
					$compte = $compte + 1;
			} 
		}
		$zip-> close ();

		$compte = file_exists($this->prefixe.'temp/emails_'.$namedir.'.zip') ? $compte : 0;
		if ($compte > 0) { $retour = $compte.' '.$this->Langue["Backup_TXT"].' <a href="temp/emails_'.$namedir.'.zip">temp/emails_'.$namedir.'.zip</a><br />'; }
		
		return Administration::retournons($compte,$retour);
			
	}

	/**
	* Edit the email's header, footer and other options 
	* @param  text		$fName  ...  name for « From » field
	* @param  text		$fMail  ...  email address for « From » field
	* @param  text		$rName  ...  name for « Reply to » field
	* @param  text		$rMail  ...  email address for « Reply to » field
	* @param  text		$intro  ...  header of every emails
	* @param  text		$bye 	  ...  footer of every emails
	* @return confirmation phrase
	*/
	public function post_courriels() {
		\Log::write(2,'Admin management : edit the email intro and bye files');
		//Définition des variables
		
		$dir = $this->config_app["attached"]["directory"];
		$dir = (substr($dir, -1) == '/') ? $dir : substr($dir, 0, -1);
		$MesLignes = array();
		$NumLigne = array();
		$NomFichier = $this->prefixe."config.app.php";
		$rendu = 0;
	
		//Sauvegarde du fichier original
		copy ($NomFichier, $this->prefixe."config.app.".date("Ymdhis").".php");
	
		//Lecture du fichier de configuration
		////Ouvrons le fichier de configuration
		$RefFichier = fopen($NomFichier, "r");
		////Boucle de lecture
		while (!feof($RefFichier)) {
			$MesLignes[$rendu] = fgets($RefFichier);
			if (strpos($MesLignes[$rendu], "'replyTo'") 	!== false && !isset($NumLigne["mail"])	 ) { $NumLigne["mail"] = $rendu; }
			if (strpos($MesLignes[$rendu], "'intro' =>") !== false && !isset($NumLigne["forma"]) )	{ $NumLigne["forma"] = $rendu; }
			++$rendu;
		}
		fclose($RefFichier);
	
		if ($NumLigne["mail"] > 0 ) {
			$MesLignes[$NumLigne["mail"] - 5] = "	'mail' => array(
";
			$MesLignes[$NumLigne["mail"] - 4] = "		'from' => array(
";
			$MesLignes[$NumLigne["mail"] - 3] = "			'name' => '".str_replace("'", "`", Input::get('fName'))."',
";
			$MesLignes[$NumLigne["mail"] - 2] = "			'email' => '".str_replace("'", "`", Input::get('fMail'))."',
";
			$MesLignes[$NumLigne["mail"] - 1] = "		),
";
			$MesLignes[$NumLigne["mail"] + 0] = "		'replyTo'  => array(
";
			$MesLignes[$NumLigne["mail"] + 1] = "			'name' => '".str_replace("'", "`", Input::get('rName'))."',
";
			$MesLignes[$NumLigne["mail"] + 2] = "			'email' => '".str_replace("'", "`", Input::get('rMail'))."',
";
			$MesLignes[$NumLigne["mail"] + 3] = "		),
";
		}
		if ($NumLigne["forma"] > 0) {
			$MesLignes[$NumLigne["forma"] + 0] = "		'intro' => '',
";
			$MesLignes[$NumLigne["forma"] + 1] = "		'bye' => '',
";
		}
	
		//Textes reçus et devant être enregistrés
		$f = fopen($this->prefixe.$dir."intro.html", "w");
		fputs($f, str_replace("'", "`", Input::get('intro')));
		fclose($f);
		$f = fopen($this->prefixe.$dir."bye.html", "w");
		fputs($f, str_replace("'", "`", Input::get('bye')));
		fclose($f);
	
		//Enregistrement du nouveau fichier corrigé
		$a = file_put_contents($NomFichier, $MesLignes);
		return Administration::retournons($a);
			
	}

	/**
	* Edit email content for a specific event ( ex.: issue update, project update, tag added to an issue, etc).
	* @return confirmation phrase
	*/
	public function post_emails() {
		\Log::write(2,'Admin management : edit the email content texts');
		$a = 0;
		$dir = $this->prefixe.$this->config_app['attached']['directory'];
	
		//Enregistrement du texte reçu
		if (Input::get('Enreg') != 'false') {
			\Log::write(4,'Admin management : edit the email content text for '.Input::get('Quel').' ');
			$a = file_put_contents($dir.Input::get('Quel').".html", Input::get('Prec'));
			$c = file_put_contents($dir.Input::get('Quel')."_tit.html", Input::get('Titre'));
			$a = ($c == 0) ? 0 : $a;
		}

		//Texte retourné en sortie en vue de l'affichage demandé par l'usager
		$Sortie = ((isset($this->Langue["following_email_".Input::get('Suiv')])) ? $this->Langue["following_email_".Input::get('Suiv')] : __('tinyissue.following_email_'.Input::get('Suiv'))).'||'.((isset($this->Langue['following_email_'.Input::get('Suiv').'_tit'])) ? $this->Langue["following_email_".Input::get('Suiv').'_tit'] : __('tinyissue.following_email_'.Input::get('Suiv').'_tit') );
		if (file_exists($dir.Input::get('Suiv').".html")) {
			$Sortie = file_get_contents($dir.Input::get('Suiv').".html");
			if (file_exists($dir.Input::get('Suiv')."_tit.html")) {
				$Sortie .= '||'.file_get_contents($dir.Input::get('Suiv')."_tit.html");
			} else {
				$Sortie .= '||'.$this->Langue["following_email_".Input::get('Suiv').'_tit'];
			}
		}

		$retour = Administration::retournons($a); 

		return $retour.'||'.$Sortie;
	}

	/**
	* Edit the administrator's preferences on error managing
	*
	* @param  integer	$acuracy   	... 3 		: how acurate are the log recordings
	* @param  text		$detail    	... 'true' : show details on user's screen   		'false' : show error 500 page
	* @param  integer	$delay    	...  99    : show details on user's screen for $delay seconds long
	* @param  text		$log       	... 'true' : log errors into  app/storage/logs		'false' : do not log
	* @param  text		$exit	   	... 'true' : use exitxt content							'false' : exit(0)
	* @param  text		$exittxt	   ... show text on screen
	* @return confirmation phrase
	*/
	public function post_errors() {
		\Log::write(2,'Admin management : Errors logging -> edit the setup.php file');
		if (Auth::user()->role_id != 4) {
			$a = 0;
		} else {
			$depuisAvr22 = array('acuracy');
			$repereAvr22 = array();
			$descrptions = array(
				'acuracy' => '
	/*
	|--------------------------------------------------------------------------
	| Acuracy
	|--------------------------------------------------------------------------
	|
	| Précision et fréquence des informations enregistrées dans le registre
	| 0: ERROR : seules les erreurs sont enregistrées
	| 1: ERR  : 
	| 2: MORE : 
	| 3: INFO : 
	| 4: SAYS :
	| 5: DETAILS : toutes les actions sont enregistrées
	| 
	*/
'
			);
			$NomFichier = "application/config/error.php";
			$RefFichier = fopen($NomFichier, "r");
			$rendu = 0;
			while (!feof($RefFichier)) {
				$MesLignes[$rendu] = fgets($RefFichier);
				if (strpos($MesLignes[$rendu], "'acuracy' => ")   > 0) { $MesLignes[$rendu] = "   'acuracy' => ".Input::get('acuracy').",
				"; $repereAvr22[] = 'acuracy'; }
				if (strpos($MesLignes[$rendu], "'delay' => ")   > 0) { $MesLignes[$rendu] = "   'delay' => ".Input::get('delay').",
				"; }
				if (strpos($MesLignes[$rendu], "'detail' => ") > 0) { $MesLignes[$rendu] = "   'detail' => ".Input::get('detail').", 
				"; }
				if (strpos($MesLignes[$rendu], "'exit' => ")   > 0) { $MesLignes[$rendu] = "   'exit' => ".((Input::get('exit') == 'false') ? 1 : "'".Input::get('exittxt').".'").",
				"; }
				if (strpos($MesLignes[$rendu], "'log' => ") 	  > 0) { $MesLignes[$rendu] = "   'log' => ".Input::get('log') .",
				"; }
				$rendu = $rendu + 1;
			}
			fclose($RefFichier);
			foreach (array_diff($depuisAvr22, $repereAvr22) as $ind => $val) {
				array_pop($MesLignes);
				$MesLignes[] = $descrptions[$val];
				$MesLignes[] = '
	\''.$val.'\' => '.Input::get($val).',
';
				$MesLignes[] = '
);';
				\Log::write(4,'Admin management : Errors logging -> add new element ( '.$val.') in setup.php');
			}
			$a = file_put_contents($NomFichier, $MesLignes);
		}
		return Administration::retournons($a);
	}

	/*
	Edit the general preferences
	* @param  text $input_coula	...	hexadecimal color for priority « After others »
	* @param  text $input_coulb	...	hexadecimal color for priority « Secondary »
	* @param  text $input_coulc	...	hexadecimal color for priority « Normal »
	* @param  text $input_could	...	hexadecimal color for priority « Take me first »
	* @param  text $input_coule	...	hexadecimal color for priority « Urgent »
	* @param  text $input_coulo	...	hexadecimal color for closed ticket
	* @param  int  $input_duree	...	default duration for a ticket
	* @param  text $input_prog		...	percentage value between « open » and « Work in progress »
	* @param  text $input_test		...	percentage value between « Work in progress » and  « testing »
	* @param  int  $input_TodoNbItems	number of items shwon by column
	* @param  int  $input_TempsFait		default value for « workingtime »
	*/	
	public function post_prefGen() {
		//Sauvegarde du fichier original
		\Log::write(2,'Admin management : General preferences');
		$SavFichier = "config.app.".date("Ymdhis").".php";
		copy ($this->prefixe."config.app.php", $this->prefixe.$SavFichier);
	
		//Définition des variables 
		$MesLignes = array();
		$NumLigne = array();
		$NomFichier = $this->prefixe."config.app.php";
		$rendu = 0;
	
		//Lecture du fichier de configuration
		////Ouvrons le fichier de configuration
		$RefFichier = fopen($NomFichier, "r");

		////Boucle de lecture
		while (!feof($RefFichier)) {
			$MesLignes[$rendu] = fgets($RefFichier);
			if (strpos($MesLignes[$rendu], "'Percent'") !== false && !isset($NumLigne['Percent']))  { 
				$NumLigne['Percent'] = $rendu; 
				$MesLignes[$rendu] = substr($MesLignes[$rendu], 0, strpos($MesLignes[$rendu], '=>')+2)." array (100,0,".Input::get('prog').",".Input::get('test').",100),
";
			}
			if (strpos($MesLignes[$rendu], "'duration'") !== false && !isset($NumLigne['duration']))  { 
				$NumLigne['duration'] = $rendu; 
				$MesLignes[$rendu] = substr($MesLignes[$rendu], 0, strpos($MesLignes[$rendu], '=>')+2)." ".Input::get('duree').",
";
			}
			if (strpos($MesLignes[$rendu], "'PriorityColors'") !== false && strpos($MesLignes[$rendu], "****") === false && !isset($NumLigne['PriorityColors']))  { 
				$NumLigne['PriorityColors'] = $rendu; 
				$MesLignes[$rendu] = "	'PriorityColors' => array ('".Input::get('coulo')."', '".Input::get('coula')."','".Input::get('coulb')."','".Input::get('coulc')."','".Input::get('could')."','".Input::get('coule')."'), 
";
			}
			if (strpos($MesLignes[$rendu], "'TodoNbItems'") !== false && !isset($NumLigne['TodoNbItems']))  { 
				$NumLigne['TodoNbItems'] = $rendu; 
				$MesLignes[$rendu] = "	'TodoNbItems' => ".Input::get('TodoNbItems').",
";
			}
			if (strpos($MesLignes[$rendu], "'TempsFait'") !== false && !isset($NumLigne['TempsFait']))  { 
				$NumLigne['TempsFait'] = $rendu; 
				$MesLignes[$rendu] = "	'TempsFait' => ".Input::get('TempsFait').",
";
			}
			++$rendu;
		}
		fclose($RefFichier);

		//Ajout des lignes nouvellement créées, pour ceux qui n'ont pas fait la mise à jour de leur fichier config.app.php
		//12 octobre 2021 
		if (!isset($NumLigne['TodoNbItems'])) {
			$passe[1] = $MesLignes[($rendu-1)]; 
			$passe[2] = $MesLignes[($rendu-2)];
			unset($MesLignes[($rendu-1)], $MesLignes[($rendu-2)]); 
			$rendu = $rendu -2;
			$MesLignes[($rendu++)] = "	/** Todo : Number of items per column
		";
			$MesLignes[($rendu++)] = "	*/
		";
			$MesLignes[($rendu++)] = "	'TodoNbItems' => ".Input::get('TodoNbItems').",
		";
			$MesLignes[($rendu++)] = "
		";
			$MesLignes[($rendu++)] = $passe[2];
			$MesLignes[($rendu++)] = $passe[1];
		}
		if (!isset($NumLigne['TempsFait'])) {
			$passe[1] = $MesLignes[($rendu-1)]; 
			$passe[2] = $MesLignes[($rendu-2)];
			unset($MesLignes[($rendu-1)], $MesLignes[($rendu-2)]); 
			$rendu = $rendu -2;
			$MesLignes[($rendu++)] = "	/** TempsFait
		";
			$MesLignes[($rendu++)] = "	*	Default duration of work (in hours) to be charged to your client for every comment describing a job done
	*  Default value is 1
		*/
		";
			$MesLignes[($rendu++)] = "	'TempsFait' => ".Input::get('TempsFait').",
		";
			$MesLignes[($rendu++)] = "
		";
			$MesLignes[($rendu++)] = $passe[2];
			$MesLignes[($rendu++)] = $passe[1];
		}
		//Enregistrement du nouveau fichier corrigé
		$a = file_put_contents($NomFichier, $MesLignes);
		return Administration::retournons($a);
	}

	/**
	* Edit the email's SMTP configuration 
	* @return confirmation phrase
	*/
	public function post_smtp() {
		\Log::write(2,'Admin management : email exchange definitions');
		//Définition des variables
		$MesLignes = array();
		$NumLigne = array();
		$repere = 0;
		$NomFichier = $this->prefixe."config.app.php";
		$rendu = 0;
	
		//Sauvegarde du fichier original
		$SavFichier = "config.app.".date("Ymdhis").".php";
		copy ($this->prefixe."config.app.php", $this->prefixe.$SavFichier);
	
		//Lecture du fichier de configuration
		////Ouvrons le fichier de configuration
		$RefFichier = fopen($NomFichier, "r");
		////Boucle de lecture
		while (!feof($RefFichier)) {
			$MesLignes[$rendu] = fgets($RefFichier);
			if ($repere > 0) {
				foreach(Input::all() as $ind => $val) {
					if (strpos($MesLignes[$rendu], "'".$ind."'") !== false && !isset($NumLigne[$ind]))  { 
						$NumLigne[$ind] = $rendu; 
						$MesLignes[$rendu] = substr($MesLignes[$rendu], 0, strpos($MesLignes[$rendu], '=>')+2)." '".str_replace("'", "`", $val)."',
";
					}
				}
			} else {
				if (strpos($MesLignes[$rendu], "/**  Mail") !== false ) { $repere = $rendu; }
			}
			++$rendu;
		}
		fclose($RefFichier);
		
		//Enregistrement du nouveau fichier corrigé  
		$a = file_put_contents($NomFichier, $MesLignes);
		return Administration::retournons($a);
	}

}