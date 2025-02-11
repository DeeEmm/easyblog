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

class EasyBlogMaintenanceScriptUpdateSefIdSetting extends EasyBlogMaintenanceScript
{
	public static $title = "Update SEF Use IDs setting";
	public static $description = "Update SEF Use IDs setting based on the existing Unicode Aliases configuration.";

	public function main()
	{
		$config = EB::config();
		$unicodeAlias = $config->get('main_sef_unicode', 0);

		if ($unicodeAlias) {
			// here we need to set the sef use IDs setting to true.
			$data = $config->toArray();

			// set useid to true
			$data['main_sef_useid'] = 1;

			$registry = EB::registry($data);

			// now let save the data
			$table = EB::table('Configs');
			$exists = $table->load(array('name' => 'config'));

			if (!$exists) {
				$table->type = 'config';
			}

			$table->params	= $registry->toString('INI');
			$table->store();
		}



		return true;
	}
}
