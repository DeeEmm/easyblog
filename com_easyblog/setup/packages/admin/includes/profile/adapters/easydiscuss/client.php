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

class EasyBlogProfileEasyDiscuss extends EasyBlogProfileDefault
{
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
		if (!EB::easydiscuss()->exists()) {
			return parent::getLink();
		}

		$link = DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&id=' . $this->profile->id, false);

		return $link;
	}
}