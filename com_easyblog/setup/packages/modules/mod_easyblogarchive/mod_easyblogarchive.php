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

// Filter by all post of archive post
$filterType = $params->get('filterType', 'normal');

$model = EB::model('Archive');
$year = $model->getArchiveMinMaxYear($filterType);

if (!$year) {
	return;
}

$currentMonth = (int) EB::date()->format('m');
$currentYear = (int) EB::date()->format('Y');

$count = $params->get('count', 0);

if (!empty($count)) {
	if (($year['maxyear'] - $year['minyear']) > $count) {
		$year['minyear'] = $year['maxyear'] - $count;
	}
}

// Set default year
$defaultYear = $modules->input->get('archiveyear', $year['maxyear'], 'REQUEST');

// Set default month
$defaultMonth = $modules->input->get('archivemonth', 0, 'REQUEST');

$menuitemid	= $params->get('menuitemid', '');
$menuitemid	= (!empty($menuitemid)) ? '&Itemid=' . $menuitemid : '';

$showEmptyMonth= $params->get('showempty', 1);
$showEmptyYear = $params->get('showemptyyear', false);

// Get excluded/included categories
$excludeCats = $params->get('excatid', array());
$includeCats = $params->get('catid', array());

$includeCats = EB::getCategoryInclusion($includeCats);

$catUrl = '';

if (is_array($includeCats)) {
	foreach ($includeCats as $includeCat) {
		$catUrl .= '&category[]='.$includeCat;
	}
}

// Default filter
$filter = $params->get('filter', '');
$filterId = '';

// Get any available filter
if ($filter == 'blogger') {
	$filterId = $params->get('bloggerId', '');
} else {
	$filterId = $params->get('teamId', '');
}

$postCounts	= $model->getArchivePostCounts($year['minyear'], $year['maxyear'], $excludeCats, $includeCats, $filter, $filterId, $filterType);

$filterUrl = '';

if ($filterType == 'archives') {
	$filterUrl = '&archives=1';
}

require($modules->getLayout());
