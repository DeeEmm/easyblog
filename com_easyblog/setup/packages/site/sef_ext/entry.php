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

if (isset($uid) && $uid) {

	// Get the preview post alias
    $post = EB::post($uid);

	if (isset($layout) && $layout == 'preview') {

		// Add the view to the list of titles
		$title[] = EBString::ucwords(JText::_('COM_EASYBLOG_SH404_ROUTER_' . strtoupper($layout)));

		// port the fix from 5.0 #242
		$title[] = str_replace('.', '-', $uid) . '-' . ucfirst($post->getAlias());
	}

	shRemoveFromGETVarsList('view');
	shRemoveFromGETVarsList('layout');
	shRemoveFromGETVarsList('uid');
}

if (isset($id) && $id) {
	// Get the category alias
    $post = EB::post($id);

    $ebConfig = EB::config();

    if ($ebConfig->get('main_sef') == 'simplecategory') {
    	$title[] = ucfirst($post->getPrimaryCategory()->getAlias());
    }

    if ($ebConfig->get('main_sef') == 'default') {
    	$title[] = JText::_('COM_EASYBLOG_SEF_ENTRY');
    }

	// For entry links, we do not want to include the /Entry/ portion
	$title[] = ucfirst($post->getAlias());

	shRemoveFromGETVarsList('view');
	shRemoveFromGETVarsList('layout');
	shRemoveFromGETVarsList('id');
}
