<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

if (!EB::isFoundryEnabled()) {
	return;
}

// Load up our library
$modules = EB::modules($module);
$config = EB::config();

// Get necessary input
$option = $modules->input->get('option', '', 'cmd');
$view = $modules->input->get('view', '', 'cmd');
$id = $modules->input->get('id', 0, 'int');

// By default the type will be "site"
$type = 'site';

// Allowed view
$allowed = array('categories', 'blogger', 'teamblog');

if ($params->get('autodetection', true)) {
	if ($option == 'com_easyblog') {
		if (in_array($view, $allowed)) {
			if ($id) {
				if ($view == 'categories') {
					$type = EBLOG_SUBSCRIPTION_CATEGORY;
				}

				if ($view == 'teamblog') {
					$type = EBLOG_SUBSCRIPTION_TEAMBLOG;
				}

				if ($view == 'blogger') {
					$type = EBLOG_SUBSCRIPTION_BLOGGER;
				}
			}
			// If 'id' is not provided this must be 'site' subscription.
		}
		// If 'view' is not inside 'allowed' view this must be 'site' subscription.
	}

	if ($type == 'site') {
		// If 'type' is still 'site' subscription, 'id' must be 0.
		$id = 0;
	}
} else {
	$type = $params->get('subscription_type', 'site');

	// Site type subscription will appear everywhere.
	if ($type != 'site') {

		if ($option != 'com_easyblog') {
			return;
		}

		if (!in_array($view, $allowed)) {
			return;
		}

		if ($view != $type) {
			return;
		}

		if (!$id) {
			return;
		}

		// update the type to the correct constant
		if ($type == 'categories') {
			$type = EBLOG_SUBSCRIPTION_CATEGORY;
		}

		if ($type == 'teamblog') {
			$type = EBLOG_SUBSCRIPTION_TEAMBLOG;
		}
	} else {
		// if subscription type is site, the id must be 0
		$id = 0;
	}
}

// Determines if the current user is subscribed
$subscribed = false;
$my = JFactory::getuser();

// Get return url
$return = base64_encode(EBFactory::getURI(true));

if (!$my->guest) {
	$subscription = EB::table('Subscriptions');
	$exists = $subscription->load(array('uid' => $id, 'utype' => $type, 'user_id' => $my->id));

	// If subscribed, we need to display unsubscribe button instead
	if ($exists) {
		$subscribed = $subscription->id;
	}
}

// generate the unique code for each of the subscribe module
$uid = uniqid();

// Determines if registration should be allowed here
$registration = JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0 ? 0 : $config->get('main_registeronsubscribe');

require($modules->getLayout());
