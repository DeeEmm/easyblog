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

CREATE TABLE IF NOT EXISTS `#__easyblog_post` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `created_by` bigint(20) unsigned NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NULL default '0000-00-00 00:00:00',
  `title` text NOT NULL,
  `permalink` text NOT NULL,
  `content` longtext NOT NULL,
  `intro` longtext NOT NULL,
  `excerpt` text NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `state` int(1) unsigned NOT NULL,
  `publish_up` datetime NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NULL default '0000-00-00 00:00:00',
  `autopost_date` datetime NULL default '0000-00-00 00:00:00',
  `ordering` int(11) unsigned NOT NULL DEFAULT 0,
  `vote` int(11) unsigned NOT NULL default 0,
  `hits` int(11) unsigned NOT NULL default 0,
  `access` int(11) unsigned NOT NULL default 0,
  `allowcomment` tinyint unsigned NOT NULL default 1,
  `login_to_read` tinyint unsigned NOT NULL DEFAULT 0,
  `subscription` tinyint unsigned NOT NULL default 1,
  `frontpage` tinyint unsigned NOT NULL default 0,
  `isnew` tinyint unsigned NULL DEFAULT 0 COMMENT 'To indicate whether the post is new created or already been edited',
  `blogpassword` varchar(100) NOT NULL DEFAULT '',
  `latitude` VARCHAR(255) NULL,
  `longitude` VARCHAR(255) NULL,
  `address` TEXT NULL,
  `posttype` VARCHAR(255) NOT NULL,
  `robots` TEXT NULL,
  `copyrights` TEXT NULL,
  `image` TEXT NULL,
  `media` LONGTEXT default null,
  `language` CHAR(7) NOT NULL,
  `send_notification_emails` TINYINT( 1 ) NOT NULL DEFAULT '1',
  `locked` TINYINT(3) NOT NULL,
  `revision_id` INT(11) NULL DEFAULT NULL,
  `source_id` bigint(20) default 0,
  `source_type` varchar(64) default '',
  `ip` VARCHAR(255) NOT NULL,
  `doctype` VARCHAR(255) NOT NULL,
  `document` longtext NULL DEFAULT NULL,
  `params` TEXT NOT NULL,
  `author_alias` VARCHAR(255) NULL,
  `version` varchar(10) default '',
  `reautopost` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`),
  KEY `easyblog_post_catid` (`category_id`),
  KEY `easyblog_post_published` (`published`),
  KEY `easyblog_post_created_by` (`created_by`),
  KEY `easyblog_post_blogger_list` (`published`, `id`, `created_by`),
  KEY `easyblog_post_searchnew` (`access`, `published`, `created`),
  KEY `easyblog_frontpage1` (`published`,`frontpage`,`created`),
  KEY `idx_pre_soucetype_postcount` (`published`, `state`, `source_type`, `source_id`),
  KEY `idx_post_sql1` (`published`, `state`, `source_type`, `source_id`, `created`),
  KEY `idx_post_revision` (`revision_id`),
  KEY `idx_permalink` (`permalink` (190)),
  KEY `idx_publishdown_posts` (`publish_down`, `published`, `state`),
  KEY `idx_latest_posts` (`published`, `state`, `access`, `frontpage`, `created`),
  FULLTEXT KEY `idx_post_title` (`title`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_post_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `primary` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `eb_post_category_postid` (`post_id`),
  KEY `eb_post_category_catid` (`category_id`),
  KEY `eb_post_category_post_cat` (`post_id`,`category_id`),
  KEY `eb_post_category_cat_post` (`category_id`, `post_id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `private` int(11) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `autopost` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `repost_autoposting` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `repost_autoposting_interval` tinyint(4) unsigned NOT NULL DEFAULT 0,
  `ordering` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `level` int(11) unsigned DEFAULT '0',
  `lft` int(11) unsigned DEFAULT '0',
  `rgt` int(11) unsigned DEFAULT '0',
  `default` tinyint(1) unsigned DEFAULT '0',
  `theme` varchar(255) NOT NULL,
  `language` char(7) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `easyblog_cat_published` (`published`),
  KEY `easyblog_cat_parentid` (`parent_id`),
  KEY `easyblog_cat_private` (`private`),
  KEY `easyblog_cat_lft` (`lft`),
  KEY `idx_category_access` (`published`, `parent_id`, `private`, `lft`),
  KEY `idx_category_alias` (`alias` (190)),
  KEY `idx_category_alias_id` (`alias` (180), `id`),
  KEY `idx_cat_lftrgt` (`lft`, `rgt`),
  KEY `idx_author` (`created_by`),
  KEY `idx_re_autopost` (`repost_autoposting`, `repost_autoposting_interval`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_category_acl` (
  `id` bigint(20) NOT NULL auto_increment,
  `category_id` bigint(20) NOT NULL,
  `acl_id` bigint(20) NOT NULL,
  `acl_type` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `status` tinyint(1) default 0,
  PRIMARY KEY  (`id`),
  KEY `easyblog_category_acl` (`category_id`),
  KEY `easyblog_category_acl_id` (`acl_id`),
  KEY `easyblog_content_type` (`content_id`, `type` (190)),
  KEY `easyblog_category_content_type` (`category_id`, `content_id`, `type` (190)),
  KEY `idx_cat_post_acl` (`category_id`, `acl_id`, `content_id`)
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__easyblog_featured` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `content_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `easyblog_featured_content_type` (`content_id`,`type` (190)),
  KEY `easyblog_content` (`content_id`),
  KEY `idx_featured_created` (`created`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `sessionid` varchar(200) NOT NULL,
  `value` int(11) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `published` tinyint(3) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`uid`),
  KEY `created_by` (`created_by`),
  KEY `rating` (`value`),
  KEY `idx_uid_type` (`uid`, `type` (64), `value`),
  KEY `idx_uid_type_user` (`uid`, `type` (64), `created_by`),
  KEY `idx_uid_type_user_session` (`created_by`, `sessionid` (170)),
  KEY `idx_uid_type_user_ipaddr` (`uid`, `type` (64), `created_by`, `ip`)
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__easyblog_autoarticle_map` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `post_id` bigint(20) unsigned NOT NULL,
  `content_id` bigint(20) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `autoarticle_map_post_id` (`post_id`),
  KEY `autoarticle_map_content_id` (`content_id`)
) DEFAULT CHARSET=utf8mb4 ;

