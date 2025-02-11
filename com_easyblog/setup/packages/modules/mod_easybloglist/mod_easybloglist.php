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

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
	return;
}

require_once($engine);
require_once(__DIR__ . '/helper.php');

if (!EB::isFoundryEnabled()) {
	return;
}

$modules = EB::modules($module);
$helper = new modEasyBlogListHelper($modules);

// Load up the configuration
$config = $modules->config;
$posts = $helper->getPosts($params);
$selected = null;

if ($modules->input->get('option') == 'com_easyblog' && $modules->input->get('view') == 'entry') {
	$selected = $modules->input->get('id', 0, 'int');
}

// Generate a unique id
$uid = uniqid();

require($modules->getLayout());
