#----- First line of this file .... please let it here, first with NO carriage return before nor after. -----

#--#Create Activity Table
CREATE TABLE IF NOT EXISTS `activity` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `description` varchar(255) character set UTF8 default NULL,
  `DE` VARCHAR(255) character set UTF8 default NULL,
  `EN` VARCHAR(255) character set UTF8 default NULL,
  `ES` VARCHAR(255) character set UTF8 default NULL,
  `FR` VARCHAR(255) character set UTF8 default NULL,
  `IT` VARCHAR(255) character set UTF8 default NULL,
  `RU` VARCHAR(255) character set UTF8 default NULL,
  `ZH_CN` VARCHAR(255) character set UTF8 default NULL,
  `ZH_TW` VARCHAR(255) character set UTF8 default NULL,
  `activity` varchar(255) character set UTF8 default NULL,
  `created_at` datetime default NOW(),
  `updated_at` datetime default NOW(),
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Permissions Table
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `permission` varchar(255) character set UTF8 default NULL,
  `description` text character set UTF8,
  `auto_has` varchar(255) character set UTF8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Projects Table
CREATE TABLE IF NOT EXISTS `projects` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(255) character set UTF8 default NULL,
  `status` tinyint(2) default '1',
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `default_assignee` bigint(20)  default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Projects Issues Table
CREATE TABLE IF NOT EXISTS  `projects_issues` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` bigint(20) NOT NULL DEFAULT '1',
  `closed_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  `assigned_to` bigint(20)  default '1',
  `project_id` bigint(20) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  `weight` bigint(20) NOT NULL DEFAULT '1',
  `title` varchar(255) DEFAULT NULL,
  `body` text,
  `created_at` datetime DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `duration` smallint(3) NOT NULL DEFAULT '30',
  `temps_plan` smallint(4) DEFAULT 30,
  `temps_fact` smallint(4) DEFAULT 30,
  `temps_paye` smallint(4) DEFAULT 30,
  `updated_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT = 2 ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Projects Issues Attachments Table
CREATE TABLE IF NOT EXISTS  `projects_issues_attachments` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `issue_id` bigint(20) default NULL,
  `comment_id` bigint(20) default '0',
  `uploaded_by` bigint(20) default NULL,
  `filesize` bigint(20) default NULL,
  `filename` varchar(250) character set UTF8 default NULL,
  `fileextension` varchar(255) character set UTF8 default NULL,
  `upload_token` varchar(100) character set UTF8 default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Projects Issues Comments Table
CREATE TABLE IF NOT EXISTS `projects_issues_comments` (
  `id` bigint(20) NOT NULL auto_increment,
  `created_by` bigint(20) default '0',
  `project_id` bigint(20) default NULL,
  `issue_id` bigint(20) default '0',
  `comment` text character set UTF8,
  `temps_fait` smallint(4) DEFAULT 1,
  `temps_fait_deb` datetime DEFAULT NULL,
  `temps_fait_fin` datetime DEFAULT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create issue-tag relationship table
CREATE TABLE IF NOT EXISTS  `projects_issues_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `issue_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issue_tag` (`issue_id`,`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Projects Links Table
CREATE TABLE IF NOT EXISTS  `projects_links` (
  `id_link` int(11) NOT NULL AUTO_INCREMENT,
  `id_project` int(11) NOT NULL DEFAULT '1',
  `category` enum('dev','git','prod') NOT NULL DEFAULT 'dev',
  `link` varchar(100) NOT NULL,
  `created` date NOT NULL,
  `desactivated` date DEFAULT NULL,
  PRIMARY KEY (`id_link`),
  KEY `id_project_category_desactivated_created` (`id_project`,`category`,`desactivated`,`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Projects Users Table
CREATE TABLE IF NOT EXISTS `projects_users` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) default '0',
  `project_id` bigint(20) default '0',
  `role_id` bigint(20) default '0',
	`created_at` datetime default NULL,
 	`updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Roles Table
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(255) character set UTF8 default NULL,
  `role` varchar(255) character set UTF8 default NULL,
  `description` varchar(255) character set UTF8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Roles Permissions Table
CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `role_id` bigint(11) default NULL,
  `permission_id` bigint(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Sessions Table
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(40) character set UTF8 NOT NULL,
  `last_activity` int(10) NOT NULL,
  `data` text character set UTF8 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(255) character set UTF8 default NULL,
  `value` text character set UTF8,
  `name` varchar(255) character set UTF8 default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create tags table
CREATE TABLE IF NOT EXISTS  `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `DE` VARCHAR(255) character set UTF8 default NULL,
  `EN` VARCHAR(255) character set UTF8 default NULL,
  `ES` VARCHAR(255) character set UTF8 default NULL,
  `FR` VARCHAR(255) character set UTF8 default NULL,
  `IT` VARCHAR(255) character set UTF8 default NULL,
  `RU` VARCHAR(255) character set UTF8 default NULL,
  `ZH_CN` VARCHAR(255) character set UTF8 default NULL,
  `ZH_TW` VARCHAR(255) character set UTF8 default NULL,
  `bgcolor` varchar(50) DEFAULT '#330033',
  `ftcolor` varchar(50) DEFAULT '#FFFFFF',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE 'utf8_general_ci';
