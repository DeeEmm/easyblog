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

$count = (int) trim($params->get('count', 0));
if ($count < 0) {
	$count = 5;
}

$model = EB::model('Blog');

$type = '';
$cid = $params->get('catid', '');

if (!empty($cid)) {
	$type = 'category';

	// Normalize the categories to be an array
	if (!is_array($cid)) {
		$cid = array($cid);
	}
}

$disabled = $params->get('enableratings') ? false : true;
$retrievalDuration = $params->get('postduration', 0);

$posts = $model->getBlogsBy($type, $cid, 'popular', $count, EBLOG_FILTER_PUBLISHED, null, false, array(), false, false, true, array(), array(), null, 'listlength', true, array(), array(), false, array(), array('retrievalDuration' => $retrievalDuration, 'paginationType' => 'none'));

// if no records found, stop here. #1589
if (!$posts) {
	return;
}

$posts = $modules->processItems($posts);
$textcount = $params->get('textcount', 150);
$layout = $params->get('module_layout', 'vertical');

// EasyBlog 5.0.x backward compatible fixes
if (!in_array($layout, array('vertical', 'horizontal'))) {
	$layout = 'vertical';
}

$columnCount = $params->get('column');

// Get the photo layout option
$photoLayout = $modules->getCoverLayout();
$photoLayout = $params->get('photo_layout');
$photoSize = $params->get('photo_size', 'medium');

// Get the photo layout alignment
$photoAlignment = $modules->getCoverAlignment();

require($modules->getLayout());
