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

// @5.1
// Backward compatibility
$config = EB::config();

// Load up our library
$modules = EB::modules($module, $config->get('main_ratings'));
require_once(__DIR__ . '/helper.php');

$helper = new modRelatedPostHelper($modules);
$view = $modules->input->get('view');
$id = $modules->input->get('id');

// We do not want to display anything other than the entry view.
if ($view != 'entry' || !$id) {
	return;
}

// Some custom properties that the user can define in the back end.
$count = (int) $params->get('count', 5);
if ($count < 0) {
	$count = 5;
}

$posts = $helper->getPosts($id, $count);

if (!$posts) {
	return;
}

$columnCount = $params->get('column', 3);
$layout = $params->get('alignment', 'vertical');
$ratings = $params->get('enableratings', false) ? false : true;

// Get the photo layout option
$photoLayout = $params->get('photo_layout');
$photoSize = $params->get('photo_size', 'medium');

$photoAlignment = $params->get('alignment', 'center');
$photoAlignment = ($photoAlignment == 'default') ? 'center' : $photoAlignment;

require($modules->getLayout());