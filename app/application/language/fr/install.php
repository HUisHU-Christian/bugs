<?php
return array(
	'Already_installed' => 'L`application BUGS est déjà isntallée.\nSi vous lisez le présent message et que BUGS ne fonctionne pas correctement, veuillez :  \n-effacer le fichier config.app.php, \n-copier le fichier install/config-setup.php depuis le dépôt git  \n-installer de nouveau. \nVos données ne devraient pas être perdues.',
	'Backup_BDD' => 'Base de données',
	'Backup_BDDemail' => 'Votre adresse courriel de connexion',
	'Backup_BDDpassword' => 'Votre mot de passe de connexion',
	'Backup_BDDwindowers_1' => 'Usager Window ?  Ce lien (site en anglais) peut vous aider ',
	'Backup_BDDwindowers_2' => 'Dump and Restore MySQL Databases using Windows Command Prompt',
	'Backup_BDDosOS' => 'Le système d`exploitation de ce système est : ',
	'Backup_BDDosLIN' => 'Linux / MacOS',
	'Backup_BDDosWIN' => 'Windows',
	'Backup_BDDresuSQL' => 'Voici votre base de données archivée',
	'Backup_BDDresuZIP' => 'Voici votre base de données compressée dans ',
	'Backup_TXT' => ' sauvegarde(s) réussie(s), conservée(s) dans ',
	'Button_CreateConfig' => 'Créer',
	'Complete_awesome' => 'Félicitations!',
	'Complete_login' => 'Entrez ...et profitez-en.',
	'Complete_presentation' => 'L`installation est terminée.  Veuillez <b>supprimer</b> le sous-répertoire <i><b>install</b></i> ou - au moins - le changer de nom. Ceci mettra votre système plus en sécurité.  En effet, un pirate pourrait utiliser ce répertoire et ses fichiers pour modifier la configuration de votre gestionnaire <b><i>Bugs</i></b>.',
	'Database_Error' => 'Impossible de lire la base de données',
	'Database_check' => 'Veuillez vérifier et corriger adéquatement les informations conservées dans le fichier config.app.php - configuration de la base de données ',
	'Database_Connect Error' => 'Vérifiez mot de passe et identifiant à la base de données: connexion échouée',
	'Database_CreateDatabase_success' => 'Nous avons créé la base de données suivante: ',
	'Database_CreateDatabase_failed' => 'Vous ne disposez pas des droits nécessaires à la création d`une base de données',
	'Email' => 'Configuration du courriel',
	'Email_Address' => 'Adresse',
	'Email_Desc' => 'Il peut arriver que Bugs émette des avis par courriel.  Quelle adresse d`émetteur voulez-vous utiliser dans de tels cas ? <br /> Ce site peut vous être utile: <a href="http://www.commentcamarche.net/faq/893-parametres-de-serveurs-pop-imap-et-smtp-des-principaux-fai" target="_blank">http://www.commentcamarche.net/faq/893-parametres-de-serveurs-pop-imap-et-smtp-des-principaux-fai</a>',
	'Email_encoding' => 'Encodage des caractères',
	'Email_encryption' => 'Encodage de transmission',
	'Email_linelenght' => 'Longueur des lignes ',
	'Email_mailerrormsg' => 'Afficher les erreurs lorsqu`il y en a',
	'Email_mailerrormsg_0' => 'Non',
	'Email_mailerrormsg_1' => 'Oui, avec message et erreur',
	'Email_mailerrormsg_2' => 'Oui, erreur seulement',
	'Email_Name' => 'Nom ',
	'Email_password' => 'Mot de passe',
	'Email_plainHTML' => 'Format des courriels sortant',
	'Email_port' => 'Port de courriel sortant',
	'Email_server' => 'Serveur de courriel sortant (SMTP)',
	'Email_transport' => 'Méthode de transport',
	'Email_sendmail_path' => 'Chemin sendmail',
	'Email_username' => 'Nom d`usager',
	'err_tit' => 'Gestion des erreurs',
	'err_detail' => 'Afficher les détail à l`écran',
	'err_exit' => 'Afficher un message permettant à l`usager de revenir à la page d`accueil de BUGS',
	'err_exittxt' => 'Composez ici la phrase invitant à cliquer vers la page d`accueil',
	'err_log' => 'Enregistrer les erreurs dans un fichier ( ./app/storage/logs/) ',
	'err_non' => 'Non',
	'err_oui' => 'Oui',
	'err_result' => 'Visualisation du résultat',
	'InitError_email' => 'Adresse courriel non-valide',
	'InitError_fname' => 'La prénom est nécessaire',
	'InitError_lname' => 'Le nom de famille est nécessaire',
	'InitError_pswrd' => 'Vous devez indiquer un mot de passe',
	'Installation' => 'Définir l`administrateur',
	'Installation_Thanks' => 'Nous vous remercions de la confiance que vous démontrez envers Bugs. Vos fichiers de configuration semblent parfaitement installés, nous pouvons procéder aux informations de fonctionnement.  Veuillez compléter le formulaire ci-bas, relativement l`administrateur principal du système.',
	'Name_finish' => 'Compléter l`installation',
	'Name_first' => 'Prénom',
	'Name_email' => 'Courriel admin',
	'Name_lang' => 'Langue',
	'Name_last' => 'Nom',
	'Name_pswd' => 'Mot de passe',
	'NoAPPfile_0' => 'Désolé, il me semble impossible d`enregistrer les information dans le fichier <code>config.app.php</code>.',
	'NoAPPfile_1' => 'Vous pouvez créer le fichier <code>config.app.php</code> manuellement et y coller les informations suivantes:',
	'NoAPPfile_2' => 'Très bien!  Après cela, vous devrez cliquer sur « Lancer l`installation ».',
	'NoConfigApp' => 'Sorry, we need a config.app.example.php file to work with. Please re-upload this from your Bugs package.',
	'OKconfAPPfile' => 'Il me semble que vous ne disposiez pas d`un fichier <code>config.app.php</code> valide.  Avant d`utiliser Bugs, il faut créer ce fichier.  C`est la première étape de l`installation.  Dans certains cas, il est possible de créer ce fichier <code>config.app.php</code> tout au cours de l`installation, mais pas tous les serveurs le permettent.  Commençons donc !',
	'OkAPPfile' => 'C`est fait, votre fichier <code>config.app.php</code> a bien été créé. Veuillez maintenant cliquer ci-bas sur « Lancer l`installation » et passer les prochaines étapes d`installation.',
	'preferences_gen' => 'Préférences générales',
	'preferences_coula' => 'Couleur associée à la priorité « Après tous les autres » - val. init.: #acacac (PaleGray)',
	'preferences_coulb' => 'Couleur associée à la priorité « Secondaire » - val. init.: #008B8B (DarkCyan)',
	'preferences_coulc' => 'Couleur associée à la priorité « Normale » - val. init.: #32CD32 (LimeGreen)',
	'preferences_could' => 'Couleur associée à la priorité « Accélérez svp » - val. init.: #FF8C00 (Darkorange)',
	'preferences_coule' => 'Couleur associée à la priorité « Urgent » - val. init.: #DC143C (Crimson)',
	'preferences_coulo' => 'Couleur associée à « billet fermé » - val. init.: #000000 (black)',
	'preferences_duree' => 'Durée normale de résolution, en jours  ',
	'preferences_pct_prog' => 'Pourcentage minimum pour passer de « ouvert » à  « <b>En progression</b> » ',
	'preferences_pct_test' => 'Pourcentage minimum pour passer de « En progression » à  « <b>En test</b> » ',
	'preferences_todonbitems' => 'Nombre d`items par colonne dans la page « Tâches » ',
	'preferences_tempsfait' => 'Nombre d`heure(s) facturée(s) par commentaire',
	'Requirement_Check' => 'Veuillez installer toutes les extensions, tous les modules PHP requis.',
	'restore' => 'Reprenez là où vous étiez: restaurez votre BUGS depuis les données d`une installation antérieure',
	'restore_bdds' => 'Source des données',
	'restore_butt' => 'Allons-y!',
	'restore_pswd' => 'Mot de passe de serveur de données',
	'restore_srvr' => 'Adresse du serveur de données',
	'restore_txte' => 'Source des fichiers textes',
	'restore_user' => 'Nom d`usager du serveur de données',
	'RunInstall' => 'Définir l`administrateur du système',
	'SetupConfigFile' => 'Fichier de configuration',
	'SQL_Database' => 'Base de données',
	'SQL_DatabaseGo' => 'Sauvegarder la BDD',
	'SQL_Driver' => 'Pilote',
	'SQL_Host' => 'Hôte',
	'SQL_Password' => 'Mot de passe',
	'SQL_Username' => 'Usager',
	'Time_Local' => 'Local',
	'Time_Timezone' => 'Fuseau horaire',
	'TXT_Database' => 'Le contenu personnalisé de vos courriels',
	'TXT_DatabaseGo' => 'Sauvegarder les textes',
	'TXT_Choose' => 'Marquez d`un crochet les textes à sauvegarder',
	'TXT_All' => 'Tous',
	'UpdateConfigFile' => 'Serveur de courriel sortant',
	'UserPref_apply' => 'Appliquer',
	'UserPref_compte' => 'Mon compte',
	'UserPref_modele' => 'Canevas (couleur de fond, couleurs de texte)',
	'UserPref_Notice' => 'Mes collaborateurs',
	'UserPref_NoticeOnLogIn' => 'Faites-moi connaître chacune des connexions d`usager par courriel',
	'UserPref_prefer' => 'Mes préférences',
	'UserPref_projet' => 'Mes projets',
	'UserPref_projet_0' => 'Afficher un menu déroulant de tous les projets dans le panneau de gauche lorsqu`aucun projet n`est choisi',
	'UserPref_projet_1' => 'Nombre de projets affichés',
	'UserPref_projet_2' => 'Lister les projets dans la partie gauche (en plus du menu déroulant)',
	'UserPref_projet_2a' => 'Oui',
	'UserPref_projet_2b' => 'Non',
	'UserPref_projet_3' => 'Ordre d`affichage des projets',
	'UserPref_projet_3a' => 'Alphabétique',
	'UserPref_projet_3b' => 'Alphabétique inverse',
	'UserPref_projet_4' => 'Utiliser de bouton « Commencer » et « Finir » pour compter mon temps de travail',
	'version' => 'Version',
	'version_actuelle' => 'Votre version active',
	'version_ahead' => 'Votre version est plus avancée que celle du serveur',
	'version_check' => 'État du logiciel',
	'version_commit' => 'Code du plus récent « git commit »  disponible',
	'version_details' => 'Voir les modifications sur le site github',
	'version_disp' => 'Dernier correctif disponible',
	'version_good' => 'Tout est à jour, félicitations',
	'version_need' => 'Besoin de mise à niveau',
	'version_offline' => 'Nous ne pouvons pas vérifier la version disponible, <br />car les fichiers en ligne ne sont pas accessibles.<br /><br />Veuillez vous référer au dépôt git ci-bas:',
	'version_release_date' => 'Date de lancement',
	'version_release_numb' => 'Correctif',
	'version_your' => 'Votre version actuelle',
	'welcome_1' => 'Bienvenue sur BUGS',
	'welcome_2' => 'Si cela vous convient, nous pouvons amorcer le travail en créant votre premier projet et votre premiet ticket.',
	'welcome_projectname' => 'Nom du nouveau projet',
	'welcome_issuedesc' => 'Description globale de la tâche à accomplir par ce billet (Titre)',
	'welcome_issuename' => 'Titre du nouveau billet (le billet comme tel)',
	'welcome_submit' => 'Allons-y ! ',
);