/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

CREATE TABLE IF NOT EXISTS `#__easyblog_reactions` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`type` varchar(255) NOT NULL,
	`published` tinyint(1) NOT NULL DEFAULT '1',
	`created` datetime DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__easyblog_reactions_history` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`post_id` bigint(20) NOT NULL,
	`reaction_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`token_id` varchar(255) NOT NULL,
	`created` datetime DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	KEY `post_id` (`post_id`,`reaction_id`),
	INDEX (`user_id`),
	INDEX (`token_id` (190))
) DEFAULT CHARSET=utf8mb4;
