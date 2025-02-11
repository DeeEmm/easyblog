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

class EasyBlogViewLogin extends EasyBlogView
{
	public function display($tmpl = null)
	{
		// If user is already logged in, just redirect them
		if (!$this->my->guest) {
			$this->info->set(JText::_('COM_EASYBLOG_YOU_ARE_ALREADY_LOGIN'), 'error');

			return $this->app->redirect(EBR::_('index.php?option=com_easyblog'));
		}

		// Determines if there's any return url
		$return = $this->input->get('return', '', 'BASE64');

		if (empty($return)) {

			// check whether have configure the login redirection from the login menu setting
			$menu = $this->app->getMenu();
			$activeMenu = $menu->getActive();

			// We need to append the correct Itemid in order for the url to not always pointing to login menu item. #1427
			$itemId = EBR::getItemId('latest');

			// Retrieve the current menu login redirection URL
			if (is_object($activeMenu) && stristr($activeMenu->link, 'view=login') !== false) {

				if (isset($activeMenu->query) && isset($activeMenu->query['loginredirection'])) {
					$itemId = $activeMenu->query['loginredirection'];
				}
			}

			// find the menu type link from the menu item id
			$loginRedirectionMenu = $menu->getItem($itemId);
			$link = $loginRedirectionMenu->link . '&Itemid=' . $loginRedirectionMenu->id;

			$return = base64_encode($link);
		}

		// Set the meta tags for this page
		EB::setMeta(0, META_TYPE_VIEW);

		$this->set('message', 'COM_EASYBLOG_MEMBER_LOGIN_INFO');
		$this->set('return', $return);

		parent::display('login/default');
	}
}
