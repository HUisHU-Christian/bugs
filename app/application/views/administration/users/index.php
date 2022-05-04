<h3>
	<a href="<?php echo URL::to('administration/users/add');?>" class="addnewuser"><?php echo __('tinyissue.add_new_user'); ?></a>
	<?php echo __('tinyissue.users'); ?>
	<span><?php echo __('tinyissue.users_description'); ?></span>
</h3>

<div class="pad">

	<div id="users-list">

		<?php foreach($roles as $role):?>

			<h4>
				<?php echo $role->name; ?>
				<span><?php echo $role->description; ?></span>
			</h4>

			<ul class="ListUsers" style="position: relative; margin-left: 8%; margin-right: 40%;">
				<?php foreach(User::where('role_id', '=', $role->id)->where('deleted', '=', 0)->order_by('firstname', 'asc')->get() as $user): ?>
				<li>
					<ul>
						<?php if(!$user->me()): ?>
						<li class="delete">
							<a href="<?php echo URL::to('administration/users/delete/' . $user->id);?>" onClick="return confirm('<?php echo __('tinyissue.delete_user_confirm'); ?>');" class="button tiny error right"><?php echo __('tinyissue.delete'); ?></a>
						</li>
						<?php endif; ?>
						<li class="edit">
						<?php 
							if($user->me()) {
								echo '<a href="'.URL::to('user/settings').'">'. __('tinyissue.edit').'</a>';
							} else {
								echo '<a href="'.URL::to('administration/users/edit/' . $user->id).'">'. __('tinyissue.edit').'</a>';
							}
						?>
						</li>
					</ul>
					<a class="name" href="mailto: <?php echo $user->email;?>">
						<img src="../app/assets/css/<?php echo \User::pref("template"); ?>/images/courriel.png" height="30" alt="Courriel" style="vertical-align: middle; padding-right: 15px;" />
					</a>
					<a class="name" href="<?php echo URL::to('administration/users/edit/' . $user->id);?>">
						<img src="../app/assets/css/<?php echo \User::pref("template"); ?>/images/modifier.png" height="30" alt="Modifier" style="vertical-align: middle; padding-right: 15px;" />
						<?php echo $user->firstname . ' ' . $user->lastname; ?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>

		<?php endforeach; ?>

	</div>

</div>