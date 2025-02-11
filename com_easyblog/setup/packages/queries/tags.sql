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

CREATE TABLE IF NOT EXISTS `#__easyblog_tag` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`created_by` int(11) NOT NULL,
	`title` varchar(255) NOT NULL,
	`alias` varchar(255) NULL,
	`description` TEXT NULL,
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	`status` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`published` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`default` tinyint(1) NOT NULL DEFAULT 0,
	`ordering` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`language` char(7) NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `easyblog_tag_title` (`title` (190)),
	KEY `easyblog_tag_published` (`published`),
	KEY `easyblog_tag_alias` (`alias` (190)),
	KEY `easyblog_tag_query1` (`published`, `id`, `title` (170)),
	KEY `idx_created_by` (`created_by`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_post_tag` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`tag_id` bigint(20) unsigned NOT NULL,
	`post_id` bigint(20) unsigned NOT NULL,
	`isrevision` tinyint(1) DEFAULT NULL,
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (`id`),
	KEY `easyblog_post_tag_tag_id` (`tag_id`),
	KEY `easyblog_post_tag_post_id` (`post_id`),
	KEY `easyblog_post_tagpost_id` (`tag_id`, `post_id`)
) DEFAULT CHARSET=utf8mb4;

