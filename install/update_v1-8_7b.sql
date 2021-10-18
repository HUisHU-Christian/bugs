ALTER TABLE projects_issues ADD COLUMN start_at date DEFAULT NULL AFTER created_at;
UPDATE projects_issues SET start_at = created_at;
