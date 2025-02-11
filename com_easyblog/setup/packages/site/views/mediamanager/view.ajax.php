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

require_once(EBLOG_ROOT . '/views/views.php');
require_once(EBLOG_LIB . '/mediamanager/mediamanager.php');

class EasyBlogViewMediamanager extends EasyBlogView
{
	public function __construct()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		parent::__construct();
	}

	/**
	 * Retrieves the loadmore contents of a folder
	 *
	 * @since	5.2
	 * @access	public
	 */
	public function loadmore()
	{
		// Get the key to lookup for
		$key = $this->input->getRaw('key');
		$page = $this->input->getRaw('page', 2);
		$query = $this->input->get('query', '', 'string');

		$options = [];
		$options['query'] = $query;

		$manager = EB::mediamanager();
		$result = $manager->getContents($key, $page, $options);

		return $this->ajax->resolve($result);
	}

	/**
	 * Retrieves the contents of a folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function contents()
	{
		// Get the key to lookup for
		$key = $this->input->getRaw('key');

		$query = $this->input->get('query', '', 'string');
		$page = $this->input->get('page', 1, 'int');

		$options = [];
		$options['query'] = $query;

		$manager = EB::mediamanager();
		$result = $manager->getContents($key, $page, $options);

		return $this->ajax->resolve($result);
	}

	/**
	 * Retrieves extended information about a file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function info()
	{
		$key = $this->input->get('key', '', 'raw');
		$currentPostUri = $this->input->get('currentPostUri', '', 'raw');

		$media = EB::mediamanager();
		$result = $media->getInfo($key, false, $currentPostUri);

		if (EB::image()->isWebp($result->meta->filename)) {
			$fallbackUri = $media->getWebpFallbackUri($result->uri);

			$result->meta->fallbackItem = $media->getInfo($fallbackUri, true);
		}

		return $this->ajax->resolve($result);
	}

	/**
	 * Allows caller to update extended details for an item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function updateItemMeta()
	{
		$mediaKey = $this->input->getRaw('key');

		$media = EB::mediamanager();
		$uri = $media->getUri($mediaKey);

		$item = $media->getMediaObject($uri);

		// Ensure that the current user is really allowed to update the data
		if ($item->created_by != $this->my->id && !FH::isSiteAdmin()) {
			return $this->ajax->reject();
		}

		$params = $item->getParams();

		// Get the param's data
		$paramsData = $this->input->get('params', [], 'array');

		// Do not try to save the editorUrl since this is generated via getMediaObject
		$editorUrl = $item->editorUrl;
		unset($item->editorUrl);

		if ($paramsData) {

			// This is unique to the item's title
			if (isset($paramsData['title']) && $paramsData['title']) {
				$item->title = $paramsData['title'];
			}

			foreach ($paramsData as $key => $value) {
				if ($key != 'title') {
					$params->set($key, $value);
				}
			}

			$item->params = $params->toString();

			$item->store();
		}

		$item->editorUrl = $editorUrl;

		// Get the latest media result
		$result = $media->getInfo($mediaKey);

		$imageLib = EB::image();

		if ($imageLib->isWebp($item->filename)) {
			$fallbackUri = $media->getWebpFallbackUri($item->uri);

			$result->meta->fallbackItem = $media->getInfo($fallbackUri, true);
		}

		return $this->ajax->resolve($result);
	}

	/**
	 * Renders a confirmation dialog before deleting items from media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function confirmDelete()
	{
		EB::requireLogin();

		$theme = EB::themes();
		$output = $theme->output('site/composer/media/dialogs/delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the dialog for folder creation
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function createFolderDialog()
	{
		EB::requireLogin();

		$theme = EB::themes();
		$output = $theme->output('site/composer/media/dialogs/create.folder');

		return $this->ajax->resolve($output);
	}

	/**
	 * Creates a new folder on the site
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function createFolder()
	{
		EB::requireLogin();

		$media = EB::mediamanager();

		$key = $this->input->getRaw('key');
		$uri = $media->getUri($key);

		$folder = $this->input->get('folder', '', 'string');
		$folder = $media->createFolder($uri, $folder);

		if ($folder instanceof EasyBlogException) {
			return $this->ajax->reject($folder);
		}

		return $this->ajax->resolve($folder, $folder->file);
	}

	/**
	 * Allows caller to rename a file or a folder
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function rename()
	{
		$key = $this->input->get('key', '', 'raw');
		$media = EB::mediamanager();

		// Get the source
		$source = $media->getUri($key);
		$target = dirname($source) . '/' . $this->input->get('filename', '', 'default');

		// Try to rename the source and target now
		$item = $media->rename($source, $target);

		// Throw errors
		if (!$item) {
			return $this->ajax->reject();
		}

		//, $result->info, $result->folder
		return $this->ajax->resolve($item);
	}

	/**
	 * Allows caller to delete items from their media
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function delete()
	{
		$keys = $this->input->get('keys', [], 'array');
		$media = EB::mediamanager();

		foreach ($keys as $key) {
			$uri = $media->getUri($key);
			$state = $media->delete($uri);

			if ($state instanceof EasyBlogException) {
				return $this->ajax->reject($state);
			}
		}

		return $this->ajax->resolve();
	}

	/**
	 * Allows caller to rebuild a single variation
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function rebuildVariation()
	{
		$name = $this->input->get('name', '', 'cmd');
		$key = $this->input->get('key', '' ,'raw');

		// Get the uri
		$uri = EBMM::getUri($key);

		$media = EB::mediamanager();
		$state = $media->rebuildVariation($uri, $name);

		$info = EBMM::getMedia($uri);

		return $this->ajax->resolve($info);
	}

	/**
	 * Allows caller to delete a user created variation
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function deleteVariation()
	{
		$name = $this->input->get('name', '', 'cmd');
		$key = $this->input->get('key', '' ,'raw');

		$media = EB::mediamanager();

		$uri = $media->getUri($key);
		$state = $media->deleteVariation($uri, $name);

		if ($state instanceof EasyBlogException) {
			return $this->ajax->reject($state);
		}

		// Get the variations list again
		$info = $media->getInfo($uri);

		return $this->ajax->resolve($info);
	}

	/**
	 * Allows caller to create a new variation of an existing image
	 *
	 * @since   5.1
	 * @access  public
	 */
	public function createVariation()
	{
		$name = $this->input->getCmd('name');
		$key = $this->input->getRaw('key');

		$media = EB::mediamanager();
		$uri = $media->getUri($key);

		// Get the width and height
		$width = $this->input->get('width', 0, 'int');
		$height = $this->input->get('height', 0, 'int');

		if ($width == 0 || $height == 0) {
			return $this->ajax->reject('Invalid width or height provided');
		}

		$params = new stdClass();
		$params->width = $width;
		$params->height = $height;

		$item = $media->createVariation($uri, $name, $params);

		if ($item instanceof EasyBlogException) {
			return $this->ajax->reject($item);
		}

		// Response object is intended to also include
		// other properties like status message and status code.
		// Right now it only inclues the media item.
		$info = $media->getInfo($uri);

		return $this->ajax->resolve($info);
	}

	/**
	 * Edits an image by replacing it
	 *
	 * @since   6.0
	 * @access  public
	 */
	public function edit()
	{
		$key = $this->input->getRaw('key');
		$image = $this->input->get('image', '' , 'default');
		$image = EB::image()->convertBase64($image);

		$media = EB::mediamanager();
		$uri = $media->getUri($key);

		// MM should check if the user really has access to upload to the target folder
		$allowed = $media->hasAccess($uri);

		if (!$allowed) {
			die("Not allowed");
		}

		// Create a temporary file
		$jConfig = EB::jconfig();
		$file = $jConfig->get('tmp_path') . '/' . md5(JFactory::getDate()->toSql() . $this->my->id);

 		$state = @file_put_contents($file, $image);

		// Replace the main image file
		$media->replace($uri, $file);

		// Refresh the info
		$info = $media->getInfo($uri);

		$message = JText::_('COM_EB_IMAGE_UPDATED_SUCCESSFULLY');

		return $this->ajax->resolve($info, $message);
	}
}
