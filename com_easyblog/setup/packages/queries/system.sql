/**
* @package  EasyBlog
* @copyright Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


CREATE TABLE IF NOT EXISTS `#__easyblog_configs` (
	`name` varchar(255) NOT NULL,
	`params` TEXT NOT NULL
) DEFAULT CHARSET=utf8mb4 COMMENT = 'Store any configuration in key => params maps';

CREATE TABLE IF NOT EXISTS `#__easyblog_adsense` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` bigint(20) unsigned NOT NULL,
	`code` varchar(255) NOT NULL,
	`published` tinyint(1) NOT NULL DEFAULT '0',
	`display` varchar(255) NOT NULL DEFAULT 'both',
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`)
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__easyblog_migrate_content` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`content_id` bigint(20) unsigned NOT NULL,
	`post_id` bigint(20) unsigned NOT NULL,
	`session_id` varchar(255) NOT NULL,
	`component` varchar(255) NOT NULL DEFAULT 'com_content',
	`filename` varchar(255) NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `content_id` (`content_id`),
	KEY `post_id` (`post_id`),
	KEY `session_id` (`session_id` (190)),
	KEY `component_content` (`content_id`, `component` (180))
) DEFAULT CHARSET=utf8mb4 COMMENT = 'Store migrated joomla content id and map with eblog post id.';

CREATE TABLE IF NOT EXISTS `#__easyblog_acl` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`group` varchar(255) NOT NULL,
	`action` varchar(255) NOT NULL,
	`default` tinyint(1) NOT NULL default '1',
	`description` text NOT NULL,
	`published` tinyint(1) unsigned NOT NULL default '1',
	`ordering` tinyint(1) unsigned NOT NULL default '0',
	PRIMARY KEY  (`id`),
	KEY `easyblog_post_acl_action` (`action` (190)),
	KEY `idx_acl_published` (`published`),
	KEY `idx_acl_published_id` (`published`, `id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_acl_group` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`content_id` bigint(20) unsigned NOT NULL,
	`acl_id` bigint(20) unsigned NOT NULL,
	`status` tinyint(1) NOT NULL,
	`type` varchar(255) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `easyblog_post_acl_content_type` (`content_id`,`type` (180)),
	KEY `easyblog_post_acl` (`acl_id`),
	KEY `acl_grp_acl_type` (`acl_id`, `type` (190))
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_mailq` (
	`id` int(11) NOT NULL auto_increment,
	`mailfrom` varchar(255) NULL,
	`fromname` varchar(255) NULL,
	`recipient` varchar(255) NOT NULL,
	`subject` text NOT NULL,
	`body` text NOT NULL,
	`created` datetime NOT NULL,
	`status` tinyint(1) NOT NULL DEFAULT '0',
	`template` varchar(255) default '',
	`data` LONGTEXT NOT NULL,
	`param` TEXT NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `easyblog_mailq_status` (`status`),
	KEY `idx_mailq_created` (`created`),
	KEY `idx_mailq_statuscreated` (`status`,`created`)
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__easyblog_meta` (
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` VARCHAR( 20 ) NOT NULL ,
	`content_id` INT( 11 ) NOT NULL ,
	`title` TEXT NULL,
	`keywords` TEXT NULL ,
	`description` TEXT NULL,
	`canonical` TEXT NULL,
	`indexing` int(3) NOT NULL DEFAULT '1',
	PRIMARY KEY  (`id`),
	KEY `idx_meta_content_type` (`content_id`,`type`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_likes` (
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` VARCHAR( 20 ) NOT NULL ,
	`content_id` INT( 11 ) NOT NULL ,
		`created_by` bigint(20) unsigned NULL DEFAULT 0,
		`created` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (`id`),
	KEY `easyblog_content_type` (`type`, `content_id`),
	KEY `easyblog_contentid` (`content_id`),
	KEY `easyblog_createdby` (`created_by`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_feedburner` (
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`userid` bigint(20) unsigned NOT NULL,
	`url` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_external_groups` (
	`id` bigint(20) NOT NULL auto_increment,
	`source` text NOT NULL,
	`post_id` bigint(20) NOT NULL,
	`group_id` int(11) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `external_groups_post_id` (`post_id`),
	KEY `external_groups_group_id` (`group_id`),
	KEY `external_groups_posts` (`group_id`, `post_id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_xml_wpdata` (
	`id` bigint(20) NOT NULL auto_increment,
	`session_id` varchar(255) NOT NULL,
	`filename` varchar(255) NOT NULL,
	`post_id` bigint(20) NOT NULL,
	`source` varchar(15) NOT NULL,
	`data` LONGTEXT NOT NULL,
	`comments` LONGTEXT NULL,
	PRIMARY KEY  (`id`),
	KEY `xml_wpdate_session` (`session_id` (190)),
	KEY `xml_wpdate_post_source` (`post_id`, `source`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_acl_filters` (
	`content_id` bigint(20) unsigned NOT NULL,
	`disallow_tags` text NOT NULL,
	`disallow_attributes` text NOT NULL,
	`type` varchar(255) NOT NULL
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_reports` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`obj_id` bigint(20) NOT NULL,
	`obj_type` varchar(255) NOT NULL,
	`reason` text NOT NULL,
	`created_by` int(11) NOT NULL,
	`created` datetime NOT NULL,
	`ip` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	KEY `obj_id` (`obj_id`,`created_by`)
) DEFAULT CHARSET=utf8mb4 ;


CREATE TABLE IF NOT EXISTS `#__easyblog_external` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`source` text NOT NULL,
	`post_id` bigint(20) NOT NULL,
	`uid` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `external_groups_post_id` (`post_id`),
	KEY `external_groups_group_id` (`uid`),
	KEY `external_groups_posts` (`uid`,`post_id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_languages` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	`locale` varchar(255) NOT NULL,
	`updated` datetime NOT NULL,
	`state` tinyint(3) NOT NULL,
	`translator` varchar(255) NOT NULL,
	`progress` int(11) NOT NULL,
	`params` text NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_media` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`filename` varchar(500) NOT NULL,
	`title` varchar(500) NOT NULL,
	`created_by` int(11) NOT NULL,
	`type` varchar(100) NOT NULL,
	`icon` varchar(255) NOT NULL,
	`preview` text NOT NULL,
	`url` text NOT NULL,
	`key` varchar(255) NOT NULL,
	`uri` varchar(255) NOT NULL,
	`place` varchar(255) NOT NULL,
	`parent` varchar(255) NOT NULL,
	`params` longtext NOT NULL,
	`created` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `uri` (`uri` (190)),
	KEY `key` (`key` (190)),
	KEY `idx_url` (`url` (128)),
	KEY `idx_type` (`type`),
	KEY `idx_places` (`place` (32)),
	KEY `idx_cron_optimiser` (`type` (32), `place` (32))
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_optimizer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,  
  `filepath` text NOT NULL,
  `status` tinyint(3) NOT NULL,
  `log` longtext NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `idx_fileurl` (`url` (128)),
  KEY `idx_filestatus` (`url` (128), `status`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_uploader_tmp` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uid` int(11) NOT NULL,
	`type` varchar(255) NOT NULL,
	`path` text NOT NULL,
	`uri` text NOT NULL,
	`raw` text NOT NULL,
	`created` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `uid` (`uid`,`type` (190)),
	KEY `idx_uploader_created` (`created`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_download` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`userid` int(11) NOT NULL,
	`state` tinyint(3) NOT NULL default 0,
	`params` longtext NULL,
	`created` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `idx_userid` (`userid`),
	KEY `idx_state` (`state`),
	KEY `idx_created` (`created`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_themes_overrides` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`file_id` text NOT NULL,
	`notes` text NOT NULL,
	`contents`  LONGTEXT NOT NULL,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `element` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `updated` datetime NOT NULL,
  `state` tinyint(3) NOT NULL,
  `version` varchar(255) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
