<?php
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
defined('_JEXEC') or die('Unauthorized Access');

// Determines if we should be processing email on page load.
$config = EB::config();

if ($config->get('main_mailqueueonpageload')) {
    EB::mailer()->dispatch();
}

// Process scheduled posts (Publishing)
EB::scheduler()->publish();

// Process scheduled posts (Unpublishing)
EB::scheduler()->unpublish();

// Process the garbage collector. Remove the records from #__easyblog_uploader_tmp which exceed 120 minutes.
EB::scheduler()->removeTmpFiles();

// Process the garbage collector. Remove BLANK post from from #__easyblog_post which exceed 3 days.
EB::scheduler()->removeBlankPosts();

// Process the facebook's token expiry notification.
EB::oauth()->notifyAdminTokenExpiry();
