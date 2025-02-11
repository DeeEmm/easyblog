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

class EasyBlogViewDownload extends EasyBlogView
{
	/**
	 * Display delete info page
	 *
	 * @since   5.2.0
	 * @access  public
	 */
	public function deleteinfo()
	{
		if (!$this->config->get('gdpr_enabled')) {
			throw EB::exception(JText::_('COM_EB_GDPR_DOWNLOAD_DISABLED'), 404);
		}

		// Get the composite keys
		$data = $this->input->get('key', '', 'raw');
		$redirect = EB::_('index.php?option=com_easyblog&view=latest', false);

		if (!$data) {
			throw EB::exception(JText::_('COM_EB_INVALID_TOKEN_PROVIDED'), 404);
		}

		$keys = base64_decode($data);
		$key = explode('|', $keys);

		$userId = $key[0];
		$email = $key[1];

		// okay all passed. lets display a password form to verify the user.
		$this->set('data', $data);
		$this->set('userId', $userId);

		return parent::display('gdpr/delete');
	}
}
