<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(dirname(__FILE__)) . '/default/client.php');

class EasyBlogProfileK2 extends EasyBlogProfileDefault
{
	public function exists()
	{
		$file1 = JPATH_ROOT . '/components/com_k2/helpers/route.php';
		$file2 = JPATH_ROOT . '/components/com_k2/helpers/utilities.php';

		if (!JFile::exists($file1) || !JFile::exists($file2)) {
			return false;
		}

		require_once($file1);
		require_once($file2);

		return true;
	}

	/**
	 * Retrieves the profile link
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getLink()
	{
		if (!$this->exists()) {
			return parent::getLink();
		}

		$link = K2HelperRoute::getUserRoute($this->profile->id);

		return $link;
	}
}