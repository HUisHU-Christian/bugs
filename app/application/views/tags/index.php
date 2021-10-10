<h3>
	<?php echo __('tinyissue.tags') ?>
</h3>

<div class="pad">

	<?php foreach($tags as $tag): ?>
		<?php 
		//2 sept 2021 recherche d'un bogue lié à ftcolor
		echo '<a href="' . URL::to('tag/' . $tag->id . '/edit') . '"><label id="tag' . $tag->id . '" class="label" style="' . ($tag->bgcolor ? ' background-color: ' . $tag->bgcolor.';' : '') . ($tag->ftcolor ? ' color: ' . $tag->ftcolor . ';' : '') . '">' . $tag->tag . '</label></a>'; 
		//echo '<a href="' . URL::to('tag/' . $tag->id . '/edit') . '"><label id="tag' . $tag->id . '" class="label" style="' . ($tag->bgcolor ? ' background-color: ' . $tag->bgcolor.';' : '') . ($tag->ftcolor ? ' color: ' . $tag->ftcolor . ';' : '') . '">' . $tag->tag . '</label></a>'; 
		//echo '<a href="' . URL::to('tag/' . $tag->id . '/edit') . '"><label id="tag' . $tag->id . '" class="label" style="' . ($tag->bgcolor ? ' background-color: ' . $tag->bgcolor.';' : '') . '">' . $tag->tag . '</label></a>'; 
		?><br /><br />
	<?php endforeach; ?>
	
	<br />
	
	<form method="to" action="<?php echo URL::to('tag/new'); ?>">
		<input type="submit" value="<?php echo __('tinyissue.create_tag'); ?>" class="button primary" />
	</form>

</div>