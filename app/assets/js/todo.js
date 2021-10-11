/* draggable element */
var enMVT = "";
var divORIG = "";
var divOVER = "";
var posiX = 0;
var posiY = 0;

function AffichonsAutres(col, rendu) {
	Exactement = Exactement + 'app/application/controllers/ajax/todo_AffichonsAutres.php?col=' + col + '&rendu=' + rendu;
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById('lane-details-' + col).innerHTML = xhttp.responseText;
			document.getElementById('todo-list-span-' + col).innerHTML = (rendu+1) + "-" + (rendu+25);
		}
	}
	xhttp.open("GET", Exactement, true);
	xhttp.send(); 
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
		Exactement = Exactement + 'app/application/controllers/ajax/todo_ChgIssue.php';
		var formdata = new FormData();
		formdata.append("Quoi", 3);
		formdata.append("divORIG", divORIG);
		formdata.append("divOVER", divOVER);
		formdata.append("cetDIV", cetDIV.id);
		formdata.append("userID", usr);

		document.getElementById(divORIG).removeChild(cetDIV);
		document.getElementById(divOVER).appendChild(cetDIV);

		//alert("Nous ferons quelque chose ici avec \ncetID = " + cetID + "\n et la div réceptrice : " + divOVER + "\nOrigine = " + divORIG);
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				//alert(xhttp.responseText);
			}
		}
		xhttp.open("POST", Exactement, true);
		xhttp.send(formdata); 

		//alert(msgFinal);
	}
	document.getElementById(cetID).style.display = "block";
}
