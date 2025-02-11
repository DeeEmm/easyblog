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

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerCaptcha extends EasyBlogController
{
	/**
	 * Generates the captcha image
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function generate()
	{
		$id = $this->input->get('id', '', 'int');

		// Load up the captcha object
		$captcha = EB::table('Captcha');

		// Clear outdated keys
		$captcha->clear();

		// load the captcha records.
		$captcha->load($id);

		if (!$captcha->id) {
			return false;
		}

        if (ob_get_length() !== false) {
            while (@ob_end_clean());

            if (function_exists('ob_clean')) {
                @ob_clean();
            }
        }

		// @task: Generate a very random integer and take only 5 chars max.
		$hash = EBString::substr(md5(rand(0, 9999)), 0, 5);
	    $captcha->response = $hash;
		$captcha->store();

	    // Captcha width and height
	    $width = 100;
	    $height = 20;

	    $image = ImageCreate($width, $height);
	    $white = ImageColorAllocate($image, 255, 255, 255);
	    $black = ImageColorAllocate($image, 0, 0, 0);
	    $gray = ImageColorAllocate($image, 204, 204, 204);

	    ImageFill($image, 0, 0, $white);
		ImageString($image, 5, 30, 3, $hash, $black);
		ImageRectangle($image, 0, 0, $width - 1, $height - 1, $gray);
		imageline($image, 0, $height / 2, $width, $height / 2, $gray);
		imageline($image, $width / 2, 0, $width / 2, $height, $gray);

		header('Content-type: image/jpeg');
	    ImageJpeg($image);
	    ImageDestroy($image);
	    exit;
	}
}
