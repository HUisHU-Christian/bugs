<?php
	include_once "db.php";
	$sortie = "";
	
	if ($_GET["col"] == 0) {
		$requISSU = Requis("SELECT ISSU.id, ISSU.status, ISSU.title, TODO.weight, PROJ.name, ISSU.project_id
			FROM projects_issues AS ISSU
			LEFT JOIN users_todos AS TODO ON TODO.issue_id = ISSU.id
			LEFT JOIN projects AS PROJ ON PROJ.id = ISSU.project_id  
			WHERE ISSU.status = 0 
				AND ISSU.assigned_to = ".$_GET["user"]." 
			ORDER BY TODO.status DESC, ISSU.updated_at DESC 
			LIMIT ".$_GET["rendu"].", 25 ");
	} else {
		$requISSU = Requis("SELECT ISSU.id, ISSU.status, ISSU.title, TODO.weight, PROJ.name, ISSU.project_id
			FROM projects_issues AS ISSU
			LEFT JOIN users_todos AS TODO ON TODO.issue_id = ISSU.id
			LEFT JOIN projects AS PROJ ON PROJ.id = ISSU.project_id  
			WHERE ISSU.status != 0 
				AND ISSU.assigned_to = ".$_GET["user"]."
				AND TODO.weight >= ".$config['Percent'][$_GET["col"]]." 
				AND TODO.weight < ".$config['Percent'][$_GET["col"]+1]." 
			ORDER BY TODO.status DESC, ISSU.updated_at DESC 
			LIMIT ".$_GET["rendu"].", 25 ");
	}
	while ($lane = mysqli_fetch_object($requISSU)) {
		$sortie .= '<div class="todo-list-item" id="todo-id-'.$lane->id.'" data-issue-id="'.$lane->id.'" draggable="true"  ondrag="dragStart(this.id);" ondragend="dragDrop(this.id);">';
		$sortie .= '	<div class="todo-list-item-inner">';
		$sortie .= '		<span><span class="colstate" style="color: '.$config['PriorityColors'][$lane->status].';" onmouseover="document.getElementById(\'taglev\').style.display = \'block\';" onmouseout="document.getElementById(\'taglev\').style.display = \'none\';">&#9899;</span>#'. $lane->id.'</span>';
		$sortie .= '			<a href="project/' . $lane->project_id . '/issue/' . $lane->id.'">'.$lane->title.'</a>&nbsp;<span>( '.$lane->weight.'%)</span>';
		$sortie .= '			<a class="todo-button del" title="Supprimer" data-issue-id="'.$lane->id.'" href="#">[X]</a>';
		$sortie .= '		<div>'.$lane->name.'</div>';
		$sortie .= '	</div>';
		$sortie .= '</div>
		';
	}
	echo $sortie;