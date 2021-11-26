<h3>
	<?php echo __('tinyissue.role') ?>s
</h3>

<div class="pad">

	<form method="post" action="">
	<?php 
	echo '<br /><br />';
	$bgcoul = array("white","#008000","#ffa500","#ff0000","#4f0000");
	$bgcolr = array("white","#006000","#CC8500","#CC0000","#300000");
	$txcoul = array("black","white","black","black","white");
	foreach($roles as $role) {
		echo '<span style="font-size: 150%; color: '.$bgcoul[$role->id].';">'.$role->id.'</span>.&nbsp;';
		echo '<input name="RoleName['.$role->id.']" type="input" size="15" maxlenght="20" value="'.$role->name.'" style="color: '.$txcoul[$role->id].'; background-color: '.$bgcoul[$role->id].'; height: 45px; font-size: 150%; border: none; border-radius: 5px 0 0 5px;" />';
		echo '<input name="RoleDesc['.$role->id.']" type="input" size="75" maxlenght="255" value="'.$role->description.'" style="color: '.$txcoul[$role->id].'; background-color: '.$bgcolr[$role->id].'; height: 45px; font-size: 150%; border: none; border-radius: 0 5px 5px 0;" />';
		echo '<br /><br />';
		echo '<br /><br />';
	} 
	?>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" onclick="document.location.href = 'administration';" value="<?php echo __('tinyissue.cancel'); ?>" class="button secondary" />
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="submit" value="<?php echo __('tinyissue.roles_modify'); ?>" class="button primary" />
	</form>

</div>