CREATE TABLE IF NOT EXISTS `#__easyblog_hashkeys` (
  `id` bigint(11) NOT NULL auto_increment,
  `uid` bigint(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `key` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `type` (`type` (190))
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__easyblog_revisions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `content` longtext NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_postid` (`post_id`),
  KEY `idx_ordering` (`post_id`, `ordering`),
  KEY `idx_revision_state` (`post_id`, `state`),
  KEY `idx_state` (`state`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_composer_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(255) NOT NULL,
  `element` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `keywords` text NOT NULL,
  `published` tinyint(3) NOT NULL,
  `created` datetime DEFAULT NULL,
  `ordering` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`),
  KEY `idx_ordering` (`ordering`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_composer_block_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `global` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_published` (`published`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_composer_list` (`user_id`, `global`, `published`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_post_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `created` datetime NOT NULL,
  `system` tinyint(1) NOT NULL,
  `core` tinyint(1) NULL DEFAULT '0',
  `params` longtext DEFAULT NULL,
  `screenshot` text NOT NULL,
  `published` tinyint(3) default 1,
  `datafix` tinyint(1) default 1,
  `doctype` varchar(255) NOT NULL default 'ebd',
  `ordering` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_post_rejected` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(20) unsigned NOT NULL,
  `created_by` int(11) NOT NULL,
  `message` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `draft_id` (`post_id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_post_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`post_id`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_associations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_postid` (`post_id`),
  KEY `idx_key` (`key` (190))
) DEFAULT CHARSET=utf8mb4;
