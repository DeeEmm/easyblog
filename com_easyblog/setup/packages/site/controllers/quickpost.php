<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/controller.php');

class EasyBlogControllerQuickpost extends EasyBlogController
{
	/**
	 * Saves an uploaded webcam picture
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function saveWebcam()
	{
		// Check for request forgeries
		FH::checkToken();

		// Ensure that the user user must be logged into the site
		EB::requireLogin();

		$image = $this->input->get('image', '', 'default');
		$image = imagecreatefrompng($image);

		ob_start();
		imagepng($image, null, 9);
		$contents = ob_get_contents();
		ob_end_clean();

		// Store this in a temporary location
		$fileName = md5(EB::date()->toSql()) . '.png';
		$tmpPath = JPATH_ROOT . '/tmp/' . $fileName;

		JFile::write($tmpPath, $contents);

		$info = @getimagesize($tmpPath);

		// Simulate a file object
		$file = array('name' => $fileName, 'tmp_name' => $tmpPath, 'type' => $info['mime']);

		// Upload this file into the user's webcam folder
		$media = EB::mediamanager();
		$adapter = $media->getAdapter('user:' . $this->my->id . '/webcam');
		$result = $adapter->upload($file, 'user:' . $this->my->id . '/webcam');

		// Try to delete the temporary file
		jimport('joomla.filesystem.file');

		if (JFile::exists($tmpPath)) {
			JFile::delete($tmpPath);
		}
		return $this->ajax->resolve($result);
	}
}
