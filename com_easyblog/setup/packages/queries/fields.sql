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

CREATE TABLE IF NOT EXISTS `#__easyblog_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `help` text NOT NULL,
  `state` tinyint(3) NOT NULL,
  `required` tinyint(3) NOT NULL,
  `type` varchar(255) NOT NULL,
  `ordering` int(11) NULL default 0,
  `params` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `idx_ordering` (`ordering`),
  KEY `idx_group_ordering` (`group_id`, `ordering`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_fields_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `state` tinyint(3) NOT NULL,
  `read` text NOT NULL,
  `write` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `state` (`state`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_fields_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `class_name` text NULL,
  PRIMARY KEY (`id`),
  KEY `field_id` (`field_id`),
  KEY `post_id` (`post_id`)
) DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `#__easyblog_category_fields_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`,`group_id`),
  KEY `cat_id` (`category_id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_fields_filter` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `cid` bigint(20) unsigned NOT NULL,
  `params` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_cid` (`user_id`,`cid`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_fields_groups_acl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `acl_id` int(11) NOT NULL,
  `acl_type` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4;