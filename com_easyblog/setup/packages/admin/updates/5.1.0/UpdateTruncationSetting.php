<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(EBLOG_ADMIN_INCLUDES . '/maintenance/dependencies.php');

class EasyBlogMaintenanceScriptUpdateTruncationSetting extends EasyBlogMaintenanceScript
{
	public static $title = "Update auto truncation setting";
	public static $description = "Update and simplify auto truncation setting.";

	public function main()
	{

		$config = EB::config();


		// check currently what editor admin configured. the setting will based on the editor selected.
		$editor = $config->get('layout_editor', null);

		$needUpdate = $config->get('layout_blogasintrotext', null);

		if (!is_null($needUpdate)) {
			// this is upgrade from 5.0.x to 5.1.x
			$data = $config->toArray();

			if ($editor != 'composer') {
				$data['composer_truncation_enabled'] = $config->get('layout_blogasintrotext', 0);
				$data['composer_truncation_readmore'] = $config->get('layout_respect_readmore', 0);

				//media position
				$data['composer_truncate_image_position'] = $config->get('main_truncate_image_position', 'top');
				$data['composer_truncate_image_limit"'] = 1;

				$data['composer_truncate_audio_position'] = $config->get('main_truncate_audio_position', 'top');
				$data['composer_truncate_audio_limit"'] = 1;

				$data['composer_truncate_video_position'] = $config->get('main_truncate_video_position', 'top');
				$data['composer_truncate_video_limit"'] = 1;

				$data['composer_truncate_gallery_position'] = $config->get('main_truncate_gallery_position', 'top');

			} else {
				$data['composer_truncate_gallery_position'] = 'hidden';

			}

			// now we unset the old key
			unset($data['layout_blogasintrotext']);
			unset($data['layout_respect_readmore']);
			unset($data['main_truncate_image_position']);
			unset($data['main_truncate_audio_position']);
			unset($data['main_truncate_video_position']);
			unset($data['main_truncate_gallery_position']);

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
