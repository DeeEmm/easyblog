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

class EasyBlogProfileKunena extends EasyBlogProfileDefault
{
	/**
	 * Determines if Kunena exists on the site
	 *
	 * @since	5.0.32
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function exists()
	{
		// Check if Kunena API exists
		$file = JPATH_ADMINISTRATOR . '/components/com_kunena/api.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);
		
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
			return false;
		}

		$link = KunenaRoute::_('index.php?option=com_kunena&view=user&userid=' . $this->profile->id, false);

		return $link;
	}
}