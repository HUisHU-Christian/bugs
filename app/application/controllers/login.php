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
			$sendmail = true;
			
			$Owner = \User::where('id', '=', 1)->get(array('email','preferences','language'));
			$Prefs = $Owner[0]->Preferences;
			$Pref = substr($Prefs, strpos($Prefs, "noticeOnLogIn=")+13, 7);
			$Pref = substr($Pref, strpos($Pref, "=")+1);

			if (substr($Pref, 0, 1) == 'f') { $sendmail = false; }			
			if (Input::get('email') == $Owner[0]->email) { $sendmail = false; }
			if (Input::get('email') == Config::get('mail.from.email')) { $sendmail = false; }
			
			if ($sendmail) {
				$Type = 'noticeonlogin';
				$Langue = $Owner[0]->language;
				$contenu = array('noticeonlogin', 'email:'.Input::get('email'), 'lang:'.$Owner[0]->language); 
				$src = array('email','value','value');
				include_once "application/controllers/ajax/SendMail.php";
			}
			
			return Redirect::to(Input::get('return', '/'));
		}
		return Redirect::to('login')
			->with('error',  __('tinyissue.password_incorrect'));
	}
}
