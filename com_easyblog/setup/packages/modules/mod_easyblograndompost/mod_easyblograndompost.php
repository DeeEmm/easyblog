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


// @5.1
// Backward compatibility
$config = $modules->config;

$count = (int) trim($params->get('total', 0));
if ($count < 0) {
	$count = 5;
}

$model = EB::model('Blog');

$type = '';
$catId = $params->get('catid', '');

// Backward compatibility
$categories = EB::getCategoryInclusion($catId);

if (!empty($categories)) {
	$type = 'category';
}

$posts = $model->getBlogsBy('', '', 'random', $count, EBLOG_FILTER_PUBLISHED, null, true, array(), false, false, true, array(), $categories, '', '', false, array(), array(), false, array(), array('paginationType' => 'none'));

// Module paramters
$posts = $modules->processItems($posts);
$textcount = $params->get('textcount', 150);
$disabled = !$params->get('enableratings', false);
$layout = $params->get('module_layout', 'vertical');

// EasyBlog 5.0.x backward compatible fixes
if (!in_array($layout, array('vertical', 'horizontal'))) {
	$layout = 'vertical';
}

$columnCount = $params->get('column', 4);

// Get the photo layout option
$photoSize = $params->get('photo_size', 'medium');

$photoLayout = $modules->getCoverLayout();
$photoAlignment = $modules->getCoverAlignment();

if (!$posts) {
	return;
}

if ($params->get('increasehits', false)) {
	foreach ($posts as $post) {
		$post->hit();
	}
}

require($modules->getLayout());
