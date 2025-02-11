<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class pkg_foundryInstallerScript 
{
	/**
	 * After the installation, we also want to enable the plugin
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function postflight()
	{
		$db = JFactory::getDBO();

		$query = array();

		$query[] = 'UPDATE ' . $db->quoteName('#__extensions');
		$query[] = 'SET ' . $db->quoteName('enabled') . '=1';
		$query[] = 'WHERE ' . $db->quoteName('element') . '=' . $db->Quote('foundry');
		$query[] = 'AND ' . $db->quoteName('type') . '=' . $db->Quote('plugin');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$db->execute();
	}
}