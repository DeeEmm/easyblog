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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewXmlRpc extends EasyBlogView
{
	/**
	 * Default method
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function display($tpl = null)
	{
		if (!$this->config->get('main_remotepublishing_xmlrpc')) {
			throw EB::exception(JText::_('COM_EASYBLOG_REMOTE_PUBLISHING_DISABLED'), 500);
		}

		$xmlrpc = EB::xmlrpc();

		// Retrieve the xmlrpc server
		$server = $xmlrpc->createServer();

		// Set the encoding
		$server->xml_header('UTF-8');

		// Allow casting to be defined by that actual values passed
		$server->functions_parameters_type = 'phpvals';

		// Set the debug level
		$server->setDebug(1);

		// Start the service
		$server->service();
		exit;
	}
}
