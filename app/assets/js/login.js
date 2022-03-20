function MotPasseOublie(etat) {
	if (etat == false) { document.location.href="index.php"; }
	document.getElementById('password').setAttribute('draggable', true);
	document.body.style.backgroundImage = "none";
	document.body.style.backgroundColor = "black";
	document.getElementById('div_MotPasseOublie').style.display = "block";
}

function dragFinisssons(Quel) {
  event.preventDefault();
	if (Quel == 'poubelle' && dragon == 'password') {
		document.getElementById('img_MotPasseOubliePoubelle').src = "app/assets/css/images/poubelle_pleine.png";
		document.getElementById('tr_form_password').style.display = "none"; 
		document.getElementById('tr_form_rappeler').style.display = "none";
		document.getElementById('div_ChxLng').style.display = "none";
		document.getElementById('password').value = "RechMotPasse";
		if (document.getElementById('input_Email').value == "") { 
//			let person = prompt("<?php echo $OublieCour[$lng]; ?>", "");
			document.getElementById('input_Email').value = person;
//			if (perso == '') { resu = "<?php echo $OublieQuoi[$lng]; ?> "; }
		}
		document.getElementById('span_MotPasseOublie').innerHTML = resu + document.getElementById('input_Email').value;
		setTimeout(function(){ document.location.href="index.php"; }, 10000); 		
	}
	dragon = "";
}


alert("Nous sommes au bas du fichier login.js");