<?php if(Auth::user()->permission('project-create')): ?>
<div id="sidebar_MenuDefault_renouveau" style="position: relative; width: 100%; padding-bottom: 10px; padding-top: 8px;">
<div id="sidebar_MenuDefault_nouveau" class="sidebarTitles" style="position: relative;"><?php echo __('tinyissue.create_project'); ?></div>
	<h2 style="position: absolute; top: 5px; text-align: right; width: 100%;">
	<a href="<?php echo URL::to('projects/new'); ?>" class="add" title="New Project" style="margin-top: -18px;"><?php __('tinyissue.new'); ?></a>
	</h2>
	</div>
	<br /><br />
<?php endif; ?>

<div id="sidebar_MenuDefault_title" class="sidebarTitles"><?php echo __('tinyissue.active_projects'); ?></div>
<span><?php echo __('tinyissue.active_projects_description');?></span>
<div id="sidebar_MenuDefault" class="sidebarItem">
<br />
<div class="menuprojetsgauche">
	<button class="button_menuprojetsgauche">
	<?php echo __('tinyissue.select_a_project'); ?>
	</button>
	<div class="div_menuprojetsgauche">
<?php
	//Récupération des préférences dans le dossier personnel de l'usager
	$Preferences = \Auth::user()->pref();

	//Liste des projets dans un menu déroulant
	////Collecte des informations
	$active_projects = Project\User::active_projects();
	$NbIssues = array();
	$Proj = array();
	$SansAccent = array();
	foreach($active_projects as $row) {
		$NbIssues[$row->to()] = $row->count_open_issues();
		$Proj[$row->to()] = $row->name.' ('.$row->count_open_issues().'/'.$row->count_closed_issues().')';
	}
	////Préparation au tri
	foreach ($Proj as $ind => $val ){
		$SansAccent[$ind] = htmlentities($val, ENT_NOQUOTES, 'utf-8');
		$SansAccent[$ind] = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $SansAccent[$ind]);
		$SansAccent[$ind] = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $SansAccent[$ind]);
		$SansAccent[$ind] = preg_replace('#&[^;]+;#', '', $SansAccent[$ind]);
	}
	////Tri des données du menu déroulant
	if ($Preferences['orderSidebar'] == 'desc') { asort($SansAccent); } else { arsort($SansAccent); }

	////Affichage du menu déroulant dans l'espace latéral gauche
	foreach($SansAccent as $ind => $val) {
		$selected = '';
		echo '<a href="'.$ind.(($NbIssues[$ind] == 0) ? '' : '/issues?tag_id=1').'" title="'.$Proj[$ind].'" >'.((strlen($Proj[$ind]) < 30 ) ? $Proj[$ind] : substr($Proj[$ind], 0, 27).' ...').'</a>';
	 }
?>
	</div>
</div>
<br /><br />
<div style="max-height: 600px; overflow-y: auto;">
<?php
	//Les préférences de l'usager ont été récupérées plus haut
	if ($Preferences['numSidebar'] != 0) {
		echo '<ul>';
		$NbIssues = array();
		$Proj = array();
		$SansAccent = array();
		foreach(Project\User::active_projects() as $row) {
			$NbIssues[$row->to()] = $row->count_open_issues();
			$Proj[$row->to()] = $row->name.'&nbsp;<span class="info-open-issues" title="'.$row->count_open_issues().' '.__('tinyissue.open_issues').' & '.$row->count_closed_issues().' '.__('tinyissue.reports_inactissues').'">(<b>'.$row->count_open_issues().'</b>/'.$row->count_closed_issues().')</span>';
			$idProj[$row->to()] = $row->id;
		}
		foreach ($Proj as $ind => $val ){
			$SansAccent[$ind] = htmlentities($val, ENT_NOQUOTES, 'utf-8');
			$SansAccent[$ind] = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $SansAccent[$ind]);
			$SansAccent[$ind] = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $SansAccent[$ind]);
			$SansAccent[$ind] = preg_replace('#&[^;]+;#', '', $SansAccent[$ind]);
		}
		
		////Tri des données affichées dans le panneau de gauche
		if ($Preferences['orderSidebar'] == 'asc') { asort($SansAccent); } else { arsort($SansAccent); }

		//Affichage dans le panneau de gauche
		$rendu = 0;
		foreach($SansAccent as $ind => $val) {
			$id = $idProj[$ind];
			$follower = \DB::table('following')->where('project','=',1)->where('project_id','=',$id)->where('user_id','=',\Auth::user()->id)->count();
			$follower = ($follower > 0) ? 1 : 0;
			echo '<a href="javascript: Following('.$follower.', '.$id.', '.\Auth::user()->id.');" title="'.(($follower == 0) ? __('tinyissue.following_start') : __('tinyissue.following_stop')).'" ><img id="img_follow_'.$id.'" src="'.\URL::home().'app/assets/images/layout/icon-comments_'.$follower.'.png" align="left" style="min-height:'.$follower.'px " /></a>';
			echo '<li>';
			echo '<a href="'.$ind.(($NbIssues[$ind] == 0) ? '' : '/issues?tag_id=1').'">'.$Proj[$ind].' </a>';
			echo '</li>';
			if (++$rendu > abs(intval($Preferences['numSidebar'])) && abs(intval($Preferences['numSidebar'])) < 990) { break; }
		}
		echo '</ul>';
	}
?>
</div>
<?php
	$ceci = array_keys($_GET);
	$prefixe = isset($ceci[0]) ? (in_array($ceci[0], array("/administration/users","/projects/reports","/user/settings","/user/issues","/project/5"))) ? "../" : "" : "";
	include_once path('public').'app/vendor/searchEngine/index.php';
	echo '<br /><br />'; 
	echo '<br /><br />'; 
	include_once "application/views/layouts/blocks/wiki.php";
?>
</div>

<script type="text/javascript" >
	$('#sidebar_MenuDefault_title').click(function() {
	    $('#sidebar_MenuDefault').toggle('slow');
	});
	
	function Following(etat, Project, Qui) {
		var xhttp = new XMLHttpRequest();
		etat = document.getElementById('img_follow_' + Project).style.minHeight.substr(0,1);
		var NextPage = '<?php echo \URL::home(); ?>app/application/controllers/ajax/Following.php?Quoi=2&Qui=' + Qui + '&Project=' + Project + '&Etat=' + etat;
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				if (xhttp.responseText != '' ) {
				//alert(xhttp.responseText);
				etat = Math.abs(etat-1);
				document.getElementById('img_follow_' + Project).src = "<?php echo \URL::home(); ?>app/assets/images/layout/icon-comments_" + etat + ".png";
				document.getElementById('img_follow_' + Project).style.minHeight = etat+"px";
				}
			}
		};
		xhttp.open("GET", NextPage, true);
		xhttp.send(); 
	}
</script>