<?php

class Login_Controller extends Controller {

	public $restful = true;

	public function get_index() {
		return View::make('layouts.login');
	}

	public function post_index() {
		$userdata = array(
			'username' => Input::get('email'),
			'password' => Input::get('password'),
			'remember' => (bool) Input::get('remember')
		) ;
		
		if(Auth::attempt($userdata)) {
			Session::forget('return');
			$Type = 'noticeonlogin';
			$contenu = array('noticeonlogin','value'); 
			$src = array('email', Input::get('email'));
			include_once "application/controllers/ajax/SendMail.php";
			
			return Redirect::to(Input::get('return', '/'));
		}
		return Redirect::to('login')
			->with('error',  __('tinyissue.password_incorrect'));
	}

}
