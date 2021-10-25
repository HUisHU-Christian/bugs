ALTER TABLE projects_issues ADD COLUMN start_at datetime DEFAULT NULL AFTER created_at;
UPDATE projects_issues SET start_at = created_at;
