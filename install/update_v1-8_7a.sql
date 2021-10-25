ALTER TABLE projects_issues ADD COLUMN temps_plan smallint(4) DEFAULT 30 AFTER duration;
ALTER TABLE projects_issues ADD COLUMN temps_fact smallint(4) DEFAULT 30 AFTER temps_plan;
ALTER TABLE projects_issues ADD COLUMN temps_paye smallint(4) DEFAULT 30 AFTER temps_fact;

ALTER TABLE projects_issues_comments ADD COLUMN temps_fait_fin time DEFAULT NULL AFTER comment;
ALTER TABLE projects_issues_comments ADD COLUMN temps_fait_deb time DEFAULT NULL AFTER comment;
ALTER TABLE projects_issues_comments ADD COLUMN temps_fait smallint(4) DEFAULT 1 AFTER comment;
