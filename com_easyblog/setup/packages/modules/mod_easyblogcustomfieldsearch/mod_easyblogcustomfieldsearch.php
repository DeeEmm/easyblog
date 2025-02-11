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

// Only show this module in Latest and Single Category View
$layout = $modules->input->get('layout', '', 'cmd');
$view = $modules->input->get('view', '', 'cmd');
$allowedViews = array('latest', 'categories');
$my = EB::user();

if (!in_array($view, $allowedViews) || ($view == 'categories') && $layout != 'listings') {
	return;
}

// Get the helper
$helper = new modCustomFieldHelper($modules);

// @5.1
// Backward compatibility
$config = $modules->config;

// See whether this is include or exclude mode
$filterMode = $params->get('filtermode', 'include');
$strictMode = $params->get('strictmode', false);

// Get other params
$limit = $params->get('optionscount', 0);
$submitOnClick = $params->get('submitonclick', false, 'bool');
$catinclusion = $params->get('catinclusion', '');

if ($catinclusion) {
	$catinclusion = implode(',', $catinclusion);
}

$groupId = $params->get('fieldgroup');
$group = EB::table('FieldGroup');
$group->load($groupId);

$sorting = $params->get('sorting', 'title');

$catid = 0;

if ($view == 'categories' && $layout == 'listings') {
	$catid = $modules->input->get('id', 0, 'int');
}

$hasSavedFilters = EB::model('fields')->getSavedFilter($catid);
$fields = $helper->getCustomFields(array('groupId' => $groupId, 'sorting' => $sorting, 'catId' => $catid));

// if there is no fields return. do not show this module.
if (!$fields) {
	return;
}

require($modules->getLayout());
