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

class EasyBlogAltaUserPoints extends EasyBlog
{
	/**
	 * Determines if AUP exists
	 *
	 * @since	5.1.15
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			jimport('joomla.filesystem.file');

			$file = JPATH_ROOT . '/components/com_altauserpoints/helper.php';

			if (!JFile::exists($file)) {
				$exists = false;
				return $exists;
			}
		
			include_once($file);

			$exists = true;
		}

		return $exists;
	}

	/**
	 * Assigns points
	 *
	 * @since	5.1.15
	 * @access	public
	 */
	public function assign($action, $userId)
	{
		if (!$this->exists()) {
			return false;
		}

		$aupId = AltaUserPointsHelper::getAnyUserReferreID($userId);

		if ($aupId) {
			return AltaUserPointsHelper::newpoints($action, $aupId);
		}
		
		return false;
	}
}
