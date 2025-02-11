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

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptMoveLogo extends EasyBlogMaintenanceScript
{
	public static $title = 'Move Logo To A New Override Path' ;
	public static $description = 'Moving the override email and schema logo to a new override path.';

	public function main()
	{
		$types = ['email', 'schema'];

		// Get all the templates of the site
		$templates = $this->getAllTemplates();

		foreach ($templates as $template) {
			foreach ($types as $type) {
				$path = JPATH_ROOT . '/templates/' . $template . '/html/com_easyblog/' . $type . '/logo.png';
				$newPath = JPATH_ROOT . '/images/easyblog_override/' . $type;

				if (JFile::exists($path)) {
					// Create its folder if it is not exists
					if (!JFolder::exists($newPath)) {
						JFolder::create($newPath);
					}

					$newFilePath = $newPath . '/logo.png';

					// Now move the logo to the new path
					JFile::move($path, $newFilePath);
				}
			}
		}

		return true;
	}

	/**
	 * Retrieve all the templates of the site
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function getAllTemplates()
	{
		$db = EB::db();
		$query = 'SELECT `template` FROM `#__template_styles`';

		$db->setQuery($query);
		$templates = $db->loadColumn();

		return $templates;
	}
}