<?php 
if (!Project\User::MbrProj(\Auth::user()->id, Project::current()->id)) {
	echo '<script>document.location.href="'.URL::to().'";</script>';
}
?>
<div class="blue-box">
	<div class="inside-pad filterANDsort">
		<div class="filter-and-sorting">
			<form method="get" action="">
				<div class="filter-and-sorting_TAGS">
					<div style="position: absolute; left: 0; width: 10%; font-weight: bold; text-align: right; top: 13px;">
						<b><?php echo __('tinyissue.tags'); ?></b><br /><span style="font-weight: lighter;">Joker : % *</span>
					</div>
					<div style="position: absolute; left: 11%; width: 85%;">
							<?php echo Form::text('tags', Input::get('tags', ''), array('id' => 'tags')); ?>
							<script type="text/javascript">
							$(function(){
								$('#tags').tagit({
									autocomplete: {
										source: '<?php echo URL::to('ajax/tags/suggestions/filter'); ?>'
									}
								});
							});
							</script>
					</div>
				</div>
				<div class="filter-and-sorting_BAS">

					<div class="filter-and-sorting_FILTER" >
						<div style="position: relative; left: -15%; width: 15%; font-weight: bold; text-align: left; top: 3px; display: inline; margin-right: -10%;">
							<b><?php echo __('tinyissue.limits'); ?></b>
						</div>
						<?php echo Form::select('limit_contrib', array( 'assigned_to' => __('tinyissue.limits_contrib_assignedto'),'created_by'  => __('tinyissue.limits_contrib_createdBy'),'closed_by'   => __('tinyissue.limits_contrib_closedby'),'updated_by'  => __('tinyissue.limits_contrib_updatedby')), Input::get('limit_contrib', '')); ?>
						<?php echo Form::select('assigned_to', $assigned_users, Input::get('assigned_to', '')); ?>
						<br /><br />
						<?php echo Form::select('limit_event', array('created_at' => __('tinyissue.limits_event_createdat'),'updated_at' => __('tinyissue.limits_event_updatedAt'),'closed_at'  => __('tinyissue.limits_event_closedAt')), Input::get('limit_event', '')); ?>
						<?php echo Form::select('limit_period',array('' => "", 'week' 	=> __('tinyissue.limits_period_week'),'month' 	=> __('tinyissue.limits_period_month'),'months' => __('tinyissue.limits_period_months'),'years' => __('tinyissue.limits_period_years')), Input::get('limit_period', ''),array("onchange"=>"CalculonsDates(this.value);" ) ); ?>
						<br /><br />
						<?php echo Form::date('DateInit', input::get('DateInit',(date("Y")-1).date("-m-d")), array('id' => 'input_DateInit')); ?>
						<?php echo Form::date('DateFina', input::get('DateFina',date("Y-m-d")), array('id' => 'input_DateFina')); ?>
					</div>
					
					<div class="filter-and-sorting_SORT">
						<div style="position: absolute; left: -25%; width: 25%; font-weight: bold; text-align: left; top: 2px; display: inline-block;">
							<b><?php echo __('tinyissue.sort_by'); ?></b>
						</div>
						<?php echo Form::select('sort_by', $sort_options, Input::get('sort_by', (Input::get('tag_id','') == 1) ? 'projects_issues.status' : 'projects_issues.updated_at')); ?>
						<?php echo Form::select('sort_order', array('asc' => __('tinyissue.sort_asc'), 'desc' => __('tinyissue.sort_desc')), $sort_order); ?>
						<input name="tag_id" value="<?php echo Input::get('tag_id', '1'); ?>" type="hidden" />
						<br />
						<br /><br />
						<input type="submit" value="<?php echo __('tinyissue.show_results'); ?>" class="button primary" />
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
</div>
</div>

<div class="pad">
<?php
	$page = $_GET["page"] ?? 1;
	if (count($issues) > \Config::get('application.pref.todoitems')) {
		echo '<br />';
		$compte = 0;
		$rendu = 0;
		echo '<ul class="tabs">';
		while ($rendu<=count($issues)) {
			echo '<li'.((++$compte == $page) ? ' class="active"' : '').'><a href="issues?tag_id='.$_GET["tag_id"].'&page='.$compte.'">'.$compte.'</a></li>';
			$rendu = $rendu + \Config::get('application.pref.todoitems');
		}
		echo '</ul>';
	}
?>

