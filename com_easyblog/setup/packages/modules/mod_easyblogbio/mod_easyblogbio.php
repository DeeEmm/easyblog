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

$option = $modules->input->get('option', '', 'cmd');
$view = $modules->input->get('view', '', 'cmd');
$layout = $modules->input->get('layout', '', 'cmd');

if ($option != 'com_easyblog' || $view != 'entry') {
	return;
}

// Get the current post id and author name
$id = $modules->input->get('id', '', 'int');
$post = EB::post($id);

$blogger = $post->getAuthor();
$biography = $blogger->getBiography(false, true);

// Get the bio character limit
$biolimit = $params->get('biolimit', 100);

require($modules->getLayout());
