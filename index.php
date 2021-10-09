<?php
session_start();

/* Redirect if we have not installed */
if(!file_exists(__DIR__ . '/config.app.php')) {
	echo '<script>document.location.href="./install/";</script>';
	exit;
}

define('LARAVEL_START', microtime(true));
$web = true;
$LangEN = array();
$LangEN["pagination"] = require 'app/application/language/en/pagination.php';
$LangEN["tinyissue"] = require 'app/application/language/en/tinyissue.php';
$LangEN["validation"] = require 'app/application/language/en/validation.php';

require 'app/paths.php';
unset($web);

require path('sys').'laravel.php';
