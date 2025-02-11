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

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
	return;
}

require_once($engine);
require_once(__DIR__ . '/helper.php');

if (!EB::isFoundryEnabled()) {
	return;
}

$modules = EB::modules($module);

$helper = new modEasyBlogLatestCommentHelper($modules);

$config = $modules->config;

$jCommentFile = JPATH_ROOT . '/components/com_jcomments/jcomments.php';

// Use jComment if the component exists.
if ($config->get('comment_jcomments') && JFile::exists($jCommentFile)) {
	$comments = $helper->getJComment();
} else {
	$comments = $helper->getLatestComment();
}

$maxCharacter = $params->get('maxcommenttext', 100);

require($modules->getLayout());
