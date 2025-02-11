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
$modules = EB::modules($module, false, false);
require_once(__DIR__ . '/helper.php');

$helper = new modEasyBlogWelcomeHelper();

// Get the current user.
$my = JFactory::getUser();

// Get the return url
$return = $helper->getReturnURL($params);

// Get the blogger object
$author = EB::user($my->id);

// Get available options
$config = EB::config();
$acl = EB::acl();
$hasTwoFactor = $helper->hasTwoFactor();

// Determines if we should allow registration
$usersConfig = JComponentHelper::getParams('com_users');
$allowRegistration = $usersConfig->get('allowUserRegistration');

require($modules->getLayout());
