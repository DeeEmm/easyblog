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

class EasyBlogMaintenanceScriptUpdateCaptchaSetting extends EasyBlogMaintenanceScript
{
	public static $title = "Update captcha setting";
	public static $description = "Update and simplify comment captcha setting.";

	public function main()
	{

		$config = EB::config();

		$legacyRecaptcha = $config->get('comment_recaptcha', null);
		$legacyCaptcha = $config->get('comment_captcha', null);

		$new = $config->get('comment_captcha_type', null);

		if (!is_null($legacyRecaptcha) && !is_null($new)) {
			// this is upgrade from 5.0.x to 5.1.x
			$data = $config->toArray();

			if ($legacyRecaptcha) {
				$data['comment_captcha_type'] = 'recaptcha';
			} else if ($legacyCaptcha) {
				$data['comment_captcha_type'] = 'builtin';
			} else {
				$data['comment_captcha_type'] = 'none';
			}

			// now we unset the old key
			unset($data['comment_recaptcha']);
			unset($data['comment_captcha']);


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
