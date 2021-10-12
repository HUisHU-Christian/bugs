<?php 
	if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
		echo '<script>document.location.href="'.URL::to().'";</script>';
	}
?>
<?php if ($activity) { ?>
<li id="comment<?php echo $activity->id; ?>" class="comment">
	<div class="insides">
		<div class="topbar">
			<div class="data">
				<?php
					if (trim($activity->attributes['data']) == '') {
						$Msg = ($activity->attributes['parent_id'] == $activity->attributes['item_id'] ) ? __('tinyissue.tag_removed') : __('tinyissue.tag_added');
						$Col = ($activity->attributes['parent_id'] == $activity->attributes['item_id'] ) ? "none" : "underline";
						$TagNum = Tag::where('id', '=', $activity->attributes['action_id'] )->first(array('id','tag','bgcolor','ftcolor'));
						$Who = \User::where('id', '=', $activity->attributes['user_id'] )->get(array('firstname','lastname','email'));
						echo '<label style="background-color: '.@$TagNum->attributes['bgcolor'].';color: '.@$TagNum->attributes['ftcolor'].'; padding: 5px 10px; border-radius: 8px;">';
						echo @$TagNum->attributes['tag'];
						echo '</label> : BAboom';
						echo '<span style="font-weight: bold; text-decoration: '.$Col.';">'.$Msg.'</span>';
						echo  __('tinyissue.by') . ' <b>' .$Who[0]->attributes["firstname"].' '.$Who[0]->attributes["lastname"].'</b> ';
						echo date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->attributes['updated_at']));
					} else {
						$prem = strpos($activity->attributes['data'], "[");
						$pren = strpos($activity->attributes['data'], "]");
						$deux = strpos($activity->attributes['data'], "[", $prem+1);
						$deuy = strpos($activity->attributes['data'], "]", $deux);
						$valadd = trim(substr($activity->attributes['data'], $prem+1, ($pren-$prem)-1));
						$numtag = ($valadd != '') ? $valadd : intval(trim(substr($activity->attributes['data'], $deux+1, ($deuy-$deux)-1))) * -1;
						$tag_info = \DB::table('tags')->where('id', '=', abs($numtag))->get();
					 	echo '<label style="background-color: '.$tag_info[0]->bgcolor.'; '.($tag_info[0]->ftcolor ? 'color: '.$tag_info[0]->ftcolor.'; ' : '').' padding: 5px 10px; border-radius: 8px;">'.$tag_info[0]->tag.'</label><b>';
					 	echo ' ';
					 	echo ($numtag > 0) ? '<span style="color: green;">'.__('tinyissue.tag_added').'</span>' : '<span style="color: red;">'.__('tinyissue.tag_removed').'</span>'; 
						echo ' '.__('tinyissue.by').' '.$user->attributes["firstname"].' '.$user->attributes["lastname"].'</b></a> ';
						echo ' &nbsp;&nbsp; '.date(Config::get('application.my_bugs_app.date_format'), strtotime($activity->attributes['updated_at'])).'';
					}
				?>
			</div>
		</div>
	</div>
	<div class="clr"></div>
</li>
<?php } ?>
