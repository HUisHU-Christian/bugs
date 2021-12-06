<?php
	$colonnes = array(40, 17, 70, 17, 50, 0.1);
	$colorStatus[0] = array(170,170,170);
	$colorStatus[1] = array(215,215,225);
	$ChampDTE = "HIST.created_at";
	$ChampUSR = "HIST.user_id";
	$Groupage = "HIST.parent_id, type_id, HIST.user_id ";
	$OrdreTRI = "PROJ.name ASC, ACTI.description ASC, PERS.firstname ASC, PERS.lastname ASC ";
	$PosiX = array('inactif0' => 151.75);
	$etOU = " AND ";

	$query  = "SELECT ";
	$query .= "PROJ.name AS zero, ";
	$query .= "COUNT(type_id)   AS prem, ";
	$query .= "ACTI.FR AS deux, ";
	$query .= "COUNT(item_id)   AS troi,";
	$query .= "MIN(item_id) AS PremBillet, ";
	$query .= "MAX(item_id) AS DernBillet, ";
	$query .= "CONCAT(PERS.firstname, ' ', UPPER(PERS.lastname)) AS quat, ";
	$query .= "IF(MIN(HIST.action_id) IS NULL, '' ,MIN(HIST.action_id)) AS BilletMINcomm, ";
	$query .= "IF(MAX(HIST.action_id) IS NULL, '', MAX(HIST.action_id)) AS BilletMAXcomm, ";
	$query .= "MAX(HIST.created_at) AS Derniere, ";
	$query .= "ACTI.description, ";
	$query .= "'' AS cinq, ";
	$query .= "1 as status ";
	$query .= "FROM users_activity AS HIST ";
	$query .= "LEFT JOIN activity AS ACTI ON ACTI.id = HIST.type_id ";
	$query .= "LEFT JOIN projects AS PROJ ON PROJ.id = HIST.parent_id ";
	$query .= "LEFT JOIN users AS PERS ON PERS.id = HIST.user_id ";
	$query .= "WHERE HIST.parent_id IS NOT NULL AND PROJ.name IS NOT NULL ";
	//$query .= "GROUP BY  HIST.parent_id, type_id, HIST.user_id";
	//$query .= "ORDER BY PROJ.name ASC, ACTI.description ASC, PERS.firstname ASC, PERS.lastname ASC";

	$SautPage = true;