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

CREATE TABLE IF NOT EXISTS `#__easyblog_oauth` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `auto` tinyint(1) NOT NULL,
  `request_token` text NOT NULL,
  `access_token` text NOT NULL,
  `message` text NOT NULL,
  `created` datetime NOT NULL,
  `private` tinyint(4) NOT NULL,
  `params` text NOT NULL,
  `system` tinyint unsigned NULL DEFAULT 0,
  `expires` datetime NULL,
  `notify` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY  (`id`),
  KEY `easyblog_oauth_user_type` (`user_id`, `type` (150)),
  KEY `idx_created` (`created`),
  KEY `idx_expiry_notify` (`type` (150),`notify`, `system`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_oauth_posts` (
  `id` INT( 11 ) NOT NULL auto_increment,
  `oauth_id` INT( 11 ) NOT NULL ,
  `post_id` INT( 11 ) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `modified` DATETIME NOT NULL ,
  `sent` DATETIME NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `easyblog_oauth_posts_ids` (`oauth_id`, `post_id`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_twitter_microblog` (
  `id_str` text NOT NULL,
  `oauth_id` int(11) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `tweet_author` text NOT NULL,
  KEY `post_id` (`post_id`),
  KEY `id_str` (`id_str` (190))
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_oauth_logs` (
  `id` int(11) NOT NULL auto_increment,
  `oauth_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `status` tinyint(3) NOT NULL,
  `created` datetime NOT NULL,
  `response` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_postid` (`post_id`),
  KEY `idx_oauthid` (`oauth_id`),
  KEY `idx_status` (`status`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_oauth_tmp` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(64) NOT NULL,
  `system` tinyint(3) NULL DEFAULT 0,
  `user_id` int(11) NULL DEFAULT 0,
  `created` datetime NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_types` (`type`, `system`),
  KEY `idx_users` (`type`, `system`, `user_id`)
) DEFAULT CHARSET=utf8mb4;
