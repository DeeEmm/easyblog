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

// Include EasyBlog's framework
require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

// Load our own config
$config = EB::config();
EB::loadLanguages();

// SH404 related stuffs
global $sh_LANG;
$sefConfig = Sh404sefFactory::getConfig();
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin($lang, $shLangName, $shLangIso, $option);

// If sef is disabled, skip this altogether
if ($dosef == false) {
	return;
}

// Remove common query strings
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

if (!empty($Itemid)) {
	shRemoveFromGETVarsList('Itemid');
}

if (!empty($limit)) {
	shRemoveFromGETVarsList('limit');
}

if (!empty($limitstart) || (isset($limitstart) && $limitstart == '0')) {
	shRemoveFromGETVarsList('limitstart');
}


// start by inserting the menu element title (just an idea, this is not required at all)
$task = isset($task) ? $task : null;
$Itemid = isset($Itemid) ? $Itemid : null;

// Get the component prefix that is configured in SH404
$prefix = shGetComponentPrefix($option);
$prefix = empty($prefix) ? getMenuTitle($option, $task, $Itemid, null, $shLangName) : $prefix;
$prefix = empty($prefix) || $prefix == '/' ? JText::_('COM_EASYBLOG_SH404_DEFAULT_ALIAS') : $prefix;

// Append to the sef title
$title[] = $prefix;

// If view is set, pass the url builders to the view
if (isset($view) && $view) {

	$adapter = __DIR__ . '/' . strtolower($view) . '.php';

	// Probably the view has some custom stuffs to perform.
	if (JFile::exists($adapter)) {
		include($adapter);
	} else {

		// If layout is set, pass the url builders to the view
		$exclude = $view == 'latest';

		if (!$exclude) {
			// Add the view to the list of titles
			$title[] = EBString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($view)));

			if (isset($layout)) {
				$title[] = EBString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($view) . '_LAYOUT_' . strtoupper($layout)));
			}
		}
	}
	shRemoveFromGETVarsList('view');
	shRemoveFromGETVarsList('layout');
}

// it seems like sh404sef require the limit so that it can calculate
// the pagination's page-x segment #2844
if (isset($limitstart) && $limitstart && !isset($limit)) {
	$limit = EB::getViewLimit();
}

// ------------------  standard plugin finalize function - don't change ---------------------------
$string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), (isset($shLangName) ? @$shLangName : null));
// ------------------  standard plugin finalize function - don't change ---------------------------
