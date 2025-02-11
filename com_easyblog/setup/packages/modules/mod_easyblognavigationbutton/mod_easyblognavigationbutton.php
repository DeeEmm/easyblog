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
require_once(__DIR__ . '/helper.php');

if (!EB::isFoundryEnabled()) {
	return;
}

// Load up our library
$modules = EB::modules($module);
$acl = EB::acl();
$config = EB::config();
$theme = EB::themes();
$fd = EB::fd();

$user = JFactory::getUser();
$profile = EB::user();
$guest = $user->guest;

// Build the return url
$return = base64_encode(JURI::getInstance()->toString());

$helper = new EBHelperNavigation();
$subscription = $helper->getSubscriptions();
$allowManage = $helper->isAllowedManage();
$totalPending = $helper->getPendingPosts();
$totalPendingComments = $helper->getPendingComments();
$totalTeamRequests = $helper->getTeamRequests();

require($modules->getLayout());
