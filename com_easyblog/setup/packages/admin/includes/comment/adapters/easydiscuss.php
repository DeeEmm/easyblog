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

jimport('joomla.filesystem.file');

class EasyBlogCommentEasyDiscuss
{
	public function html(EasyBlogPost &$blog)
	{
		// Determines if the comment plugin is enabled
		$enabled = JPluginHelper::isEnabled('content', 'easydiscuss');

		if (!$enabled) {
			return;
		}

		// Determines if EasyDiscuss exists
		if (!EB::easydiscuss()->exists()) {
			return;
		}

		$articleParams = new stdClass();
		$articleParams->allowComments = $blog->allowComments();
		$this->app = JFactory::getApplication();
		$result = $this->app->triggerEvent('onDisplayComments', array(&$blog, &$articleParams));

		if (isset($result[0]) || isset($result[1])) {

			// There could be komento running on the site
			if (isset($result[1]) && $result[1]) {
				$output = $result[1];
			} else {
				$output = $result[0];
			}

			return $output;
		}

		return;
	}
}
