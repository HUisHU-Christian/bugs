ALTER TABLE projects_users ADD COLUMN updated_at DATETIME DEFAULT NOW()  AFTER role_id;
ALTER TABLE projects_users ADD COLUMN created_at DATETIME DEFAULT NOW()  AFTER updated_at;
UPDATE projects_users SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL AND updated_at IS NULL;

