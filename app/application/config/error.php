<?php
return array(
	/*
	|--------------------------------------------------------------------------
	| Ignored Error Levels
	|--------------------------------------------------------------------------
	|
	| Here you simply specify the error levels that should be ignored by the
	| Laravel error handler. These levels will still be logged; however, no
	| information about about them will be displayed.
	|
	*/
	'ignore' => array(),

	/*
	|--------------------------------------------------------------------------
	| Error Detail
	|--------------------------------------------------------------------------
	|
	| Detailed error messages contain information about the file in which an
	| error occurs, as well as a PHP stack trace containing the call stack.
	| You'll want them when you're trying to debug your application.
	|
	| If your application is in production, you'll want to turn off the error
	| details for enhanced security and user experience since the exception
	| stack trace could contain sensitive information.
	|
	*/
	'detail' => true,

	/*
	|--------------------------------------------------------------------------
	| Error Logging
	|--------------------------------------------------------------------------
	|
	| When error logging is enabled, the "logger" Closure defined below will
	| be called for every error in your application. You are free to log the
	| errors however you want. Enjoy the flexibility.
	|
	*/
	'log' => true,
	
	/*
	|--------------------------------------------------------------------------
	| exit
	|--------------------------------------------------------------------------
	|
	| There are various method to exit from program : show all details (see up) or not
	| but you can also offer opportunity to come back at work with simple click
	|
	| Available values are:
	| 0 : exit with normally
	| 1 : exit with code for server
	| "Content" : Write there you message.  A link to home page will be automatically added.
	|
	*/
	//'exit' => 0,																				//Example of an exit with code 0
	//'exit' => 1,																				//Example of an exit with code 1
	//'exit' => "An error occured, please click here to come to BUGS ",		//Example of an exit with text
	'exit' => "Vous pouvez reprendre le travail, mais la page qui vous été montrée ici indique que vous ne pourrez pas tenter de faire cette tâche sans une mise à jour de ",

	/*
	|--------------------------------------------------------------------------
	| Error Logger
	|--------------------------------------------------------------------------
	|
	| Because of the various ways of managing error logging, you get complete
	| flexibility to manage error logging as you see fit. This function will
	| be called anytime an error occurs within your application and error
	| logging is enabled. 
	|
	| You may log the error message however you like; however, a simple log
	| solution has been setup for you which will log all error messages to
	| text files within the application storage directory.
	|
	*/
	'logger' => function($exception) {
		//mail("info@rcmission.net", "Erreur dans BUGS - auto log", $_SERVER["SERVER_ADDR"].'<br />'.$_SERVER["SERVER_NAME"].'<br /><br />'.$exception);
		Log::exception($exception);
	},
);