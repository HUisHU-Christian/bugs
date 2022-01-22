	function AppliquerCourriel() {
		var champs = new Array('input_email_from_name','input_email_from_email','input_email_replyto_name','input_email_replyto_email');
		var compte = 0;
		var intro = CachonsEditor(7);
		var bye = CachonsEditor(8);
		for (x=0; x<champs.length; x++) {
			if (document.getElementById(champs[x]).style.backgroundColor == 'red' ) { return false; }
			if (document.getElementById(champs[x]).style.backgroundColor == 'yellow' ) { compte = compte + 1; }
		}
		if (compte == 0 && intro == IntroInital && bye == TxByeInital) { return false; }
		for (x=0; x<champs.length; x++) {
			document.getElementById(champs[x]).style.backgroundColor = 'red';
		}

		$.post(siteurl + 'ajax/administration/courriels', {
			fName :	document.getElementById('input_email_from_name').value,
			fMail :	document.getElementById('input_email_from_email').value,
			rName :	document.getElementById('input_email_replyto_name').value,
			rMail :	document.getElementById('input_email_replyto_email').value,
			intro :	intro,
			bye : 	bye
		}, function(data){
			IntroInital = intro; 
			TxByeInital = bye;
			Verdissons(champs,data);
		});
	}
	
	function AppliquerPrefGen() {
		var champs = new Array('input_coula','input_coulb','input_coulc','input_could','input_coule','input_coulo','input_duree','input_prog','input_test','input_TodoNbItems','input_TempsFait');
		$.post(siteurl + 'ajax/administration/prefGen', {
			coula			: document.getElementById('input_coula').value,
			coulb			: document.getElementById('input_coulb').value,
			coulc			: document.getElementById('input_coulc').value,
			could			: document.getElementById('input_could').value,
			coule			: document.getElementById('input_coule').value,
			coulo			: document.getElementById('input_coulo').value,
			duree			: document.getElementById('input_duree').value,
			prog			: document.getElementById('input_prog').value,
			test			: document.getElementById('input_test').value,
			TodoNbItems	: document.getElementById('input_TodoNbItems').value,
			TempsFait	: document.getElementById('input_TempsFait').value
		}, function(data){
			Verdissons(champs,data);
		});
	}
	
	function AppliquerServeur() {
		champs = new Array('input_email_encoding','input_email_linelenght','input_email_server','input_email_port','input_email_encryption','input_email_username','input_email_password','select_Email_transport','select_Email_plainHTML','input_email_mailerrormsg');
		if (!VerifChamps(champs)) { return false; }
		$.post(siteurl + 'ajax/administration/smtp', {
			transport 	: 	document.getElementById('select_Email_transport').value,
			plainHTML 	: 	document.getElementById('select_Email_plainHTML').value,
			encoding 	: 	document.getElementById('input_email_encoding').value,
			linelenght 	: 	document.getElementById('input_email_linelenght').value,
			server 		: 	document.getElementById('input_email_server').value,
			port 			: 	document.getElementById('input_email_port').value,
			encryption 	: 	document.getElementById('input_email_encryption').value,
			username 	: 	document.getElementById('input_email_username').value,
			password 	: 	document.getElementById('input_email_password').value,
			mailerrormsg : 	document.getElementById('input_email_mailerrormsg').value
		}, function(data){
			Verdissons(champs,data);
		});
	}

	function AppliquerErr() {
		//Afin que cette fonction appelle correctement la fonction ajax ci-bas, il fallut modifier le fichier ./app/application/routes.php
		$.post(siteurl + 'ajax/administration/errors', {
			detail : (document.getElementById('input_err_detail_true').checked ? 'true': 'false'),
			log : (document.getElementById('input_err_log_true').checked ? 'true' : 'false'),
			exit : (document.getElementById('input_err_exit_true').checked ? 'true' : 'false'),
			exittxt : document.getElementById('input_err_exittxt').value,
			delay : document.getElementById('input_err_delay').value
		}, function(data){
			Verdissons(Array('input_err_exittxt'),data);
		});
	}

	function AppliquerTest(Qui) {
		var champs = new Array('input_email_from_name','input_email_from_email','input_email_replyto_name','input_email_replyto_email');
		var compte = 0;
		for (x=0; x<champs.length; x++) {
			if (document.getElementById(champs[x]).style.backgroundColor == 'red' ) { return false; }
			if (document.getElementById(champs[x]).style.backgroundColor == 'yellow' ) { compte = compte + 1; }
		}
		if (compte > 0) { alert("Vous devez mettre à jour avant de tester"); return false; }

		var xhttp = new XMLHttpRequest();
		var NextPage = 'app/application/controllers/ajax/SendMail.php?Type=TestonsSVP&User=' + Qui;
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (xhttp.responseText != '' ) {
					document.getElementById('global-notice').innerHTML = xhttp.responseText;
					document.getElementById('global-notice').style.display = 'block';   
					setTimeout(function(){
						document.getElementById('global-notice').style.display = 'none';   
					}, 7500);
				}
			}
		};
		xhttp.open("GET", NextPage, true);
		xhttp.send(); 

		//Afin que cette fonction appelle correctement la fonction ajax ci-bas, il fallut modifier le fichier ./app/application/routes.php
		$.post(siteurl + 'ajax/administration/backuptxt', {
			detail : (document.getElementById('input_err_detail_true').checked ? 'true': 'false'),
			log : (document.getElementById('input_err_log_true').checked ? 'true' : 'false'),
			exit : (document.getElementById('input_err_exit_true').checked ? 'true' : 'false'),
			exittxt : document.getElementById('input_err_exittxt').value
		}, function(data){
			Verdissons(Array('input_err_exittxt'),data);
		});
	}
	
	function BackupBDD() {
		var monOS = (document.getElementById('input_databaseOSl').checked) ? 'Linux' : 'Windows';
		$.post(siteurl + 'ajax/administration/backupbdd', {
			courriel : document.getElementById('input_databaseCourriel').value,
			motpasse : document.getElementById('input_databaseMotPasse').value,
			mysystos : monOS
		}, function(data){
			Verdissons(Array('input_databaseCourriel','input_databaseMotPasse'), data);
			d = data.split('|');
			document.getElementById('span_BackupBDD').innerHTML = d[0];
		});

	}

	function BackupTXT() {
		$.post(siteurl + 'ajax/administration/backuptxt', {
			assigned : 	 	document.getElementById('input_ChxTxt_assigned').value, 	
			attached : 	 	document.getElementById('input_ChxTxt_attached').value,	
			config :  		document.getElementById('input_ChxTxt_config').value,
			comment : 	 	document.getElementById('input_ChxTxt_comment').value, 	
			issue : 	 		document.getElementById('input_ChxTxt_issue').value,		
			issueproject : document.getElementById('input_ChxTxt_issueproject').value,
			project : 	 	document.getElementById('input_ChxTxt_project').value,
			projectdel : 	document.getElementById('input_ChxTxt_projectdel').value,
			projectmod : 	document.getElementById('input_ChxTxt_projectmod').value,
			status : 	 	document.getElementById('input_ChxTxt_status').value,
			tagsADD : 	 	document.getElementById('input_ChxTxt_tagsADD').value,
			tagsOTE : 	 	document.getElementById('input_ChxTxt_tagsOTE').value
		}, function(data){
			Verdissons(Array(), data);
			d = data.split('|');
			document.getElementById('div_divBackupTXT').style.marginLeft = 0;
			document.getElementById('div_divBackupTXT').innerHTML = d[0];
		});
	}
	
	function DatabaseAjour (valeur) {
		$.post(siteurl + 'ajax/administration/AjourDataBase', {
			MAJsql  : valeur,
			comment : "admin"
		}, function(data){
			document.getElementById('span_ajour_' + valeur).innerHTML = "";
		});
	}

	function ChangeonsText(Quel, Question) {
		var texte = CachonsEditor(9);
		var Enreg = (Question == 'OUI') ? true : false;
		if (texte != TexteInital && Enreg == false) { Enreg = confirm(Question); }
		$.post(siteurl + 'ajax/administration/emails', {
			Enreg : 	 Enreg,
			Prec : 	 texte,
			Quel : 	 Affiche, 
			Suiv : 	 Quel,
			Titre : 	 document.getElementById('input_TitreMsg').value
		}, function(data){
			Affiche = Quel;
			Verdissons(Array('input_err_exittxt'),data);
			var recu = data.split('||');
			TexteInital = recu[1];
			ChangeonsEditor(9, TexteInital);
			document.getElementById('input_TitreMsg').value = recu[2];
		});
	}
	
	function Verdissons(champs, data) {
		d = data.split("|");
		msg = d[0];
		coul = (d[1]) ? d[1] : 'black';
		document.getElementById('global-notice').innerHTML = d[0];
		document.getElementById('global-notice').style.backgroundColor = coul; 
		document.getElementById('global-notice').style.color = ((coul=='black') ? 'yellow' : 'black');   
		document.getElementById('global-notice').style.display = 'block';   
		setTimeout(function(){
			document.getElementById('global-notice').style.display = 'none';   
		}, 7500);
		for (x=0; x<champs.length; x++) {
			document.getElementById(champs[x]).style.backgroundColor = 'green';
		}
		var blanc = setTimeout(function() { for (x=0; x<champs.length; x++) { document.getElementById(champs[x]).style.backgroundColor = 'white'; } }, 5000);
	}

	function VerifChamps(champs) {
		var compte = 0;
		for (x=0; x<champs.length; x++) {
			if (document.getElementById(champs[x]).style.backgroundColor == 'red' ) { return false; }
			if (document.getElementById(champs[x]).style.backgroundColor == 'yellow' ) { compte = compte + 1; }
		}
		if (compte == 0) { return false; }
		for (x=0; x<champs.length; x++) {
			document.getElementById(champs[x]).style.backgroundColor = 'red';
		}
		return true;
	}

	var Affiche = "comment";	
	var IntroInital = ""
	var TexteInital = ""
	var TxByeInital = ""
	setTimeout(function() { IntroInital = CachonsEditor(7); } , 1500);
	setTimeout(function() { TxByeInital = CachonsEditor(8); } , 1500);
	setTimeout(function() { TexteInital = CachonsEditor(9); } , 1500);
