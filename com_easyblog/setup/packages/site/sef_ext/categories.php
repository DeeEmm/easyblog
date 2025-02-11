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

$addView = true;
$addAlias = true;

if ($Itemid) {
	$activeMenu = JFactory::getApplication()->getMenu('site')->getItem($Itemid);

	if ($activeMenu && $activeMenu->query['view'] == 'categories') {
		$addView = false;

		if (isset($activeMenu->query['id']) && $activeMenu->query['id'] && isset($id) && $id && $activeMenu->query['id'] == $id) {
			$addAlias = false;
		}
	}
}

if (isset($id) && $id) {
	// Get the category alias
	$category = EB::table('Category');
	$category->load($id);

	// Add the view to the list of titles
	if ($addView) {
		$title[] = EBString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($view)));
	}

	if ($addAlias) {

		$alias = $category->getAlias();
		$alias = str_replace(':', '-', $alias);

		$title[] = ucfirst($alias);
	}

	shRemoveFromGETVarsList('view');
	shRemoveFromGETVarsList('layout');
	shRemoveFromGETVarsList('id');

	// override the limit
	if (isset($limitstart) && $limitstart && !isset($limit)) {
		$limit = EB::getViewLimit('category_posts_limit', 'category');
	}
}

if (!isset($id)) {

	// override the limit
	if (isset($limitstart) && $limitstart && !isset($limit)) {
		$limit = EB::getViewLimit('categories_limit', 'categories');
	}

	// Add the view to the list of titles
	if ($addView) {
		$title[] = EBString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($view)));
	}

	shRemoveFromGETVarsList('view');
}
