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

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerBase extends EasyBlogController
{
	/**
	 * Method to explicitly retrieve the latest token on demand
	 *
	 * @since	6.0.10
	 * @access	public
	 */
	public function csrf()
	{
		if ($this->doc->getType() == 'ajax') {
			return $this->ajax->resolve(FH::token());
		}

		return FH::token();
	}
}
