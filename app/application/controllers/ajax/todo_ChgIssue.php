<?php
include_once "db.php";
$_GET["Quoi"] = $_GET["Quoi"] ?? $_POST["Quoi"];
$retour = 0;
if ($_GET["Quoi"] == 1) {
	$tags = $_GET["tags"] ?? 1;
	$attached = $_GET["attached"] ?? 1;
	$retour = 1;
	if ($_GET["Etat"] == 0) {
		$retour = (Requis("INSERT INTO following VALUES(NULL, ".$_GET["Qui"].", ".$_GET["Project"].", ".$_GET["Quel"].", 0, ".$attached.", ".$tags.")")) ? 2 : $retour;
	} else {
		$retour = (Requis("DELETE FROM following WHERE user_id = ".$_GET["Qui"]." AND project_id = ".$_GET["Project"]." AND issue_id = ".$_GET["Quel"]." AND project = 0")) ? 3 : $retour;
	}
}
if ($_GET["Quoi"] == 2) {
	$retour = 4;
	if ($_GET["Etat"] == 0) {
		$retour = (Requis("INSERT INTO following VALUES(NULL, ".$_GET["Qui"].", ".$_GET["Project"].", 0, 1, 0, 0)")) ? 5 : $retour;
	} else {
		$retour = (Requis("DELETE FROM following WHERE user_id = ".$_GET["Qui"]." AND project_id = ".$_GET["Project"]." AND project = 1")) ? 6 : $retour;
	}
}

if ($_GET["Quoi"] == 3) {
	$_GET["Etat"] = "";
	if (!isset($_POST["userID"])) { $retour = "Permission refusée"; }
	$user_id = $_POST["userID"];
	$issue_id = substr($_POST["cetDIV"], 8);
	$requISSU = Requis("SELECT role_id, ISSU.status AS status, ISSU.project_id AS project_id FROM projects_issues AS ISSU LEFT JOIN projects_users AS PUSR ON PUSR.project_id = ISSU.project_id WHERE ISSU.id =".$issue_id);
	$QuelISSU = Fetche($requISSU);
	if ($QuelISSU["role_id"] != 1) { 
		$old_status = intval(substr($_POST["divORIG"],-1));
		$new_status = intval(substr($_POST["divOVER"],-1));
		if ($new_status >= 0 && $new_status <= 3) {
			// Close issue if todo is moved to closed lane. 
			if ($new_status == 0) {
				Requis ("INSERT INTO users_activity (id,user_id,parent_id,item_id,type_id,created_at,updated_at) VALUES (NULL, ".$_POST["userID"].", ".$QuelISSU["project_id"].", ".$issue_id.", 3, NOW(), NOW() ) on duplicate key UPDATE updated_at = NOW()");
				Requis ("UPDATE users_todos SET status = 0, updated_at = NOW() WHERE issue_id = ".$issue_id);
				Requis("UPDATE projects_issues SET status = 0, closed_by = ".$_POST["userID"].", closed_at = NOW() WHERE id = ".$issue_id."");
				$retour = "Fermeture du billet";
			} else {
				$Moyenne = ($config['Percent'][$new_status] + $config['Percent'][$new_status + 1]) / 2;
				Requis ("INSERT INTO users_activity (id,user_id,parent_id,item_id,type_id, created_at,updated_at) VALUES (NULL, ".$_POST["userID"].", ".$QuelISSU["project_id"].", ".$issue_id.", 10, NOW(), NOW() ) on duplicate key UPDATE updated_at = NOW()");
				Requis ("UPDATE users_todos SET status = ".(($QuelISSU["status"] == 0) ? 4 : $QuelISSU["status"] ).", weight = ".$Moyenne.", updated_at = NOW() WHERE issue_id = ".$issue_id);
				Requis ("UPDATE projects_issues SET closed_by = NULL, closed_at = NULL, status = ".(($QuelISSU["status"] == 0) ? 4 : $QuelISSU["status"] ).", weight = ".$Moyenne.", updated_at = NOW() WHERE id = ".$issue_id);
				$retour = "Billet modifié";
			}
//		} else {
//			return array(
//			'success' => FALSE,
//			'errors' => __('tinyissue.todos_err_update'),
//			);
//			$retour = "Modification du billet";
		}
	}
}
echo $retour.' car nous avons reçu : '.$_GET["Etat"];
?>
