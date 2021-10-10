<h3>
<?php 
echo __('tinyissue.projects');
echo '<span>'.__('tinyissue.projects_description').'</span>';

$_GET["status"] = $_GET["status"] ?? 1;
?>
</h3>

<div class="pad">

	<ul class="tabs">
		<li <?php echo $active == 'active' ? 'class="active"' : ''; ?>>
			<a href="<?php echo URL::to('projects'); ?>">
				<?php echo $active_count < 2 ? (($active_count == 0) ? __('tinyissue.no_one') : '1') . ' '.__('tinyissue.active_project') : $active_count . ' '.__('tinyissue.active_projects'); ?>
			</a>
		</li>
		<li <?php echo $active == 'archived' ? 'class="active"' : ''; ?>>
			<a href="<?php echo URL::to('projects'); ?>?status=0">
				<?php echo $archived_count < 2 ? (($archived_count == 0) ? __('tinyissue.no_one') : '1') . ' '.__('tinyissue.archived_project') : $archived_count . ' '.__('tinyissue.archived_projects'); ?>
			</a>
		</li>
	</ul>

	<div class="inside-tabs">
		<div class="blue-box">
			<div class="inside-pad">
				<ul class="projects">
				<?php
					foreach($projects as $row) {
						$issues = $row->count_open_issues();
						$closedissues = $row->issues()->where('status', '=', 0)->count();
						$dayspassed = (date("U") - date("U",strtotime($row->created_at)))/86400;
						$velocity = number_format($closedissues/$dayspassed,2);
						$etcday = 0;
						if($velocity > 0){ $etcday = ceil($issues / $velocity); }else{ $etcday = $issues / 1; }
						$etc = date("d-m-Y",strtotime("+".$etcday." days"));
						echo '<li>';
							echo '<a href="'.$row->to().'">'.$row->name.'</a><br />';
							echo $issues == 1 ? '1 '. __('tinyissue.open_issue') : $issues . ' '. __('tinyissue.open_issues');
							echo '&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;'; 
							echo $closedissues == 1 ? '1 '. __('tinyissue.closed_issue') : $closedissues . ' '. __('tinyissue.closed_issues'); 
							echo '<br />';
							if ($row->count_open_issues() > 0) { 
								echo '<strong>'.__('tinyissue.velocity_velocity').':</strong>&nbsp;'.$velocity.'&nbsp;'.__('tinyissue.velocity_rate').'&nbsp;&nbsp;&nbsp;';
								echo '<strong>'.__('tinyissue.velocity_etc').':</strong>&nbsp;'.$etc;
							}
							echo '<br />';
						echo '</li>';
					} 
					echo '<li>';
					if(Auth::user()->permission('project-create') && @$_GET["status"] == 1) { 
						echo (count($projects) == 0) ? __('tinyissue.you_do_not_have_any_projects') : '<br />';
						echo '<a href="'.URL::to('projects/new').'">';
						echo __('tinyissue.create_project'); 
						echo '</a>';
					}
					echo '</li>';
				?>
				</ul>
			</div>
		</div>
	</div>
</div>
