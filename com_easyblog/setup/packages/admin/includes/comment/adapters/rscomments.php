<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogCommentRSComments extends EasyBlog
{
	public function html(EasyBlogPost &$blog)
	{
		jimport('joomla.filesystem.file');

		$file = JPATH_ROOT . '/components/com_rscomments/helpers/rscomments.php';

		if (!JFile::exists($file)) {
			return;
		}

		include_once($file);

		$rsTemplate = RSCommentsHelper::getTemplate();
		$rsComments = RSCommentsHelper::showRSComments('com_easyblog', $blog->id, $rsTemplate);

		$template = EB::themes();
		$template->set('rsComments', $rsComments);
		$output = $template->output('site/comments/rscomments');

		return $output;
	}
}
