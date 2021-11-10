<h3>
	<?php echo __('tinyissue.create_a_new_project');?>
	<span><?php echo __('tinyissue.create_a_new_project_description');?></span>
</h3>
<script type="text/javascript" >
<?php
	$liste = '<select name="role[]">';
	$r = \DB::table('roles')->where('id', '<=', Auth::user()->role_id)->order_by('id','DESC')->get();
	foreach ($r as $val) {
		$liste .= '<option value="'.$val->id.'">'.$val->name.'</option>';
	}
	$liste .= '</select>';
?>
var liste = '<?php echo $liste; ?>';
</script>
<div class="pad">

	<form method="post" action="" id="submit-project">
		<table class="form" style="width: 80%;">
			<tr>
				<th style="width: 10%;"><?php echo __('tinyissue.name');?></th>
				<td><input type="text" name="name" style="width: 90%;  background-color: #FFF; color: #000; border-width: 2px; border-color: #999;" /></td>
			</tr>
		</table>

		<input type="hidden" name="default_assignee" value="<?php echo Auth::user()->id; ?>" id="default_assignee-id" />

	<table class="form" style="width: 80%;">
		<tr>
			<th style="width: 10%;"><?php echo __('tinyissue.assign_users');?></th>
			<td>
				<input type="text" id="add-user-project" style="margin: 0;  background-color: #FFF; color: #000; border-width: 2px; border-color: #999;" placeholder="<?php echo __('tinyissue.assign_users_holder');?>" />

				<ul class="assign-users" style="width: 40%;">
					<li class="project-user<?php echo Auth::user()->id; ?>">
						<a href="javascript:void(0);" onclick="$('.project-user<?php echo Auth::user()->id; ?>').remove();" class="delete">Remove</a>
						<?php echo Auth::user()->firstname . ' ' . Auth::user()->lastname; ?>
						<input type="hidden" name="user[]" value="<?php echo Auth::user()->id; ?>" />
						<div style="float: right; padding-right: 10px;"><?php echo $liste; ?></div>
						<br clear="all" />
					</li>
				</ul>
			</td>
		</tr>
		<tr>
			<th></th>
			<td><input type="submit" onclick="$('#submit-project').submit();" value="<?php echo __('tinyissue.create_project');?>"  /></td>
		</tr>
	</table>
	</form>

</div>