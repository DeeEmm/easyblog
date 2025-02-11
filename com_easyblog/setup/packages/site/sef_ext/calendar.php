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

// e.g. /calendar/calendarView/2019/04
// e.g. /calendar/listView/2019/04

$title[] = EBString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($view)));

if (isset($year)) {
	$title[] = $year;
	shRemoveFromGETVarsList('year');
}

if (isset($month)) {
	$title[] = $month;
	shRemoveFromGETVarsList('month');
}

if ((isset($year) || isset($month)) && isset($layout)) {
	$title[] = EBString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($view) . '_LAYOUT_' . strtoupper($layout)));
}
