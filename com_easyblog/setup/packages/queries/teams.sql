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


CREATE TABLE IF NOT EXISTS `#__easyblog_team` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `alias` varchar(255) NULL,
  `description` TEXT NOT NULL,
  `avatar` varchar(255) NULL,
  `access` tinyint(1) NULL DEFAULT 1,
  `published` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `allow_join` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `idx_viewable_team` (`published`, `access`, `created`),
  KEY `idx_published` (`published`),
  KEY `idx_access` (`access`),
  KEY `idx_created` (`created`)
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__easyblog_team_groups` (
  `team_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  KEY `team_id` (`team_id`),
  KEY `group_id` (`group_id`),
  KEY `idx_team_group` (`team_id`, `group_id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_team_users` (
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `isadmin` tinyint(1) NULL DEFAULT 0,
  KEY `easyblog_team_id` (`team_id`),
  KEY `easyblog_team_userid` (`user_id`),
  KEY `easyblog_team_isadmin` (`team_id`, `user_id`, `isadmin`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_team_post` (
  `team_id` int(11) NOT NULL,
  `post_id` bigint(11) NOT NULL,
  KEY `easyblog_teampost_tid` (`team_id`),
  KEY `easyblog_teampost_pid` (`post_id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_team_request` (
  `id` int(11) NOT NULL auto_increment,
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ispending` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `easyblog_team_request_teamid` (`team_id`),
  KEY `easyblog_team_request_userid` (`user_id`),
  KEY `easyblog_team_request_pending` (`ispending`)
) DEFAULT CHARSET=utf8mb4;
