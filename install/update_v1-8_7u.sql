UPDATE projects_users AS PRO SET role_id = (SELECT role_id FROM users AS USR WHERE USR.id = PRO.user_id), updated_at = NOW() WHERE role_id = 0;
