<?php if ($activity) { ?>
<?php
//if you change this file, don't forget to copy the new version to
// /app/application/views/activity/attached-file.php  
	if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
		echo '<script>document.location.href="'.URL::to().'";</script>';
	}
$FileTypes = (is_dir('../app/assets/images/upload_type')) ? $FileTypes= scandir('../app/assets/images/upload_type') : array();
$url =\URL::home();
$What = \DB::table('projects_issues_attachments')->where('id', '=', $activity->attributes['action_id'])->order_by('id','DESC')->get();
?>
<li id="comment<?php echo $activity->id; ?>" class="comment activity-item">
	<div class="insides">
		<div class="topbar">
			<div class="data">
				<?php
					if (@$What[0]->filename !== NULL) {
						$Who = \User::where('id', '=', $activity->attributes['user_id'] )->get(array('firstname','lastname','email'));
						//Modification du 23 juin 2019
						////Afin de pouvoir traiter encore les fichiers autrefois référencés avec "../" dans la base de données, nous corrigeons ici à la pièce
						////Le nouveau mode d'enregistrement n'impose plus les caractères "../" à l'enregistrement de l'adresse
						if (substr($What[0]->filename, 0, 3) == '../' ) { $What[0]->filename = substr($What[0]->filename, 3); }
						echo '<a href="'.$url.$What[0]->filename.'" target="_blank" />';
						echo '<img src="'.$url.(( in_array(strtolower($What[0]->fileextension), array('jpg','jpeg','gif','png'))) ? $What[0]->filename : ((( in_array(strtolower($What[0]->fileextension).'.png', $FileTypes)) ? 'app/assets/images/upload_type/'.$What[0]->fileextension.'.png' : 'app/assets/images/icons/file_01.png'))).'" height="30" align="right" border="0" />';
						echo '</a>';
						echo '<span style="font-weight: bold; color: #090;">'.__('tinyissue.fileuploaded').'</span> '.__('tinyissue.by').' ';
						echo $Who[0]->attributes["firstname"].' '.$Who[0]->attributes["lastname"].' : ';
						echo '<a href="'.$url.$What[0]->filename.'" style="font-weight: bold; color: #009; text-decoration:underline;" target="_blank">';
						echo '<b>'.$activity->attributes['data'].'</b>';
						echo '</a>';
						echo ' - '.date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->attributes['updated_at']));
					}
				?>
			</div>
		</div>
	</div>

	<div class="clr"></div>
</li>
<?php } ?>
