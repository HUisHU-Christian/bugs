<?php
	$colonnes = array(50,60, 20, 20, 20,20);
	$colorStatus[0] = array(170,170,170);
	$colorStatus[1] = array(215,215,225);
	$ChampDTE = "TIK.start_at";
	$ChampUSR = "TIK.assignee";
	$OrdreTRI = "PRO.name ASC, TIK.start_at ASC, TIK.closed_at ASC";
	$PosiX = array('inactif0' => 151.75);
	$etOU = " AND ";
	$query  = "SELECT ";
	//Projet
	$query .= "CONCAT (PRO.id, '. ',PRO.name) AS zero, ";
	//BIllet
	$query .= "CONCAT (TIK.id, '. ', TIK.title) AS prem, ";
	//Heures de la soumission
	$query .= "TIK.temps_plan as deux, ";
	//Heures faites
	$query .= "(SELECT SUM(temps_fait) FROM projects_issues_comments AS COMM WHERE COMM.issue_id = TIK.id GROUP BY COMM.issue_id) AS troi, ";
	//Heures facturées
	$query .= "TIK.temps_fact  AS quat, ";
	//Heures payées
	$query .= "TIK.temps_paye AS cinq, ";
	//$query .= "(SELECT SUM(temps_fait) FROM projects_issues_comments AS COMM WHERE COMM.issue_id = TIK.id GROUP BY COMM.issue_id) AS inactif0, ";
	$query .= "'' AS inactif0, ";
	$query .= "TIK.status AS status ";
	$query .= "FROM projects_issues AS TIK ";
	$query .= "LEFT JOIN projects AS PRO ON PRO.id = TIK.project_id ";
	$query .= "LEFT JOIN users AS USR ON USR.id = TIK.assigned_to ";
	$query .= "WHERE TIK.temps_paye != TIK.temps_fact ";
//	$query .= "WHERE PRO.status = 1 ";

	$SautPage = true;