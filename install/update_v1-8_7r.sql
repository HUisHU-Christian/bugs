ALTER TABLE projects_issues MODIFY COLUMN temps_fact int DEFAULT 0;
ALTER TABLE projects_issues MODIFY COLUMN temps_paye int DEFAULT 0;
UPDATE projects_issues SET temps_plan = 0, temps_fact = 0, temps_paye = 0 WHERE created_at < '2021-09-01';

ALTER TABLE projects_issues_comments MODIFY COLUMN temps_fait int DEFAULT 0;
UPDATE projects_issues_comments SET temps_fait = 0 WHERE created_at < '2021-09-01';
