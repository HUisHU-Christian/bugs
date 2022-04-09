<?php
class install {
	public $config;
	function __construct() {
		$this->config = require '../config.app.php';
		$this->language = $_GET["Lng"] ?? "en";
		$this->language = require '../app/application/language/'.$_GET["Lng"].'/install.php';
	}

	public function check_connect() {
		@$connect = ($GLOBALS["___mysqli_ston"] = mysqli_connect($this->config['database']['host'], $this->config['database']['username'], $this->config['database']['password']));
		if(!$connect) { return array('error' => '<strong>'.($this->language['Database_Connect_Error'] ?? "We could not connect to database").'.</strong>!'); 	}
		$check_db = $this->check_db($connect);
				return $check_db;

		if(!$check_db) { return array('error' => '<strong>'.($this->language['Database_Error'] ?? "Error on database process").'.</strong>'); 		}
		return $check_db;
	}

	public function check_requirements() {
		$errors = array();
		if(!extension_loaded('pdo')) { 		$errors[] = 'pdo extension not found.'; }
		if(!extension_loaded('pdo_mysql')) { 	$errors[] = 'mysql driver for pdo not found .'; }
		if(version_compare(PHP_VERSION, '7.1', '<') && !extension_loaded('mcrypt')) { 		$errors[] = 'mcrypt extension not found.'; }
		if(version_compare(PHP_VERSION, '7.0', '>') && !extension_loaded('openSSL')) { 		$errors[] = 'openSSL extension not found.'; }
		if(version_compare(PHP_VERSION, '7.3', '<')) { 	$errors[] = 'PHP too old for Bugs. PHP 7.3 or above is needed.'; }
		return $errors;
	}

	public function create_database() {
		@$connect = ($GLOBALS["___mysqli_ston"] = mysqli_connect($this->config['database']['host'], $this->config['database']['username'], $this->config['database']['password']));
		if(!$connect) { return array('error' => '<strong>'.($this->language['Database_Connect_Error'] ?? "We could not connect to database").'.</strong>!'); 	}
		mysqli_query($GLOBALS["___mysqli_ston"], "CREATE DATABASE IF NOT EXISTS ".$_POST["database_name"]);
		if (!isset($GLOBALS["___mysqli_ston"]->error)) { return '<p style="color:#090;font-size: 150%;background-color: #FFF; text-align:center; width: 75%; position: absolute; top: 0; left: 15%;">'.$this->language['Database_CreateDatabase_success'].$_POST["database_name"].'</p>'; }
		return (trim($GLOBALS["___mysqli_ston"]->error) != '')  ?  '<p style="color:#F00;font-size: 150%;background-color: #FFF; text-align:center; width: 75%; position: absolute; top: 0; left: 15%;">'.$this->language['Database_CreateDatabase_failed'].'</p>' : '<p style="color:#090;font-size: 150%;background-color: #FFF; text-align:center; width: 75%; position: absolute; top: 0; left: 15%;">'.$this->language['Database_CreateDatabase_success'].$_POST["database_name"].'</p>';
	}

	public function create_tables() {
		$tablesCreator = require './mysql-structure.php';
		foreach($tablesCreator as $query) {
			$this->Requis($query);
		}
	}

	public function create_adminUser() {
			require '../app/laravel/hash.php';
			require '../app/laravel/str.php';
			/* Create Administrator Account */
			$email = (trim($_POST["email"]) != '' ) ? $_POST["email"] : 'admin@email.com';
			$first_name = (trim($_POST["first_name"]) != '' ) ? $_POST["first_name"] : 'first_name';
			$last_name = (trim($_POST["last_name"]) != '' ) ? $_POST["last_name"] : 'last_name';
			$password = (trim($_POST["password"]) != '' ) ? $_POST["password"] : 'admin';
			$work = str_pad(8, 2, '0', STR_PAD_LEFT);
			$salt = substr(str_shuffle(str_repeat('01234567789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',5)),0,40);
			$salt = substr(strtr(base64_encode($salt), '+', '.'), 0, 22);
			$password = crypt($password, '$2a$'.$work.'$'.$salt);
			$language = $_POST['language'];
			/* Check if email exists if so change the password on it */
			$test_query = "select * from users where email = '".$email."' and deleted = 0 LIMIT 1";
			$test_result = $this->Requis($test_query);
			if($this->Combien($test_result) >= 1) {
				$query = "UPDATE `users` SET
					password = '".$password."',
					firstname = '".$first_name."',
					lastname = '".$last_name."'
				WHERE email = '".$email."' AND deleted = 0
				LIMIT 1
				";
			} else {
				$query = "
				INSERT INTO users(
					role_id,
					email,
					password,
					firstname,
					lastname,
					language,
					created_at
				)VALUES(
					4,
					'".$email."',
					'".$password."',
					'".$first_name."',
					'".$last_name."',
					'".$language."',
					now()
				)";
			}
			mysqli_query($GLOBALS["___mysqli_ston"], $query);
			return true;
	}

	private function check_db($connect) {
		$database_connect = ((bool)mysqli_query( $connect, "USE " . $this->config['database']['database']));
		if($database_connect) {
			return $database_connect;
		}
		return false;
	}
	
	public function Combien($resu) {
		return mysqli_num_rows($resu);
	}
	
	public function Requis ($query) {
		return mysqli_query($GLOBALS["___mysqli_ston"], $query);
	}
}
