<?php

class Administration_Activity_Controller extends Base_Controller {

	public function __construct() {
		parent::__construct();

		$this->filter('before', 'permission:administration');
	}

	public function get_index() {
		return $this->layout->with('active', 'dashboard')->nest('content', 'administration.activity.index', array(
			'activities' => \Activity::order_by(\Auth::user()->language, 'ASC')->get()
		));
	}

	public function get_edit($id_activ) {
		return $this->layout->with('active', 'dashboard')->nest('content', 'administration.activity.edit', array(
			'activity' => \Activity::find($id_activ)
		));
	}

	public function post_edit() {
		$update = \Activity::update_activity(Input::all());

		if(!$update['success']) {
			return Redirect::to('administration/activity/edit/'.Input::get('id'))
				->with('notice-error', __('tinyissue.admin_modif_false'));
		}

		return Redirect::to('administration/activity')
			->with('notice', __('tinyissue.admin_modif_true'));


	}

}
