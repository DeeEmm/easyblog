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

CREATE TABLE IF NOT EXISTS `#__easyblog_comment` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `post_id` bigint(20) unsigned NOT NULL,
  `comment` text NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) NULL DEFAULT '',
  `url` varchar(255) NULL DEFAULT '',
  `ip` varchar(255) NULL DEFAULT '',
  `created_by` bigint(20) unsigned NULL DEFAULT 0,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `publish_up` datetime NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NULL default '0000-00-00 00:00:00',
  `ordering` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `vote` int(11) unsigned NOT NULL default 0,
  `hits` int(11) unsigned NOT NULL default 0,
  `sent` TINYINT(1) DEFAULT 0 NULL,
  `parent_id` int(11) unsigned NULL default 0,
  `lft` int(11) unsigned NOT NULL default 0,
  `rgt` int(11) unsigned NOT NULL default 0,
  PRIMARY KEY  (`id`),
  KEY `easyblog_comment_postid` (`post_id`),
  KEY `easyblog_comment_parent_id` (`parent_id`),
  KEY `idx_comment_createdby` (`created_by`),
  KEY `idx_comment_post_items` (`post_id`, `published`, `rgt`)
) DEFAULT CHARSET=utf8mb4;



CREATE TABLE IF NOT EXISTS `#__easyblog_captcha` (
  `id` int(11) NOT NULL auto_increment,
  `response` varchar(5) NOT NULL DEFAULT '',
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8mb4;
