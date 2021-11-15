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

		var xhttp = new XMLHttpRequest();
		var formdata = new FormData();
		formdata.append("fName", document.getElementById('input_email_from_name').value);
		formdata.append("fMail", document.getElementById('input_email_from_email').value);
		formdata.append("rName", document.getElementById('input_email_replyto_name').value);
		formdata.append("rMail", document.getElementById('input_email_replyto_email').value);
		formdata.append("intro", document.getElementById('input_email_replyto_email').value);
		formdata.append("intro", intro);
		formdata.append("bye", bye);
		var NextPage = 'app/application/controllers/ajax/ChgConfEmail.php';
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (xhttp.responseText != '' ) {
					//alert(xhttp.responseText);
					IntroInital = intro; 
					TxByeInital = bye;
					Verdissons(champs,xhttp.responseText);
				}
			}
		};
		xhttp.open("POST", NextPage, true);
		xhttp.send(formdata); 
	}
	
	function AppliquerPrefGen() {
		champs = new Array('input_coula','input_coulb','input_coulc','input_could','input_coule','input_coulo','input_duree','input_prog','input_test','input_TodoNbItems','input_TempsFait');
		if (!VerifChamps(champs)) { return false; }

		var xhttp = new XMLHttpRequest();
		var formdata = new FormData();
		for(x=0; x<champs.length; x++) {
			formdata.append(champs[x].substr(6) , document.getElementById(champs[x]).value);
		}
		var NextPage = 'app/application/controllers/ajax/ChgPrefGen.php';
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (xhttp.responseText != '' ) {
					//alert(xhttp.responseText);
					Verdissons(champs,xhttp.responseText);
				}
			}
		};
		xhttp.open("POST", NextPage, true);
		xhttp.send(formdata); 
	}
	
	function AppliquerServeur() {
		champs = new Array('input_email_encoding','input_email_linelenght','input_email_server','input_email_port','input_email_encryption','input_email_username','input_email_password','select_Email_transport','select_Email_plainHTML','input_email_mailerrormsg');
		if (!VerifChamps(champs)) { return false; }
		var xhttp = new XMLHttpRequest();
		var formdata = new FormData();
		formdata.append("transport", document.getElementById('select_Email_transport').value);
		formdata.append("plainHTML", document.getElementById('select_Email_plainHTML').value);
		formdata.append("encoding", document.getElementById('input_email_encoding').value);
		formdata.append("linelenght", document.getElementById('input_email_linelenght').value);
		formdata.append("server", document.getElementById('input_email_server').value);
		formdata.append("port", document.getElementById('input_email_port').value);
		formdata.append("encryption", document.getElementById('input_email_encryption').value);
		formdata.append("username", document.getElementById('input_email_username').value);
		formdata.append("password", document.getElementById('input_email_password').value);
		formdata.append("mailerrormsg", document.getElementById('input_email_mailerrormsg').value);
		var NextPage = 'app/application/controllers/ajax/ChgConfEmail_Server.php';
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (xhttp.responseText != '' ) {
					//alert(xhttp.responseText);
					Verdissons(champs,xhttp.responseText);
				}
			}
		};
		xhttp.open("POST", NextPage, true);
		xhttp.send(formdata); 
	}

	function AppliquerErr() {
		alert("Nous sommes rendus ici. \nIl est assez tard, je vais me coucher");
		$.post(siteurl + 'ajax/administration/errors', {
			user_id : document.getElementById('input_err_detail').value,
			user_id : document.getElementById('input_err_log').value,
			user_id : document.getElementById('input_err_exit').value,
			user_id : document.getElementById('input_err_exittxt').value
		}, function(data){
			var a = 1;
		});
	
		return true;
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
	}
	
	function BackupBDD() {
		monOS = (document.getElementById('input_databaseOSl').checked) ? 'Linux' : 'Windows';
		var formdata = new FormData();
		formdata.append("Courriel", document.getElementById('input_databaseCourriel').value );
		formdata.append("MotPasse", document.getElementById('input_databaseMotPasse').value );
		formdata.append('OS', monOS );		
		var xhttp = new XMLHttpRequest();
		var NextPage = 'app/application/controllers/ajax/Sauvegarde_BDD.php';
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
					document.getElementById('span_BackupBDD').innerHTML = xhttp.responseText;
				if (xhttp.responseText == 'Échec') {
					document.getElementById('span_BackupBDD').innerHTML = "<font color=\"red\">Vérifiez vos configurations, nous n`avons pas pu enregistrer</font>";
				} else if (xhttp.responseText == 'Non') {
					document.getElementById('span_BackupBDD').innerHTML = "Vous ne disposez pas de droits nécessaires";
				} else {
					document.getElementById('span_BackupBDD').innerHTML = xhttp.responseText;
				}
			}
		};
		xhttp.open("POST", NextPage, true);
		xhttp.send(formdata); 
	}

	function BackupTXT() {
		var formdata = new FormData();
		formdata.append('assigned', document.getElementById('input_ChxTxt_assigned').value ); 	
		formdata.append('attached', document.getElementById('input_ChxTxt_attached').value );	
		formdata.append("config", document.getElementById('input_ChxTxt_config').value);
		formdata.append('comment', document.getElementById('input_ChxTxt_comment').value ); 	
		formdata.append('issue', document.getElementById('input_ChxTxt_issue').value );		
		formdata.append('issueproject', document.getElementById('input_ChxTxt_issueproject').value );
		formdata.append('OS', document.getElementsByName('OS').value );		
		formdata.append('project', document.getElementById('input_ChxTxt_project').value );
		formdata.append('projectdel', document.getElementById('input_ChxTxt_projectdel').value );
		formdata.append('projectmod', document.getElementById('input_ChxTxt_projectmod').value );
		formdata.append('status', document.getElementById('input_ChxTxt_status').value );
		formdata.append('tagsADD', document.getElementById('input_ChxTxt_tagsADD').value );
		formdata.append('tagsOTE', document.getElementById('input_ChxTxt_tagsOTE').value );
		var xhttp = new XMLHttpRequest();
		var NextPage = 'app/application/controllers/ajax/Sauvegarde_TXT.php';
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (xhttp.responseText == 'Non') {
					alert("Aucune copie réussie.");
				} else {
					document.getElementById('div_divBackupTXT').innerHTML = xhttp.responseText;
				}
			}
		};
		xhttp.open("POST", NextPage, true);
		xhttp.send(formdata); 
	}

	function ChangeonsText(Quel, Langue, Question) {
		var texte = CachonsEditor(9);
		var Enreg = (Question == 'OUI') ? true : false;
		if (texte != TexteInital && Enreg == false) { Enreg = confirm(Question); }
		var formdata = new FormData();
		formdata.append("Enreg", Enreg);
		formdata.append("Lang", Langue);
		formdata.append("Prec", texte);
		formdata.append("Quel", Affiche); 
		formdata.append("Suiv", Quel);
		formdata.append("Titre", document.getElementById('input_TitreMsg').value);
		var xhttp = new XMLHttpRequest();
		var NextPage = 'app/application/controllers/ajax/ChgConfEmail_Textes.php';
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (xhttp.responseText != '' ) {
					Affiche = Quel;
					if (Question == 'OUI') { 
						document.getElementById('global-notice').innerHTML = 'Modification apportée avec succès.  /  Successfully updated.';
						document.getElementById('global-notice').style.display = 'block';   
						setTimeout(function(){
							document.getElementById('global-notice').style.display = 'none';   
						}, 7500); 
					}
					var r = xhttp.responseText;
					var recu = r.split('||');
					TexteInital = recu[0];
					ChangeonsEditor(9, TexteInital);
					document.getElementById('input_TitreMsg').value = recu[1];
				}
			}
		};
		xhttp.open("POST", NextPage, true);
		xhttp.send(formdata); 
	}
	
	function Verdissons(champs,msg) {
		document.getElementById('global-notice').innerHTML = msg;
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

	var Affiche = "attached";	
	var IntroInital = ""
	var TexteInital = ""
	var TxByeInital = ""
	setTimeout(function() { IntroInital = CachonsEditor(7); } , 1500);
	setTimeout(function() { TxByeInital = CachonsEditor(8); } , 1500);
	setTimeout(function() { TexteInital = CachonsEditor(9); } , 1500);
