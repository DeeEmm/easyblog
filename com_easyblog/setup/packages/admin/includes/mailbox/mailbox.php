<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogMailbox
{
	/**
	 * Imports items from specific adapter
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function import($adapter)
	{
		$file = __DIR__ . '/adapters/' . $adapter . '.php';

		require_once($file);

		$class = 'EasyBlogMailboxAdapter' . ucfirst($adapter);
		$obj = new $class();

		return $obj->execute();
	}

	/**
	 * Test a connection
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function test($server, $port, $service, $ssl, $mailbox, $user, $pass)
	{
		require_once(__DIR__ . '/adapters/lib.php');

		return EasyblogMailboxLibrary::testConnect($server, $port, $service, $ssl, $mailbox, $user, $pass);
	}
}