#--

#--#Create ToDo Table
CREATE TABLE IF NOT EXISTS `users_todos` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `issue_id` bigint(20) default NULL,
  `user_id` bigint(20) default NULL,
  `status` tinyint(2) default '1',
  `weight` bigint(20) default 1,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `role_id` bigint(20) unsigned NOT NULL default '1',
  `email` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `firstname` varchar(255) default NULL,
  `lastname` varchar(255) default NULL,
  `language` varchar(5) default 'en',
  `preferences` text,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `deleted` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create update_history table
CREATE TABLE IF NOT EXISTS `update_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Footprint` varchar(25) DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `DteRelease` datetime DEFAULT NULL,
  `DteInstall` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create following table
CREATE TABLE IF NOT EXISTS `following` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `project` tinyint(2) NOT NULL DEFAULT 0,
  `attached` tinyint(2) NOT NULL DEFAULT 1,
  `tags` tinyint(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--

#--#Create Users Activity Table
CREATE TABLE IF NOT EXISTS `users_activity` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) default NULL,
  `parent_id` bigint(20) default NULL,
  `item_id` bigint(20) default NULL,
  `action_id` bigint(20) default NULL,
  `type_id` int(11) default NULL,
  `data` text character set UTF8,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
#--


#--#Insert Permisions Data
INSERT IGNORE INTO `permissions` (`id`, `permission`, `description`, `auto_has`) VALUES
	(1, 'issue-view', 'View issues in project assigned to', NULL),
	(2, 'issue-create', 'Create issues in projects assigned to', NULL),
	(3, 'issue-comment', 'Comment in issues in projects assigned to', '1'),
	(4, 'issue-modify', 'Modify issues in projects assigned to', '1'),
	(6, 'administration', 'Administration tools, such as user management and application settings.', NULL),
	(9, 'project-create', 'Create a new project', NULL),
	(10, 'project-modify', 'Modify a project assigned to', NULL),
	(11, 'project-all', 'View, modify all projects and issues', '1,2,3,4');
#--

#--#Insert Roles Data
INSERT IGNORE INTO `roles` (`id`, `name`, `role`, `description`)
VALUES
	(1,'User','user','Only can read the issues in the projects they are assigned to'),
	(2,'Developer','developer','Can update issues in the projects they are assigned to'),
	(3,'Manager','manager','Can update issues in all projects, even if they aren\'t assigned'),
	(4,'Administrator','administrator','Can update all issues in all projects, create users and view administration');
#--

#--#Insert Roles Permissions Data
INSERT IGNORE INTO `roles_permissions` (`id`, `role_id`, `permission_id`) VALUES
	(1, 1, 1),
	(2, 1, 2),
	(3, 1, 3),
	(4, 2, 1),
	(5, 2, 2),
	(6, 2, 3),
	(7, 2, 4),
	(8, 3, 11),
	(9, 3, 1),
	(10, 3, 2),
	(11, 3, 3),
	(12, 3, 4),
	(13, 4, 1),
	(14, 4, 2),
	(15, 4, 3),
	(16, 4, 6),
	(17, 4, 9),
	(18, 4, 10),
	(19, 4, 11),
	(20, 4, 4);
#--

#--#Insert Activity Types
INSERT IGNORE INTO `activity` (`id`, `description`, `EN`,`FR`,`activity`)
VALUES
	(1,'Opened a new issue','Opened a new issue','Nouveau billet créé','create-issue'),
	(2,'Commented on a issue','Commented on a issue','Nouveau commentaire sur un billet','comment'),
	(3,'Closed an issue','Closed an issue','Billet fermé','close-issue'),
	(4,'Reopened an issue','Reopened an issue','Billet rouvert','reopen-issue'),
	(5,'Reassigned an issue','Reassigned an issue','Changement de responsable du billet','reassign-issue'),
	(6,'Updated issue tags','Updated issue tags','Mise à jour des étiquettes','update-issue-tags'),
	(7,'Attached a file to issue','Attached a file to issue','Fichier joint au billet','attached-file'),
	(8,'Move an issue from project A to project B','Move an issue from project A to project B','éplacement d`un billet du projet A vers le projet B',	'ChangeIssue-project'),
	(9,'User starts or stop following issue or project','User starts or stop following issue or project','Un usager a commencé ou cessé de suivre le billet', 'Follow'),
	(10,'Updated an issue','Updated an issue','Mise à jour d`un billet', 'IssueEdit'),
	(11,'Deleted a comment','Deleted a comment','Commentaire supprimé', 'delete_comment'),
	(12,'Edited a comment','Edited a comment','	Commentaire modifié','edit_comment' ),
	(13,'Elapsed time worked on an issue','Elapsed time worked on an issue','Temps de travail d`un ouvrier', 'issue_chrono');
