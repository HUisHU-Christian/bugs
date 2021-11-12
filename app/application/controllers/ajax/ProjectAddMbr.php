<?php
	$membres = "";
	include_once ("db.php");
	$_GET["User"] = strtolower($_GET["User"]);
	$requUSER  = "INSERT INTO projects_users VALUES (NULL, ".$_GET["User"].", ".$_GET["Projet"].", 2, NOW(), NOW()) ";
	if (Requis($requUSER)) {
		Requis("INSERT INTO following VALUES (NULL, ".$_GET["User"].", ".$_GET["Projet"].", 0, 1, 1, 1) ");
		$QuelPERS = Explose("SELECT id, firstname, UPPER(lastname) AS lastname, role_id FROM users WHERE id = ".$_GET["User"]);
		$membres .= $QuelPERS["firstname"] . ' ' . $QuelPERS["lastname"];

		if ($_GET["CettePage"] == 'page') {
			$resuROLE = Requis("SELECT * FROM roles WHERE id <= ".$_GET["MonRole"]);
			$retour  = $QuelPERS["id"].'|'.$QuelPERS["role_id"].'|';
			$retour .= $QuelPERS["firstname"] . ' ' . $QuelPERS["lastname"];
				while($QuelROLE = Fetche($resuROLE)) { 
					$retour .= '|'.$QuelROLE["id"].'&'.$QuelROLE["name"]; 
				}
			echo $retour;
		} else {
			echo '<li id="project-user'.$_GET["User"].'">'.$membres.'</li>';
		}
	}
?>
