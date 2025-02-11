/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

CREATE TABLE IF NOT EXISTS `#__easyblog_feeds` (
	`id` bigint(20) NOT NULL auto_increment,
	`title` text NOT NULL,
	`url` text NOT NULL,
	`interval` int(11) NOT NULL,
	`cron` tinyint(3) NOT NULL,
	`item_creator` int(11) NOT NULL,
	`item_team` int(11) NOT NULL,
	`item_category` bigint(20) NOT NULL,
	`item_frontpage` tinyint(3) NOT NULL,
	`item_published` tinyint(3) NOT NULL,
	`item_content` varchar(25) NOT NULL,
	`item_get_fulltext` tinyint(3) default '0' NOT NULL,
	`author` tinyint(3) NOT NULL,
	`params` text NOT NULL,
	`published` tinyint(3) NOT NULL,
	`created` datetime NOT NULL,
	`last_import` datetime NOT NULL,
	`flag` tinyint(3) default '0',
	`language` text NULL,
	PRIMARY KEY  (`id`),
	KEY `cron` (`cron`),
	KEY `item_creator` (`item_creator`),
	KEY `author` (`author`),
	KEY `item_frontpage` (`item_frontpage`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_feeds_history` (
	`id` bigint(20) NOT NULL auto_increment,
	`feed_id` bigint(20) NOT NULL,
	`post_id` int(11) NOT NULL,
	`uid` text NOT NULL,
	`created` datetime NOT NULL,
	`params` text NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `feed_post_id` (`feed_id`,`post_id`),
	KEY `feed_uids` (`feed_id`, `uid` (180) ),
	KEY `idx_postid` (`post_id`)
) DEFAULT CHARSET=utf8mb4;