#--

#--#Create default tags : id 10
INSERT INTO `tags` (`id`, `tag`, `bgcolor`, `ftcolor`, `created_at`, `updated_at`, `EN`,`FR`,`ES`) VALUES
(1,	'status:open',				'#c43c35','#FFFFFF',	'2013-11-30 11:23:01',	'2013-11-30 11:23:01','status:open',			'État:ouvert',					'Estado: Aberto'),
(2,	'status:closed',			'#46A546','#FFFFFF',	'2013-11-30 11:23:01',	'2013-11-30 11:23:01','status:closed',			'État:fermé',					'Estado: Cerrado'),
(3,	'type:feature',			'#62cffc','#FFFFFF',	'2013-11-30 11:23:01',	'2013-11-30 11:23:01','type:feature',			'Type: développement',		'Tipo: desarollo'),
(4,	'type:bug',					'#f89406','#FFFFFF',	'2013-11-30 11:23:01',	'2013-11-30 11:23:01','type:bug',				'Type: débogage',				'Tipo: debug'),
(6,	'resolution:won`t fix',	'#812323','#FFFFFF',	'2013-11-30 11:23:01',	'2013-11-30 11:23:01','resolution:won`t fix','Verdict: impossible :(',	'Deicsion: impossible'),
(7,	'resolution:fixed',		'#048383','#FFFFFF',	'2013-11-30 11:23:01',	'2013-11-30 11:23:01','resolution:fixed',		'Verdict: Résolu ! :)',		'Decision: Solucionado'),
(8,	'status:testing',			'#FCC307','#FFFFFF',	'2013-11-30 11:23:01',	'2016-11-30 23:11:01','status:testing',		'État: nous testons',		'Estado: haciendo tests'),
(9,	'status:inProgress',		'#FF6600','#FFFFFF',	'2016-11-10 23:12:01',	'2016-11-10 23:12:01','status:inProgress',	'État: Progressons',			'Estado: progressamos');
#--

#--#Import open/closed states
INSERT INTO projects_issues_tags (issue_id, tag_id, created_at, updated_at)
(
	SELECT id as issue_id, IF(status = 1, 1, 2) as tag_id, NOW(), NOW()
	FROM projects_issues
);
#--


#--#Database updates
INSERT INTO `update_history` (`id`, `Footprint`, `Description`, `DteRelease`, `DteInstall`) VALUES
(1,'-------------------------','Version 1.6.0','2017-05-01',NULL),
(2,'Database update via admin','update_v1-1_1.sql','2017-05-10',NULL),
(3,'Database update via admin','update_v1-2_9.sql','2018-06-01',NULL),
(4,'Database update via admin','update_v1-3_1.sql','2018-07-04',NULL),
(5,'Database update via admin','update_v1-3_2.sql','2018-07-05',NULL),
(6,'Database update via login','update_v1-3_3.sql','2018-09-18',NULL),
(7,'Database update via admin','update_v1-8_3a.sql','2018-10-10',NULL),
(8,'Database update via admin','update_v1-8_4a.sql','2019-02-14',NULL),
(9,'Database update via admin','update_v1-8_4b.sql','2019-05-08',NULL),
(10,'Database update via admin','update_v1-8_4d.sql','2019-07-08',NULL),
(11,'Database update via admin','update_v1-8_5af.sql','2020-04-01',NULL),
(12,'Database update via admin','update_v1-8_5t.sql','2020-05-30',NULL),
(13,'Database update via admin','update_v1-8_6e.sql','2020-08-11',NULL),
(14,'Database update via admin','update_v1-8_7a.sql','2020-10-12',NULL),
(15,'Database update via admin','update_v1-8_7ag.sql','2021-04-03',NULL),
(16,'Database update via admin','update_v1-8_7b.sql','2021-05-04',NULL),
(17,'Database update via admin','update_v1-8_7c.sql','2021-06-24',NULL),
(18,'Database update via admin','update_v1-8_7m.sql','2021-07-17',NULL),
(19,'Database update via admin','update_v1-8_7p.sql','2021-07-20',NULL),
(20,'Database update via admin','update_v1-8_7r.sql','2021-09-01',NULL),
(21,'Database update via admin','update_v1-8_7s.sql','2021-10-01',NULL),
(22,'Database update via admin','update_v1-8_7u.sql','2021-12-31',NULL);


#----- Last line of this file .... Anything bellow this line will be lost. -----

#--###--#Create a first admin user:
##--###--# email = myemail@email.com
##--###--# password = admin
##--#INSERT INTO `users` (`id`, `role_id`, `email`, `password`, `firstname`, `lastname`, `language`, `created_at`, `updated_at`, `deleted`) VALUES
##--#(NULL,	4,	'myemail@email.com',	'XhS.DHsB8wt1o',	'admin',	'admin',	'en',	NOW(),	NOW(),	0);

