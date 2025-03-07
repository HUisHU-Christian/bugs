$(function(){
   if($('.global-notice').html().length > 0){
   	$('.global-notice').slideDown();
   	setTimeout(function(){  $('.global-notice').slideUp(); }, 7500);
   	$('.global-notice').live('click', function(){ $('.global-notice').slideUp(); });
   }
});

var chronoOnOff = "";
var dureeAffichage = 0;
var delaiSauvegarde = false;
var saving = false;
var Nouv = false;

function AffichonsVieux(url,id) {
	url = url.substr(0, url.indexOf('project'));
	url = url + "app/application/controllers/ajax/comment_viewDeleted.php?Quel=" + id;
	window.open(url,'BUGS: review deleted comment','width=400,height=300,left=300,top=200,toolbar=no,channelmode=no,location=no,location=no,resizable=yes,status=no,titlebar=no',false);
}

function AddTag (Quel,d) {
	if (!Nouv ) { return true; }
	saving_toggle(true);
	var IDcomment = 'comment' + new Date().getTime();
	$.get(siteurl + 'ajax/tags/retag', {
		ProjectID : ProjectID,
		IssueID : IssueID,
		Modif : 'AddOneTag',
		Quel : Quel
	}, function(data){
		var adLi = document.createElement("LI");
		adLi.className = 'comment';
		adLi.id = IDcomment;
		document.getElementById('ul_IssueDiscussion').appendChild(adLi);
		document.getElementById(IDcomment).innerHTML = data;
		saving_toggle(false);
	});
}

function addUserProject(project_id, user, cettepage, tradSupp, projsuppmbre, MonRole) {
	var contenu = new Array();
	$.post(siteurl + 'ajax/project/addUserProject', {
		Projet 	: project_id,
		User 		: user,
		CettePage : cettepage,
		MonRole 	: MonRole
	}, function(data){
			if ( data  != "") {
				if (cettepage == 'page') {
					var c = data;
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
					contenu[2] = data;
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
	});
}

function ChgRoleUser(role_id, project_id, user_id) {
	$.post(siteurl + 'ajax/project/changeRoleUser', {
		user_id : user_id,
		role_id : role_id,
		project_id : project_id
	}, function(data){
		var a = 1;
	});
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
}

function delaiAffichage() {
	dureeAffichage = dureeAffichage + 1;
	if (dureeAffichage > 5) {
		var delaiSauvegarde = false;
		dureeAffichage = 0;
		document.getElementById('global-saving').style.display = 'none';
	}
}

function Follows(Quoi, Qui, ProjectID, IssueID, Etat) {
	$.post(siteurl + 'ajax/project/following', {
		quoi	: Quoi,
		qui	: Qui,
		projet : ProjectID,
		issue : IssueID,
		etat 	: 	Etat
	}, function(data){
		return data;
	});
}

function Issue_ChgListMbre(NumProj) {
	$.post(siteurl + 'ajax/project/issueChgListMbre', {
		projet 	: NumProj
	}, function(data){
		document.getElementById('project_newSelectResp').innerHTML = data;
	});
}

function OteTag(Quel) {
	saving_toggle(true);
	var IDcomment = 'comment' + new Date().getTime();
	$.get(siteurl + 'ajax/tags/retag', {
		ProjectID : ProjectID,
		IssueID : IssueID,
		Modif : 'eraseTag',
		Quel : Quel
	}, function(data){
		var adLi = document.createElement("LI");
		adLi.className = 'comment';
		adLi.id = IDcomment;
		document.getElementById('ul_IssueDiscussion').appendChild(adLi);
		document.getElementById(IDcomment).innerHTML = data;
		saving_toggle(false);
	});
}

function propose_project_user(user, project_id, cettepage, MonRole) {
	var n = new Date();
	var Modif = "false";
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

function Reassignment (Project, Prev, Suiv, Issue) {
	if (saving) { return true; }
	saving_toggle(true);
	var n = new Date();
	var Modif = "false";
	if (n-d > 3000 ) { Modif = "AddOneTag"; }
	var IDcomment = 'comment' + n.getTime();
	$.post(siteurl + 'ajax/project/reassign', {
		Modif		: 'reassign',
		Project 	: Project,
		Prev 		: Prev,
		Suiv 		: Suiv,
		Issue 	: 	Issue
	}, function(data){
		var adLi = document.createElement("LI");
		adLi.className = 'comment';
		adLi.id = IDcomment;
		document.getElementById('ul_IssueDiscussion').appendChild(adLi);
		document.getElementById(IDcomment).innerHTML = data;
		var MyDropDown = document.getElementById('dropdown_ul');
		var items = MyDropDown.getElementsByTagName("li");
		for (var i = 1; i < items.length; ++i) {
			var monID = items[i].getAttribute('id');
			var num = monID.substring(12);
			var contenu = items[i].innerHTML;
			var nomDeb = contenu.indexOf('>',0);
			var nomFin = contenu.indexOf('<', nomDeb);
			var nom = contenu.substring(nomDeb+1,nomFin);
			var contenu = '<a class="user0" href="javascript: Reassignment(' + Project + ', ' + Prev + ', ' + num + ',' + Issue + ');">' + nom + '</a>';
			if (num == Suiv) {
				contenu = '<span style="color: #FFF; margin-left: 10px; font-weight: bold;">' + nom + '</span>';
				document.getElementById('span_currentlyAssigned_name').innerHTML = nom;
			}
			items[i].innerHTML = contenu;
		}
		saving_toggle(false);
	});
}


function remove_project_user(user_id, project_id, projsuppmbre) {
	if(!confirm(projsuppmbre)){ return false; }
	if (saving) { return true; }
	saving_toggle(true);

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
		saving_toggle(false);
	});
}

function saving_toggle(nouvEtat){
	saving = nouvEtat;
	if(saving){
		document.getElementById('global-saving').style.display = "block";
		document.getElementById('global-saving').style.color = "yellow";
		delai = setInterval(function (){delaiAffichage()}, 500);
	}else{
		document.getElementById('global-saving').style.display = "none";
		clearInterval(delai);
	}
	return true;
}

function supprimerIssue(issue_id) {
	$.post(siteurl + 'ajax/project/issueDelete', {
		issue_id : issue_id
	}, function(data){
		document.location.href = '../../issues?tag_id=1';
	});
}

setTimeout(function(){ 
	Nouv = true; 
	}, 3000
);
