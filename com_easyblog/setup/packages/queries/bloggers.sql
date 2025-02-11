/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

CREATE TABLE IF NOT EXISTS `#__easyblog_users` (
  `id` bigint(20) unsigned NOT NULL,
  `nickname` varchar(255) NULL,
  `avatar` varchar(255) NULL,
  `description` text NULL,
  `url` varchar(255) NULL,
  `params` text NULL,
  `published` tinyint(1) NOT NULL default 1,
  `title` varchar( 255 ) NOT NULL,
  `biography` text NULL,
  `permalink` varchar(255) NULL,
  `custom_css` text NULL,
  `ordering` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  KEY `easyblog_users_permalink` (`permalink` (190))
) DEFAULT CHARSET=utf8mb4;