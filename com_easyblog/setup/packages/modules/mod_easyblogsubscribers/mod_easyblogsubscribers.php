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

// Load up our library
$modules = EB::modules($module);

$option = $modules->input->get('option', '', 'cmd');
$view = $modules->input->get('view', '', 'cmd');
$id = $modules->input->get('id', 0, 'int');

// Get subscription type
$type = $params->get('subscription_type', 'site');

if ($type != 'site') {

	// Allowed views
	$allowed = array('entry', 'categories', 'blogger', 'teamblog');

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
}

// Get a list of subscribers
$model = EB::model('Subscription');
$subscribers = $model->getSubscribers($type, $id);

// Determines if the current user is subscribed
$subscribed = false;
$my = JFactory::getuser();

// Compile the return url
$return = base64_encode(EBFactory::getURI(true));

if (!$my->guest) {
    $subscription = EB::table('Subscriptions');
    $exists = $subscription->load(array('uid' => $id, 'utype' => $type, 'user_id' => $my->id));

    if ($exists) {
        $subscribed = $subscription->id;
    }
}

require($modules->getLayout());
