<?php

return array(

	/** new user **/
//   'subject_newuser' => 'Your '.Config::get('application.my_bugs_app.name').' account',
//	'new_user' => 'You have been set up with '.Config::get('application.my_bugs_app.name').' at',
   'creds' => 'You may log in with email %s and password %s.',
	
	/** issue updates **/
	'new_issue' => 'New issue "%s" was submitted to "%s" project',
	'new_comment' => 'Issue "%s" in "%s" project has a new comment',
	'assignment' => 'New issue "%s" was submitted to "%s" project and assigned to you',
	'assigned_by' => 'Assigned by: ',
	'reassignment' => 'Issue "%s" in "%s" project was reassigned to you',
	'update' => 'Issue "%s" in "%s" project was updated',
	
	'following_email_noticeonlogin' => 'Hello<br /><br />New connection to BUGS system occured with username: <b>{email}</b>.<br /><br /><br />Sincerely.<br />',
	'following_email_noticeonlogin_tit' => 'User connected to BUGS',
	'following_email_useradded' => 'Welcome as new member and user of BUGS.  <br />An administrator added your name and email into the BUGS system so you can collaborate with his group.  First of all, you need to define your password.  For now, you temporary password is : {static}',
	'following_email_useradded_tit' => 'Welcome on BUGS',

	'submitted_by' => 'Submitted by: %s',
	'created_by' => 'Created by: %s',
	'reassigned_by' => 'Reassigned by: ',
	'updated_by' => 'Updated by: %s',

	'issue_changed' => 'Issue "%s" in "%s" project was %s',
	'closed' => 'closed',
	'reopened' => 'reopened',
	//changed, reopened, etc. by
	'by' => 'by',	
	
	/** general **/
	'more_url' => 'Find more information at: ',
);