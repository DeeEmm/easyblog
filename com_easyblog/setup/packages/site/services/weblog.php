<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Add RSD headers
$doc = JFactory::getDocument();
$config = EB::config();

if (!$config->get('main_remotepublishing_xmlrpc')) {
	return;
}

if ($doc->getType() == 'html') {

	$liveWriterLink = rtrim(JURI::root(), '/') . '/components/com_easyblog/wlwmanifest.xml';

	$rsdLink = EBR::getRoutedURL('index.php?option=com_easyblog&view=rsd&tmpl=component', false, true);

	$doc->addHeadLink($rsdLink, 'EditURI', 'rel', array('type' => 'application/rsd+xml'));
	$doc->addHeadLink($liveWriterLink, 'wlwmanifest', 'rel', array('type' => 'application/wlwmanifest+xml'));
}
