ALTER TABLE projects_issues_comments MODIFY COLUMN temps_fait_fin datetime DEFAULT NULL;
ALTER TABLE projects_issues_comments MODIFY COLUMN temps_fait_deb datetime DEFAULT NULL;
ALTER TABLE projects_issues_comments MODIFY COLUMN temps_fait smallint(4) DEFAULT 1;

