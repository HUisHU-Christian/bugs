<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>Error 500 - Internal Server Error</title>

		<style>
			body {
				background: #121212;
				color: #c3c4c5;
				font: 14pt;
			}
			a {
				color: yellow;
			}
			a:hover {
				color: white;
			}
		</style>
	</head>
	<body>
	<a href="<?php echo URL::to(); ?>"><img src="app/assets/images/layout/logo_degrade.png" height="100" alt="" /></a>
		<div id="main">
			<?php 
				$messages = array(__('tinyissue.error404_title_0'),__('tinyissue.error404_title_1'),__('tinyissue.error404_title_2')); 
			?>

			<h2><?php echo __('tinyissue.error404_header'); ?></h2>

			<h3><?php echo __('tinyissue.error404_means'); ?></h3>

			<p><?php echo __('tinyissue.error404_p1'); ?></p>

			<p><?php echo __('tinyissue.error404_p2').' '.HTML::link('/', __('tinyissue.homepage')); ?>?</p>
		</div>
	</body>
</html>