<div class="inside-tabs">
<div class="blue-box">
	<div class="inside-pad" id="lane-details-0">
		<?php 
		if(!$issues) {
				echo '<p>'.__('tinyissue.no_issues').'</p>';
		} else {
			$rendu = 0;
			echo '<ul class="issues" id="sortable">';
			foreach($issues as $row) {
				$rendu = $rendu + 1;
				if ($rendu <= (($page-1)*\Config::get('application.pref.todoitems'))) { continue; }
				if ($rendu > ($page*\Config::get('application.pref.todoitems'))) { break; }
				$follower = \DB::table('following')->where('project','=',0)->where('issue_id','=',$row->id)->where('user_id','=',\Auth::user()->id)->count();
				$follower = ($follower > 0) ? 1 : 0;
				
				echo '<li class="sortable-li activity-item" data-issue-id="'.$row->id.'">';
				echo '<a href="javascript: Following('.$row->id.', '.$row->project_id.', '.\Auth::user()->id.');" class="commentstate_'.$follower.'" id="a_following_'.$row->id.'"  style="min-height: '.$follower.'; "  title="'.$row->comment_count().' '.__('tinyissue.following_stand').' / '.(($follower == 0) ? __('tinyissue.following_start') : __('tinyissue.following_stop')).'" >'.$row->comment_count().'</a>';

				if(!empty($row->tags)) {
					$Lng = strtoupper(\Auth::user()->language);
					echo '<div class="tags">';
					foreach($row->tags()->order_by('tag', 'ASC')->get() as $tag) {
						echo '<label class="label" style="'.($tag->bgcolor ? ' background-color: ' . $tag->bgcolor . ';' : '').($tag->ftcolor ? ' color: ' . $tag->ftcolor . ';' : '').'">' . (($tag->$Lng != '') ? $tag->$Lng : $tag->tag) . '</label>';
					}
					echo '</div>';
				} 

				echo '<div style="width: 72px; float: left; text-align:center; ">
						<a href="" class="id">#'.$row->id.'</a>
						<br /><br />
						<br /><br />
						<span class="colstate" style="color: '.\Config::get('application.pref.prioritycolors')[$row->status].'; " 
							onmouseover="document.getElementById(\'taglev\').style.display = \'block\';" 
							onmouseout="document.getElementById(\'taglev\').style.display = \'none\';">&#9899;
						</span>
					</div>';
				echo '<div class="data">';
					echo '<a href="'.$row->to().'" style="font-size: 150%; ">'.$row->title.'</a>';
					echo '<div class="info">';
					echo __('tinyissue.created_by'); 
					echo '&nbsp;&nbsp;<strong>'.$row->user->firstname . ' ' . $row->user->lastname.'</strong>';
					if(is_null($row->updated_by)) { echo Time::age(strtotime($row->created_at)); }
					if(!is_null($row->updated_by)) {  
						echo ' - '.__('tinyissue.updated_by');
						echo '&nbsp;&nbsp;<strong>';
						echo (isset($row->updated->firstname)) ? $row->updated->firstname : '';
						echo (isset($row->updated->lastname)) ? $row->updated->lastname : '';
						echo '</strong> ';
						echo Time::age(strtotime($row->updated_at));
					} 
					if (substr($row->start_at, 0, 10) > date("Y-m-d")) { echo ' <span style="color: black; background-color: yellow;">'.__('tinyissue.issue_start_at').' '.substr($row->start_at, 0, 10).'</span>'; }
					if($row->assigned_to != 0) {
						echo ' - '.__('tinyissue.assigned_to'); 
						echo '&nbsp;&nbsp;<strong>'.((isset($row->assigned->firstname)) ? $row->assigned->firstname : '') . ' ' . ((isset($row->assigned->lastname)) ? $row->assigned->lastname : '').'</strong>';
					} 
					echo '</div>';

					$_GET["tag_id"] = $_GET["tag_id"] ?? 0;
//Gestion des droits basée sur le rôle spécifique à un projet
//Modification du 13 novembre 2021
//					if ($_GET["tag_id"] == 1 && Auth::user()->role_id != 1) {
					if ($_GET["tag_id"] == 1 && \Project\User::GetRole($row->project_id) != 1) {
						echo '<br /><br />';
								//Percentage of work done
								////Calculations
								$SizeXtot = 500;
								$SizeX = $SizeXtot / 100;
								$Etat = Todo::load_todo($row->id);
								////Here we show the progress bar
								if (is_object($Etat)) {
									echo '<div class="Percent2" id="div_ProgressBarPercent">';
									echo '<div style="background-color: green; position: absolute; top: 0; left: 0; width: '.($Etat->weight).'%; height: 100%; text-align: center; line-height:20px;" />&nbsp;</div>';
									echo '<div style="background-color: gray; position: absolute;  top: 0; left: '.$Etat->weight.'%; width: '.(100-$Etat->weight).'%; height: 100%; text-align: center; line-height:20px;" />&nbsp;</div>';
									echo '</div>';
								}
						
								//Timing bar, according to the time planified (field projects_issues - duration) for this issue
								////Calculations
								$Deb = strtotime($row->start_at);
								$Dur = (time() - $Deb) / 86400;
								$Dur = ($Dur < 0) ? 0 : $Dur;
								if (!isset($row->duration)) { $row->duration = 30; }
								if ($row->duration === 0 || is_null($row->duration)) { $row->duration = 30; }
								$DurRelat = round(($Dur / $row->duration) * 100);
								$Dur = round($Dur);
								$DurColor = ($DurRelat < 65) ? 'green' : (( $DurRelat > \Config::get('application.pref.percent')[3]) ? 'red' : 'yellow') ;
								if ($DurRelat >= 50 && (!isset($Etat->weight) || $Etat->weight <= 50) ) { $DurColor = 'yellow'; }
								if ($DurRelat >= 75 && (!isset($Etat->weight) || $Etat->weight <= 50) ) { $DurColor = 'red'; }
								$TxtColor = ($DurColor == 'yellow') ? 'black' : 'white' ;
								////Here we show to progress bar
								echo '<div class="Percent2" id="div_ProgressBarDays">';
								echo '<div style="background-color: '.$DurColor.'; position: absolute; top: 0; left: 0; width: '.(($DurRelat <= 100) ? $DurRelat : 100).'%; height: 100%; text-align: center; line-height:20px;" />&nbsp;</div>';
								if ($DurRelat < 100) {  echo '<div style="background-color: gray; position: absolute;  top: 0; left: '.$DurRelat.'%; width: '.(100-$DurRelat).'%; height: 100%; text-align: center; line-height:20px;" />&nbsp;</div>'; }
								echo '</div>';
					}
					echo '<br clear="all" />';
					echo '</div>';
					echo '</li>';
	
				}
				echo '</ul>';
			 }
