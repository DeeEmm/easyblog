
CREATE TABLE IF NOT EXISTS `#__easyblog_subscriptions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `uid` bigint(20) unsigned NOT NULL,
  `utype` varchar(64) not null,
  `user_id` bigint(20) unsigned NULL DEFAULT '0',
  `fullname` varchar(255) NULL,
  `email` varchar(100) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `easyblog_subscriptions_types` (`uid`,`utype`),
  KEY `easyblog_subscriptions_types_userid` (`uid`,`utype`, `user_id`),
  KEY `easyblog_subscriptions_types_email` (`uid`,`utype`, `email`),
  KEY `easyblog_subscriptions_userid` (`user_id`),
  KEY `easyblog_subscriptions_email` (`email`)
) DEFAULT CHARSET=utf8mb4;
