$(function(){
   if($('.global-notice').html().length > 0){
   	$('.global-notice').slideDown();
   	setTimeout(function(){  $('.global-notice').slideUp(); }, 7500);
   	$('.global-notice').live('click', function(){ $('.global-notice').slideUp(); });
   }
});

var chronoOnOff = "";
var saving = false;

function AffichonsVieux(url,id) {
	url = url.substr(0, url.indexOf('project'));
	url = url + "app/application/controllers/ajax/comment_viewDeleted.php?Quel=" + id;
	window.open(url,'BUGS: review deleted comment','width=400,height=300,left=300,top=200,toolbar=no,channelmode=no,location=no,location=no,resizable=yes,status=no,titlebar=no',false);
}

function addUserProject(project_id, user, cettepage, tradSupp, projsuppmbre, MonRole) {
	var contenu = new Array();
	var Exactement = siteurl + "app/application/controllers/ajax/ProjectAddMbr.php";
	Exactement = Exactement + "?Projet=" + project_id;
	Exactement = Exactement + "&User=" + user;
	Exactement = Exactement + "&CettePage=" + cettepage;
	Exactement = Exactement + "&tradSupp=" + tradSupp;
	Exactement = Exactement + "&MonRole=" + MonRole;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if ( this.responseText  != "") {
				if (cettepage == 'page') {
					var c = this.responseText;
					contenu = c.split("|");
					var detail = "";
					var texte = "";
					var NouvLigne = document.createElement("tr");
					NouvLigne.id = "project-user_" + user;
						var NouvCol = document.createElement("td");
						var ceci = document.createTextNode(contenu[2]);
						NouvCol.appendChild(ceci);
						NouvCol.class = "project-user";
						NouvCol.width = "60%";
						NouvLigne.appendChild(NouvCol);
						
						NouvCol = document.createElement("td");
						NouvCol.class = "project-user";
						NouvCol.width = "20%";
						var NouvSel = document.createElement("select");
						for (x=3; x<contenu.length; x++) {
							detail = contenu[x].split("&");
							ceci = document.createElement("option");
							ceci.value = detail[0];
							texte = document.createTextNode(detail[1]);
							ceci.appendChild(texte);
							NouvSel.appendChild(ceci);
						}
						NouvSel.value = MonRole;
						NouvSel.addEventListener("change", function () { ChgRoleUser("this.value", project_id, contenu[0]); } );
						NouvCol.appendChild(NouvSel);
						NouvLigne.appendChild(NouvCol);
					
						NouvCol = document.createElement("td");
						NouvCol.class = "project-user";
						NouvCol.width = "10%";
						var NouvInput = document.createElement("input");
						NouvInput.type = "checkbox";
						NouvInput.value = 1;
						NouvInput.checked = true;
						NouvInput.id = "input_user_" + contenu[0];
						NouvInput.ckecked = "checked";
						NouvInput.addEventListener("click", function () { Following("this.checked", project_id, contenu[0]); } );
						NouvCol.appendChild(NouvInput);
						NouvLigne.appendChild(NouvCol);
					
						NouvCol = document.createElement("td");
						NouvCol.class = "project-user";
						NouvCol.width = "10%";
						var NouvInput = document.createElement("a");
						NouvInput.href = "javascript:void(0);";
						NouvInput.addEventListener("click", function () { remove_project_user(contenu[0], project_id, projsuppmbre); } );
						NouvInput.class = "delete";
						texte = document.createTextNode(tradSupp);
						NouvInput.appendChild(texte)
						NouvCol.appendChild(NouvInput);
						NouvLigne.appendChild(NouvCol);
					
					document.getElementById("table_ListUsers").appendChild(NouvLigne);
					document.getElementById('projetProsedNamesPage').innerHTML = "";
					document.getElementById('input_rechNom').value = "";
				} else {
					contenu[2] = this.responseText;
					contenu[1] = contenu[2].substring(contenu[2].indexOf(">")+1);
					contenu[0] = contenu[1].substring(0, contenu[1].indexOf("<"));
					var user = Math.round(Math.random()*999);
					if (document.getElementById("table_ListUsers")) {
						var NouvLigne = document.createElement("tr");
						NouvLigne.id = "project-user_" + user;
							var NouvCol = document.createElement("td");
							var ceci = document.createTextNode(contenu[0]);
							NouvCol.appendChild(ceci);
							NouvCol.class = "project-user";
							NouvCol.width = "60%";
							NouvLigne.appendChild(NouvCol);
							document.getElementById("table_ListUsers").appendChild(NouvLigne);
					}
				}
				if (document.getElementById('projetProsedNamesList')) { document.getElementById('projetProsedNamesList').innerHTML = ""; }
				if (document.getElementById('add-user-project')) 		{ document.getElementById('add-user-project').innerHTML = ""; }
				if (document.getElementById('sidebar-users')) 			{ document.getElementById('sidebar-users').innerHTML = document.getElementById('sidebar-users').innerHTML + '<li id="project-user' + user + '">' + contenu[2] + '</li>'; }
			}
		}
	};
	xhttp.open("GET", Exactement, true);
	xhttp.send(); 
}

