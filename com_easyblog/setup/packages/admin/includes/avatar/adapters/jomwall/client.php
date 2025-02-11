<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogAvatarJomWall
{
	public function exists()
	{
		$file = JPATH_ROOT . '/components/com_awdwall/helpers/user.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	public function getAvatar($profile, $fromOpengraph = false)
	{
		if (!$this->exists()) {
			return false;
		}

		$avatar = AwdwallHelperUser::getBigAvatar51($profile->id);;

		return $avatar;
	}

	public function getLink()
	{
		if (!$this->exists()) {
			return false;
		}

		$Itemid = AwdwallHelperUser::getComItemId();
		$link = EBR::_('index.php?option=com_awdwall&view=mywall&wuid='.$this->profile->id.'&Itemid='.$Itemid, false);
		return $link;
	}
}
