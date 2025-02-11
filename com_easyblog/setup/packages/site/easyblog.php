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


// Dependencies
require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

// check if foundry enabled or not.
if (!EB::isFoundryEnabled()) {
	return EB::raiseWarning(404, 'Please ensure that the plugin <b>Foundry by Stackideas</b> is enabled on the site. Please contact site administrator regarding this matter.');
}

require_once(__DIR__ . '/router.php');
require_once(__DIR__ . '/controllers/controller.php');

// Check for environment changes
EB::checkEnvironment();
EB::initFoundry();

EB::ajax()->process();

// Load other services
EB::loadLanguages();
EB::loadServices();

require_once(EB_ROOT . '/views/views.php');

// Get controller name if specified
$app = JFactory::getApplication();
$controllerName	= $app->input->get('controller', 'easyblog', 'cmd');

$controller = JControllerLegacy::getInstance('easyblog');
$task = $app->input->get('task');

$controller->execute($task);
$controller->redirect();
