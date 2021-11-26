var enMVT = "";
var divORIG = "";
var divOVER = "";
var posiX = 0;
var posiY = 0;

function AffichonsAutres(col, rendu) {
	$.post(siteurl + 'ajax/todo/AffichonsAutres', {
		user : usr,
		col : col,
		rendu : rendu
	}, function(data){
		document.getElementById('lane-details-' + col).innerHTML = data;
		document.getElementById('todo-list-span-' + col).innerHTML = (rendu+1) + "-" + (rendu+NbIssues);
	});
}

function dragStart(cetID) {
	enMVT = cetID;
	document.getElementById(cetID).style.display = "none";
	document.body.addEventListener("mousemove", (event) => { posiX = event.x ; posiY = event.y; });
}

function dragOver(cetID) {
	if (divORIG == "") { divORIG = cetID; }
	document.getElementById(cetID).style.borderStyle = "dashed";
	document.getElementById(cetID).style.borderColor = "red";
	document.getElementById(cetID).style.borderWidth = "2px";
	divOVER = cetID;
}

function dragLeave(cetID) {
	document.getElementById(cetID).style.borderStyle = "none";
}

function dragDrop(cetID) {
	if (divOVER != divORIG) {
		var cetDIV = document.getElementById(cetID);
		$.post(siteurl + 'ajax/todo/DragDropChgIssue', {
			Quoi 	:	3,
			divORIG :	divORIG,
			divOVER :	divOVER,
			cetDIV 	:	cetDIV.id,
			userID 	:	usr
		}, function(data){
			Verdissons (data);
		});
		document.getElementById(divOVER).appendChild(cetDIV);
		document.getElementById(cetID).style.display = "block";
	}	
}

function Verdissons (data) {
	d = data.split("|");
	msg = d[0];
	coul = (d[1]) ? d[1] : 'black';
	document.getElementById('global-notice').innerHTML = d[0];
	document.getElementById('global-notice').style.backgroundColor = coul; 
	document.getElementById('global-notice').style.color = ((coul=='black') ? 'yellow' : 'black');   
	document.getElementById('global-notice').style.display = 'block';   
	setTimeout(function(){
		document.getElementById('global-notice').style.display = 'none';
	}, 1500);
}