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

class EasyBlogProfileDefault extends EasyBlog
{
	public $profile;

	public function __construct(EasyBlogTableProfile $profile)
	{
		parent::__construct();
		
		$this->profile = $profile;
	}

	/**
	 * Retrieves the profile link
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getLink()
	{
		// If profile user is not an author for whatever reason it is, we shouldn't display it as a link
		$isAuthor = EB::isBlogger($this->profile->id);

		if (!$isAuthor) {
			return 'javascript:void(0);';
		}


		$default = EBR::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $this->profile->id);

		return $default;
	}
}