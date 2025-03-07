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
	| Exit message
	|--------------------------------------------------------------------------
	|
	| You may give to users the opportunity to come back to BUGS by writing a sentence
	| or to only abort the process as it was at the beginning of BUGS
	|
	| Enable values are
	| 0 ( int ) : to abort process and kill all PHP processes
	| 1 ( int ) : to abort process nicely
	| text : to show text on screen with a like to the todo page.
	| 
	| You should not modify this page yourself, 
	| it's better to use the BUGS Administration page
	|  - section Error managing
	|  - set "Show a message ... " to YES
	|  - write your message in the message box
	*/
   'exit' => 'Cliquez ci-contre pour retourner à la page d`accueil de.',
																																																																																																																																																																																																																																																																																																					

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
		Log::exception($exception);
	},
	
	/*
	|--------------------------------------------------------------------------
	| Delay
	|--------------------------------------------------------------------------
	|
	| Temps d'affichage de la page d'erreur (en secondes) avant que l'usager soit ramené à la page index.php
	|
	*/

   'delay' => 99,
								
	/*
	|--------------------------------------------------------------------------
	| Acuracy
	|--------------------------------------------------------------------------
	|
	| Précision et fréquence des informations enregistrées dans le registre
	| 0: ERROR : seules les erreurs sont enregistrées
	| 1: ERR  : 
	| 2: MORE : 
	| 3: INFO : 
	| 4: SAYS :
	| 5: DETAILS : toutes les actions sont enregistrées
	| 
	*/

   'acuracy' => 3,
				
);