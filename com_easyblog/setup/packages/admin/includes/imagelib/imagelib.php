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

use Foundry\Libraries\Image;

class EasyBlogImagelib
{
	public function __construct()
	{
		$config = EB::config();

		$this->lib = new Image($config->get('main_image_processor'));
	}

	public function __call($method, $arguments)
	{
		if (method_exists($this, $method)) {
			return call_user_func_array([$this, $method],$arguments);
		}


		// Fallback to the library
		return call_user_func_array([$this->lib, $method],$arguments);
	}

	/**
	 * Allows caller to insert the preset watermark configured in the settings
	 *
	 * @since	6.0.0
	 * @access	public
	 */
	public function insertWatermark()
	{
		if (!EB::hasImageWatermark()) {
			return false;
		}

		$config = EB::config();
		$file = JPATH_ROOT . EB::getLogoOverridePath('watermark');
		$position = $config->get('image_watermark_position', 'bottom-right');

		return $this->lib->insert($file, $position);
	}
}
