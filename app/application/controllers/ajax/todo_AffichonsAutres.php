<?php
	include_once "db.php";
	$sortie = "";
	
	if ($_GET["col"] == 0) {
		$requISSU = Requis("SELECT ISSU.id, ISSU.status, ISSU.title, TODO.weight, PROJ.name, ISSU.project_id
			FROM projects_issues AS ISSU
			LEFT JOIN users_todos AS TODO ON TODO.issue_id = ISSU.id
			LEFT JOIN projects AS PROJ ON PROJ.id = ISSU.project_id  
			WHERE ISSU.status = 0 
				AND ISSU.assigned_to = 1 
			ORDER BY TODO.status DESC, ISSU.updated_at DESC 
			LIMIT ".$_GET["rendu"].", 25 ");
	} else {
		$result = \DB::table('projects_issues')
									->join('users_todos', 'users_todos.issue_id', '=', 'projects_issues.id')
									->join('projects', 'projects.id', '=', 'projects_issues.project_id')
									->where('assigned_to', '=', Auth::user()->id)
									->where('users_todos.weight', '>=', $bas)
									->where('users_todos.weight', '<', $haut)
									->where('projects_issues.status', $zero, 0)
									->order_by('projects_issues.status', 'DESC')
									->order_by('projects_issues.updated_at', 'DESC')
									->get(['projects_issues.id','projects_issues.status','projects_issues.title','users_todos.weight','projects.name', 'projects_issues.project_id']);
 		return $result;
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
