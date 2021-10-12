<h3>
	<?php echo __('tinyissue.dashboard'); ?>
	<span>
		<?php echo __('tinyissue.dashboard_description'); ?>
	</span>
</h3>

<div class="pad">
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
?>
</div>