//Gestion des droits basée sur le rôle spécifique à un projet
//Selon l'analyse du 13 novembre 2021, il n'est pas nécessaire de changer le calcul de droit ci-bas
//			 if (\Project\User::GetRole($row->project_id) != 1) { 
			 if (Auth::user()->role_id != 1) { 
				echo '<div id="sortable-msg">'.__('tinyissue.sortable_issue_howto').'</div>';
			}
		 ?>
		<div id="sortable-save"><input id="sortable-save-button" class="button primary" type="submit" value="<?php echo __('tinyissue.save'); ?>" /></div>
	</div>
</div>
<script type="text/javascript">
function CalculonsDates(Quoi) {
	var auj = new Date();
	var dat = new Date();
	var yyyy = auj.getFullYear();
	var mm = auj.getMonth()+1; //January is 0!
	var dd = auj.getDate();
	mm = (mm < 10) ? '0'+ mm : mm;	
	dd = (dd < 10) ? '0'+ dd : dd;	
	document.getElementById('input_DateInit').value = yyyy + '-' + mm + '-' + dd;
	var duree = 365;
	if (Quoi == 'week') { duree = 7; }
	if (Quoi == 'month') { duree = 31; }
	if (Quoi == 'months') { duree = 62; }
	if (Quoi == 'years') { duree = 365; }
	dat.setDate(dat.getDate() - duree);
	yyyy = dat.getFullYear();
	mm = dat.getMonth()+1; //January is 0!
	dd = dat.getDate();
	mm = (mm < 10) ? '0'+ mm : mm;	
	dd = (dd < 10) ? '0'+ dd : dd;	
	document.getElementById('input_DateFina').value = yyyy + '-' + mm + '-' + dd;
}
function Following(Quel, Project, Qui) {
	<?php if (@$_GET["tag_id"] != 2) { ?> 
	var etat = (document.getElementById('a_following_' + Quel).style.minHeight.substr(0,1) == '0') ? 0 : 1;
	var data = Follows(1, Qui, Project, Quel, etat);
	if (data != '') {
		etat = Math.abs(etat-1);
		document.getElementById('a_following_' + Quel).className = "commentstate_" + etat;
		document.getElementById('a_following_' + Quel).style.minHeight = etat+"px";
	}
	<?php } ?>
}
function pad(n, width, z) {
  z = z || '0';
  n = n + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}
function OteTag() {
	return true;
}
function AddTag (tags){
	return true;
}

function LitTags () {
	return true;
}
</script>
