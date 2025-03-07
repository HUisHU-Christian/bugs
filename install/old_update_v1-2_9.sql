ALTER TABLE projects_issues ADD COLUMN duration smallint(3) NOT NULL DEFAULT '100' AFTER created_at;
ALTER TABLE projects ADD COLUMN default_assignee bigint(20) NULL DEFAULT '1';
ALTER TABLE projects_issues ADD COLUMN weight bigint(20) NULL default '1';

ALTER TABLE projects_issues
CHANGE created_by created_by bigint(20) NOT NULL DEFAULT '1' AFTER id,
CHANGE weight weight bigint(20) NOT NULL DEFAULT '1' AFTER status;

CREATE TABLE projects_links (
  id_link int(11) NOT NULL AUTO_INCREMENT,
  id_project int(11) NOT NULL DEFAULT '1',
  category enum('dev','git','prod') NOT NULL DEFAULT 'dev',
  link varchar(100) NOT NULL,
  created date NOT NULL,
  desactivated date DEFAULT NULL,
  PRIMARY KEY (id_link),
  KEY id_project_category_desactivated_created (id_project,category,desactivated,created)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS users_todos (
  id bigint(20) unsigned NOT NULL auto_increment,
  issue_id bigint(20) default NULL,
  user_id bigint(20) default NULL,
  status tinyint(2) default '1',
  weight bigint(20) default 1,
  created_at datetime default NULL,
  updated_at datetime default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

UPDATE tags SET id = id + 1001 WHERE id > 8;
INSERT INTO tags (id, tag, bgcolor, created_at, updated_at) VALUES (9,	'status:inProgress','#FF6600', NOW(), NOW() ) on duplicate key UPDATE id = id;
UPDATE tags SET id = id - 1000 WHERE id > 9;
