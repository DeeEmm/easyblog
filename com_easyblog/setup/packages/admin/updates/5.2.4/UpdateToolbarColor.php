<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptUpdateToolbarColor extends EasyBlogMaintenanceScript
{
	public static $title = "Update toolbar active color";
	public static $description = "Update toolbar active color";

	public function main()
	{
		$config = EB::config();

		$activeColor = $config->get('layout_toolbaractivecolor', null);

		if (strtolower($activeColor) == '#ffffff') {
			$newActiveColor = '#5C5C5C';

			// need to check for zink template.
			$theme = $config->get('theme_site');

			if ($theme == 'zinc') {
				$toolbarColor = $config->get('layout_toolbarcolor', '');

				if (strtolower($toolbarColor) == '#e51c23') {
					// zincred
					$newActiveColor = '#E93E44';
				}

				if (strtolower($toolbarColor) == '#303f9f') {
					// zinc blue
					$newActiveColor = '#4F5CAD';
				}

				if (strtolower($toolbarColor) == '#259b24') {
					// zinc green
					$newActiveColor = '#46AA45';
				}
			}

			$config->set('layout_toolbaractivecolor', $newActiveColor);

			$jsonString = $config->toString();

			$table = EB::table('Configs');
			$exists = $table->load(array('name' => 'config'));

			if (!$exists) {
				$table->type = 'config';
			}

			$table->params = $jsonString;
			$table->store();
		}

		return true;
	}
}