function ChgRoleUser(role_id, project_id, user_id) {
	$.post(siteurl + 'ajax/project/changeRoleUser', {
		user_id : user_id,
		role_id : role_id,
		project_id : project_id
	}, function(data){
		var a = 1;
	});

	return true;
}

function Chronometrons(etat, nouvText, user_id, issue_id, project_id) {
	etat = (chronoOnOff == "") ? etat : chronoOnOff;
	var contenu = "";
	var nouvEtat = 'off';
	if (etat == 'off') {
		contenu = prompt("Veuillez décrire le travail effectué","");
		nouvEtat = 'on';
	}
		$.post(siteurl + 'ajax/project/chronometrons', {
			etat : etat,
			comment : contenu,
			issue_id : issue_id,
			project_id : project_id
	}, function(data){
		var a = data;
	});

	document.getElementById('input_chrono').value = nouvText;
	document.getElementById('input_chrono').classList.remove("chrono_" + etat);
	document.getElementById('input_chrono').classList.add("chrono_" + nouvEtat);
	chronoOnOff = nouvEtat;

	return true;
}

function Issue_ChgListMbre(NumProj) {
	var Exactement = siteurl + "app/application/controllers/ajax/ListMbr.php"
	Exactement = Exactement + "?Projet=" + NumProj;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
			document.getElementById('project_newSelectResp').innerHTML = this.responseText;
	    }
	};
	xhttp.open("GET", Exactement, true);
	xhttp.send(); 
}

function propose_project_user(user, project_id, cettepage, MonRole) {
	$.post(siteurl + 'ajax/project/proposeProjectUser', {
		user		: user,
		projet 	: project_id,
		cettePage : cettepage,
		monRole 	: 	MonRole
	}, function(data){
		if (cettepage == 'sidebar') {
			document.getElementById('projetProsedNamesList').innerHTML = data;
		} else if (cettepage == 'page') {
			document.getElementById('projetProsedNamesPage').innerHTML = data;
		}
	});
}

function remove_project_user(user_id, project_id, projsuppmbre) {
	if(!confirm(projsuppmbre)){ return false; }
//	saving_toggle();

	$.post(siteurl + 'ajax/project/remove_user', {
		user_id : user_id,
		project_id : project_id
	}, function(data){
		var poubelle = "";
		var sonpere = "";
		if (document.getElementById("table_ListUsers")) {
			poubelle = document.getElementById('project-user_' + user_id);
			sonpere = poubelle.parentNode;
			if(!sonpere.removeChild(poubelle)) {
				document.getElementById("table_ListUsers").removeChild(poubelle);
			}
		}
		if (document.getElementById('sidebar-users')) {
			poubelle = document.getElementById('project-user' + user_id);
			document.getElementById("sidebar-users").removeChild(poubelle);
		}
//		$('#project-user' + user_id).fadeOut();
//		saving_toggle();
	});

//	return true;
}

function saving_toggle(){
	if(saving){
		$('.global-saving').hide();
		saving = false;
	}else{
		$('.global-saving').show();
		saving = true;
	}
}

