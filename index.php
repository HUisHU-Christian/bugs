<?php
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

//Auto-update the database if conditions are fullfilled
if (isset($_SERVER ["REDIRECT_SCRIPT_URL"]) && isset($_POST["MAJsql"])) {  
	if (substr($_SERVER ["REDIRECT_SCRIPT_URL"], 0, -5) == substr($_SERVER["PHP_SELF"], 0, -9)  && substr($_SERVER ["REDIRECT_SCRIPT_URL"], -5) == 'login' && trim($_POST["MAJsql"]) != '' && substr($_POST["MAJsql"], 0, 7) == 'update_' && substr($_POST["MAJsql"], -4) == '.sql') {
		$sql = file_get_contents("../install/".$_POST["MAJsql"], $flags = null, $context = null, $offset = null, $maxlen = null);
		file_put_contents("../install/historique.txt", ";".$_POST["MAJsql"], FILE_APPEND);
		unset($_POST["MAJsql"]);
	}
}
