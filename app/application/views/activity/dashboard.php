<?php
	$SansAccent = array();
	foreach(Auth::user()->dashboard() as $project) {
		if(!$project['activity']) continue;

		$id = $project['project']->attributes['id'];
		$NomProjet[$id] = $project['project']->name;
		$SansAccent[$id] = htmlentities($NomProjet[$id], ENT_NOQUOTES, 'utf-8');
		$SansAccent[$id] = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $SansAccent[$id]);
		$SansAccent[$id] = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $SansAccent[$id]);
		$SansAccent[$id] = preg_replace('#&[^;]+;#', '', $SansAccent[$id]);

		foreach($project['activity'] as $activity) {
			$actiProj[$id][] =  $activity;
		}
		asort($SansAccent);
	}
	if (count($SansAccent) == 0 && Auth::user()->role_id == 4) {
		$prefixe = "../";
		$Lng = require_once($prefixe."app/application/language/en/install.php"); 
		if ( file_exists($prefixe."app/application/language/".\Auth::user()->language."/install.php") && \Auth::user()->language != 'en') {
			$LnT = require_once ($prefixe."app/application/language/".\Auth::user()->language."/install.php");
			$LngSRV = array_merge($Lng, $LnT);
		} else {
			$LngSRV = $Lng;
		}
		echo '<h3 style="background-color: yellow; font-size: 200%; color: black; padding-top: 100px;">'.$LngSRV["welcome_1"].'<span style="color:black;">'.$LngSRV["welcome_2"].'</span></h3>
				<div class="pad">
				<form action="'.URL::to('home/new').'" method="post" id="agissons">
				<h3>'.__('tinyissue.create_a_new_project').'</h3>'.
				$LngSRV['welcome_projectname'].' : <input name="projectName" size="80" style="font-size: 200%;"><br />
				<br /><br />
				<h3>'.__('tinyissue.create_a_new_issue').'</h3>'.
				$LngSRV['welcome_issuename'].' : <input name="ticketName" size="80" style="font-size: 150%;"><br />
				<br /><br />'.
				$LngSRV['welcome_issuedesc'].' <br /> <textarea name="body" id="texteara_body" style="width: 98%; height: 150px; background-color: #FFF; color: #000; border-width: 2px; border-color: #999;"></textarea>
				<br />
				<br /><br />
				<div style="text-align: center;"><input type="submit" value="'.$LngSRV['welcome_submit'].'" class="button	primary"/></div>
				</form>
				';
?>
<script type="text/javascript" >
var AllEditors = new Array();
function showckeditor (Quel, id) {
	CKEDITOR.config.entities = false;
	CKEDITOR.config.entities_latin = false;
	CKEDITOR.config.htmlEncodeOutput = false;

	AllEditors[id] = CKEDITOR.replace( Quel, {
		language: '<?php echo \Auth::user()->language; ?>',
		height: 175,
		toolbar : [
			{ name: 'Fichiers', items: ['Source']},
			{ name: 'CopieColle', items: ['Cut','Copy','Paste','PasteText','PasteFromWord','RemoveFormat']},
			{ name: 'FaireDefaire', items: ['Undo','Redo','-','Find','Replace','-','SelectAll']},
			{ name: 'Polices', items: ['Bold','Italic','Underline','TextColor']},
			{ name: 'ListeDec', items: ['horizontalrule','table','JustifyLeft','JustifyCenter','JustifyRight','Outdent','Indent','Blockquote']},
			{ name: 'Liens', items: ['Image', 'NumberedList','BulletedList','-','Link','Unlink']}
		]
	} );
}
setTimeout(function() { showckeditor ('body', 0);} , 567);
</script>
<?php
	} else {
		
		echo '<h3>'.__('tinyissue.dashboard').'<span>'.__('tinyissue.dashboard_description').'</span></h3>';

		echo '<div class="pad">';
		foreach ($SansAccent as $id => $name) {
			echo '<div class="blue-box">';
			echo '	<div class="inside-pad">';
			echo '		<h4>';
			echo '			<a href="project/'.$id.'">'.$NomProjet[$id].'</a>';
			echo '		</h4>';
			echo '		<ul class="activity">';
			foreach($actiProj[$id] as $activity) { echo $activity; }
			echo '		</ul>';
			echo '		<a href="project/'.$id.'">'.$NomProjet[$id].'</a>';
			echo '	</div>';
			echo '</div>';
		}
	}
?>
</div>
