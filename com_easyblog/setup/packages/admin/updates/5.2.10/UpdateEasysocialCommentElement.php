<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptUpdateEasysocialCommentElement extends EasyBlogMaintenanceScript
{
	public static $title = 'Update element for comments made in Easyblog' ;
	public static $description = 'Update element from blog.user.null to blog.user.create.';

	public function main()
	{
		if (!EB::easysocial()->exists()) {
			return true;
		}

		$model = ES::model('Apps');

		if (!$model->isAppInstalled('blog', 'user', 'apps')) {
			return true;
		}

		$db = EB::db();
		$query = 'UPDATE `#__social_comments` SET `element` = '
				. $db->Quote('blog.user.create')
				. ' WHERE `element` = ' . $db->Quote('blog.user.null');

		$db->setQuery($query);
		$db->query();

		return true;
	}
}
