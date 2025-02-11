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

class EasyBlogControllerMedia extends EasyBlogController
{
	/**
	 * Handles uploads from media manager.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function upload()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Only allowed users who are allowed to upload images
		if (!$this->acl->get('upload_image')) {
			$this->output(EB::exception('COM_EASYBLOG_NOT_ALLOWED', EASYBLOG_MSG_ERROR));
		}

		// Load up media manager
		$media = EB::mediamanager();

		// Get uri
		$key = $this->input->getRaw('key');
		$uri = $media->getUri($key);

		// get uri for post
		$postUri = null;
		$postkey = $this->input->getRaw('postkey', null);
		if ($postkey) {
			$postUri = $media->getUri($postkey);
		}

		// get the file name.
		$fileName = $this->input->get('name', '', 'default');

		// check if file are archive or not.
		$archiveExts = array('zip', 'rar', '7z', 'tar', 'gzip', 'tar.gz', 'gz', 'zipx');
		$isArchiveFile = false;

		if ($fileName) {
			$ext = JFile::getExt($fileName);
			if (in_array($ext, $archiveExts)) {
				$isArchiveFile = true;
			}
		}

		// Get the file input
		$file = $this->input->files->get('file');

		if ($isArchiveFile) {
			$file = $this->input->files->get('file', null, 'raw');
		}

		// Check if the file is really allowed to be uploaded to the site.
		$state = EB::image()->canUploadFile($file);

		if ($state instanceof Exception) {
			return $this->output($state);
		}

		// MM should check if the user really has access to upload to the target folder
		$allowed = $media->hasAccess($uri);

		if ($allowed instanceof Exception) {
			return $this->output($allowed);
		}

		// Upload the file now
		$adapter = $media->getAdapter($uri);
		$file = $adapter->upload($file, $uri);

		// Throw an error if the upload fails
		if ($file instanceof Exception) {
			return $this->output($file);
		}

		// Response object is intended to also include
		// other properties like status message and status code.
		// Right now it only inclues the media item.
		$response = new stdClass();
		$response->media = $media->getInfo($file->uri, true, $postUri);

		if (EB::image()->isWebp($fileName)) {
			$response->media->meta->fallbackItem = $media->getInfo($file->fallbackItemUri, true, $postUri);
		}

		return $this->output($response);
	}

	/**
	 * Generates the output for uploader
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function output($result)
	{
		// If this is an exception, port it to an array first.
		if ($result instanceof Exception) {

			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
			echo json_encode($result->toArray());
			exit;
		}

		header('Content-type: text/x-json; UTF-8');
		echo json_encode($result, JSON_HEX_TAG);
		exit;
	}

}
