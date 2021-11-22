<?php
$active_projects = Project\User::active_projects();
$MonRole = Project\User::GetRole(Project::current()->id);

if(count($active_projects)>1) {
?>

<div id="sidebar_Projects" class="sidebarItem">

<br />
<div class="menuprojetsgauche">
	<button class="button_menuprojetsgauche">
	<?php echo __('tinyissue.select_a_project'); ?>
	</button>
	<div class="div_menuprojetsgauche">
<?php
	//Récupération des préférences dans le dossier personnel de l'usager
	$Pref = \User::pref();

	//Liste des projets dans un menu déroulant
	////Collecte des informations
	$NbIssues = array();
	$Proj = array();
	$SansAccent = array();
	foreach($active_projects as $row) {
		$NbIssues[$row->to()] = $row->count_open_issues();
		$Proj[$row->to()] = $row->name.' ('.$NbIssues[$row->to()].')';
	}
	////Préparation au tri
	foreach ($Proj as $ind => $val ){
		$SansAccent[$ind] = htmlentities($val, ENT_NOQUOTES, 'utf-8');
		$SansAccent[$ind] = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $SansAccent[$ind]);
		$SansAccent[$ind] = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $SansAccent[$ind]);
		$SansAccent[$ind] = preg_replace('#&[^;]+;#', '', $SansAccent[$ind]);
	}
	////Tri des données du menu déroulant
	asort($SansAccent);

	////Affichage des projets en menu déroulant dans l'espace latéral gauche
	foreach($SansAccent as $ind => $val) {
		$selected = (substr($ind, strrpos($ind, "/")+1) == Project::current()->id) ? 'selected' : '';
		echo '<a href="'.$ind.(($NbIssues[$ind] == 0) ? '' : '/issues?tag_id=1').'" title="'.$Proj[$ind].'" >'.((strlen($Proj[$ind]) < 30 ) ? $Proj[$ind] : substr($Proj[$ind], 0, 27).' ...').'</a>';
	 }
?>
	</div>
</div>
<br /><br />

<?php
	//Recherche terminologique dans les projets et billets
	$ceci = array_keys($_GET);
	$prefixe = (substr($ceci[0], 0, 9) == '/project/' && strpos($ceci[0],'issue') == 0) ? '../' : '../../../';
	$prefixe = (substr($ceci[0], -6) == 'issues') ? '../../' : $prefixe;
	include_once path('public').'app/vendor/searchEngine/index.php'; 
?>

</div>

<?php
}
?>
<div id="sidebar_Issues_title" class="sidebarTitles"><?php echo __('tinyissue.ptickets'); ?></div>
<div id="sidebar_Issues" class="sidebarItem">
<h2>
	<?php if(Auth::user()->permission('project-modify')): ?>
	<a href="<?php echo Project::current()->to('edit'); ?>" class="edit"><?php echo __('tinyissue.edit');?></a>
	<?php endif; ?>

	<span><?php echo HTML::link(Project::current()->to(), Project::current()->name); ?><br />
	<?php echo __('tinyissue.assign_users_and_edit_the_project');?></span>
</h2>

<ul>
	<li><a href="<?php echo Project::current()->to('issues'); ?>?tag_id=1"><?php echo Project::current()->count_open_issues(); ?> <?php echo __('tinyissue.open_issues');?></a></li>
	<li><a href="<?php echo Project::current()->to('issues'); ?>?tag_id=2"><?php echo Project::current()->count_closed_issues(); ?> <?php echo __('tinyissue.closed_issues');?></a></li>
</ul>
</div>

<?php 
//Gestion des droits basée sur le rôle spécifique à un projet
//Selon l'analyse du 13 novembre 2021, il n'est pas néssaire de changer le calcul du droit ci-bas
if (Auth::user()->role_id != 1) { ?>
<div id="sidebar_Users_title" class="sidebarTitles"><?php echo __('tinyissue.assigned_users'); ?></div>
<div id="sidebar_Users" class="sidebarItem">
<h2>
	<?php 
		//echo __('tinyissue.assigned_users');
	?>
	<span><?php echo __('tinyissue.assigned_users_description');?></span>
</h2>


<ul class="sidebar-users" id="sidebar-users">
<?php foreach(Project::current()->users()->get() as $row): ?>
	<li id="project-user<?php echo $row->id; ?>">
		<?php if(Auth::user()->permission('project-modify') && count(Project::current()->users()->get())  > 1): ?>
		<a href="javascript:void(0);" onclick="remove_project_user(<?php echo $row->id; ?>, <?php echo Project::current()->id; ?>, '<?php echo __('tinyissue.projsuppmbre'); ?>', 'sidebar');" class="delete"><?php echo __('tinyissue.remove');?></a>
		<?php endif; ?>
		<?php echo $row->firstname . ' ' . $row->lastname; ?>
	</li>
<?php endforeach; ?>
</ul>

<?php if(Auth::user()->permission('project-modify')): ?>
	<input type="text" id="add-user-project" placeholder="<?php echo __('tinyissue.assign_a_user');?>" onkeyup="if(this.value.length > 2) { propose_project_user(this.value, <?php echo Project::current()->id; ?>, 'sidebar', '<?php echo __('tinyissue.remove'); ?>', '<?php echo __('tinyissue.projsuppmbre'); ?>', <?php echo $MonRole; ?>); }" />
	<div id="projetProsedNamesList">
	</div>
<?php endif; ?>
</div>
<?php } 
	$project_WebLnks = \DB::table('projects_links')->where('id_project', '=', Project::current()->id)->order_by('category','ASC')->get();
	$WebLnk = array();
	foreach($project_WebLnks as $WebLnks) {
		if (trim($WebLnks->desactivated) == '') { $WebLnk[$WebLnks->category] = $WebLnks->link; }
	}
?>

<div id="sidebar_Website_title" class="sidebarTitles"><?php echo (count($WebLnk) > 0 ) ? __('tinyissue.website_title') : ''; ?></div>
<?php
if (count($WebLnk) > 0 ) {
?>
<div id="sidebar_Website" class="sidebarItem">
<h2>
	<?php 
		//echo __('tinyissue.website_title');
	?>
	<span><?php echo __('tinyissue.website_description');?></span>
</h2>
<?php
	echo '<ul>';
	foreach($WebLnk as $categ => $link) {
		echo '<li><a href="'.$link.'" class="links" target="_blank">'.__('tinyissue.website_'.$categ).'</a></li>';
	}
	echo '</ul>';
	echo '</div>';
	echo '<br /><br />'; 
	echo '<br /><br />'; 
}
include_once "application/views/layouts/blocks/wiki.php";
?>



<script type="text/javascript" >
	$('#sidebar_Website_title').click(function() {
	    $('#sidebar_Website').toggle('slow');
	});
	$('#sidebar_Users_title').click(function() {
	    $('#sidebar_Users').toggle('slow');
	});
	$('#sidebar_Issues_title').click(function() {
	    $('#sidebar_Issues').toggle('slow');
	});
	$('#sidebar_Projects_title').click(function() {
	    $('#sidebar_Projects').toggle('slow');
	});
	
function AfficheNomProjet(Quel) {
	document.getElementById('global-notice').style.display = "block";
	document.getElementById('global-notice').innerHTML = "Voici le projet : " + Quel;
}
function CacheNomProjet(Quel) {
	document.getElementById('global-notice').style.display = "none";
	document.getElementById('global-notice').innerHTML = "Voici le projet : " + Quel;
}

</script>